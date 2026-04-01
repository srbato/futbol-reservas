<?php

namespace App\Http\Controllers;

use App\Models\MembershipPlan;
use App\Models\VenueAdminSubscription;
use App\Services\MercadoPagoSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VenueAdminMembershipController extends Controller
{
    private const CURRENCY = 'ARS';

    public function show(Request $request)
    {
        $user = $request->user();

        $activeSubscription = $user->activeVenueAdminSubscription()->first();

        $latestSubscription = $user->venueAdminSubscriptions()
            ->latest()
            ->first();

        $planSlug = $request->query('plan');
        $billingCycle = in_array($request->query('billing'), ['monthly', 'annual'])
            ? $request->query('billing')
            : 'monthly';

        if (!$planSlug) {
            $inferredSlug = $activeSubscription?->plan_slug ?? $latestSubscription?->plan_slug;

            if ($inferredSlug) {
                $planSlug = $inferredSlug;
                if (!$request->query('billing') && ($activeSubscription?->billing_cycle ?? $latestSubscription?->billing_cycle)) {
                    $billingCycle = $activeSubscription?->billing_cycle ?? $latestSubscription?->billing_cycle;
                }
            } elseif ($activeSubscription || $latestSubscription) {
                $planSlug = MembershipPlan::where('is_featured', true)->where('is_active', true)->value('slug')
                    ?? MembershipPlan::where('is_active', true)->orderBy('sort_order')->value('slug');
            } else {
                return redirect()->route('planes')
                    ->with('info', 'Seleccioná un plan para continuar.');
            }
        }

        $plan = MembershipPlan::where('slug', $planSlug)
            ->where('is_active', true)
            ->first();

        if (!$plan) {
            return redirect()->route('planes')
                ->with('error', 'El plan seleccionado no está disponible.');
        }

        $subscriptionHistory = $user->venueAdminSubscriptions()
            ->latest()
            ->get();

        $price = $billingCycle === 'annual'
            ? $plan->annualTotalPrice()
            : (float) $plan->monthly_price;

        $trialAvailable = $billingCycle === 'monthly' && $plan->hasTrial() && !$user->venueAdminSubscriptions()
            ->whereNotNull('trial_ends_at')
            ->exists();

        return view('membership.become-partner', compact(
            'plan',
            'billingCycle',
            'price',
            'activeSubscription',
            'latestSubscription',
            'subscriptionHistory',
            'trialAvailable'
        ));
    }

    public function checkout(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'super_admin') {
            return back()->with('error', 'Tu usuario ya tiene acceso total al sistema.');
        }

        $data = $request->validate([
            'plan_slug'     => ['required', 'string', 'exists:membership_plans,slug'],
            'billing_cycle' => ['required', 'in:monthly,annual'],
            'mp_email'      => ['required', 'email'],
        ]);

        $plan = MembershipPlan::where('slug', $data['plan_slug'])
            ->where('is_active', true)
            ->firstOrFail();

        $billingCycle = $data['billing_cycle'];
        $price = $billingCycle === 'annual'
            ? $plan->annualTotalPrice()
            : (float) $plan->monthly_price;

        $referralCode = null;
        $referralCodeInput = trim($request->input('referral_code', ''));
        if ($referralCodeInput) {
            $referralCode = \App\Models\ReferralCode::where('code', strtoupper($referralCodeInput))->first();
            if (!$referralCode) {
                return back()->with('error', 'El código de referido no es válido.');
            }
            if ($referralCode->user_id === $user->id) {
                return back()->with('error', 'No podés usar tu propio código de referido.');
            }
        }

        $activeSubscription = $user->activeVenueAdminSubscription()->first();

        if ($activeSubscription && $activeSubscription->status === 'TRIAL') {
            return back()->with('error', 'Actualmente estás en período de prueba. Esperá a que venza para contratar un plan pago.');
        }

        if ($activeSubscription && $activeSubscription->mp_subscription_status !== 'cancelled') {
            return back()->with('error', 'Ya tenés una suscripción activa con cobro automático. Cancelala antes de contratar una nueva.');
        }

        $pendingSubscription = $user->venueAdminSubscriptions()
            ->where('status', 'PENDING_PAYMENT')
            ->latest()
            ->first();

        if ($pendingSubscription) {
            return back()->with('error', 'Ya tenés una suscripción pendiente de aprobación.');
        }

        // Trial solo si el plan lo tiene Y el usuario nunca usó un trial antes
        $trialDays = 0;
        if ($plan->hasTrial()) {
            $usedTrial = $user->venueAdminSubscriptions()
                ->whereNotNull('trial_ends_at')
                ->exists();

            if (!$usedTrial) {
                $trialDays = (int) $plan->trial_days;
            }
        }

        $subscription = VenueAdminSubscription::create([
            'user_id'            => $user->id,
            'plan_slug'          => $plan->slug,
            'billing_cycle'      => $billingCycle,
            'long_term_months'   => $billingCycle === 'annual' ? $plan->longTermMonths() : 1,
            'status'             => 'PENDING_PAYMENT',
            'monthly_price'      => $price,
            'currency'           => self::CURRENCY,
            'payment_provider'   => 'mercadopago',
            'referral_code_used' => $referralCode?->code,
            'trial_ends_at'      => $trialDays > 0 ? now()->addDays($trialDays) : null,
        ]);

        $baseUrl = rtrim(config('app.url'), '/');

        [$ok, $response] = app(MercadoPagoSubscriptionService::class)->createPreapproval(
            subscription: $subscription,
            userEmail:    $data['mp_email'],
            planName:     $plan->name,
            price:        $price,
            billingCycle: $billingCycle,
            backUrl:      $baseUrl . '/membership/success',
        );

        if (!$ok || !isset($response['init_point'])) {
            Log::error('MP Subscription: sin init_point en preapproval', [
                'subscription_id' => $subscription->id,
                'response'        => $response,
            ]);
            $subscription->delete();

            $mpMessage = $response['message'] ?? '';
            if (str_contains($mpMessage, 'different countries')) {
                $errorMsg = 'Tu cuenta de MercadoPago no es de Argentina. La suscripción requiere una cuenta de MercadoPago Argentina.';
            } elseif (str_contains($mpMessage, 'lower than')) {
                $errorMsg = 'El monto del plan es demasiado bajo para procesar el pago. Contactá al soporte.';
            } else {
                $errorMsg = 'No se pudo iniciar la suscripción con MercadoPago. Por favor, intentá de nuevo.';
            }

            return back()->with('error', $errorMsg);
        }

        $subscription->update([
            'mp_preapproval_id' => $response['id'] ?? null,
        ]);

        return redirect()->away($response['init_point']);
    }

    public function startTrial(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'super_admin') {
            return back()->with('error', 'Tu usuario ya tiene acceso total al sistema.');
        }

        $data = $request->validate([
            'plan_slug' => ['required', 'string', 'exists:membership_plans,slug'],
        ]);

        $plan = MembershipPlan::where('slug', $data['plan_slug'])
            ->where('is_active', true)
            ->firstOrFail();

        if (!$plan->hasTrial()) {
            return back()->with('error', 'Este plan no tiene período de prueba disponible.');
        }

        $usedTrial = $user->venueAdminSubscriptions()
            ->whereNotNull('trial_ends_at')
            ->exists();

        if ($usedTrial) {
            return back()->with('error', 'Ya usaste el período de prueba anteriormente.');
        }

        $activeSubscription = $user->activeVenueAdminSubscription()->first();
        if ($activeSubscription) {
            return back()->with('error', 'Ya tenés una suscripción activa.');
        }

        $trialDays   = (int) $plan->trial_days;
        $trialEndsAt = now()->addDays($trialDays);

        VenueAdminSubscription::create([
            'user_id'          => $user->id,
            'plan_slug'        => $plan->slug,
            'billing_cycle'    => 'monthly',
            'long_term_months' => 1,
            'status'           => 'TRIAL',
            'monthly_price'    => $plan->monthly_price,
            'currency'         => self::CURRENCY,
            'payment_provider' => null,
            'trial_ends_at'    => $trialEndsAt,
            'starts_at'        => now(),
            'expires_at'       => $trialEndsAt,
        ]);

        if ($user->role === 'user') {
            $user->role = 'venue_admin';
            $user->save();
        }

        return redirect()->route('va.dashboard')
            ->with('success', "¡Período de prueba activado! Tenés {$trialDays} días de acceso gratuito al panel admin. Al finalizar, podrás contratar un plan.");
    }

    public function cancelSubscription(Request $request)
    {
        $user = $request->user();

        $subscription = $user->activeVenueAdminSubscription()->first();

        if (!$subscription) {
            return back()->with('error', 'No tenés una suscripción activa para cancelar.');
        }

        if ($subscription->status === 'TRIAL') {
            $subscription->update([
                'status'     => 'CANCELLED',
                'expires_at' => now(),
            ]);

            if ($user->role === 'venue_admin') {
                $user->role = 'user';
                $user->save();
            }

            return redirect()->route('membership.become')
                ->with('success', 'Período de prueba cancelado. Perdiste el acceso al panel admin.');
        }

        if ($subscription->mp_preapproval_id) {
            app(MercadoPagoSubscriptionService::class)->cancelPreapproval($subscription->mp_preapproval_id);
        }

        // Keep status=ACTIVE so user retains access until expires_at
        $subscription->update(['mp_subscription_status' => 'cancelled']);

        $accessUntil = $subscription->expires_at?->format('d/m/Y') ?? 'la fecha de vencimiento';

        return redirect()->route('membership.become')
            ->with('success', "Suscripción cancelada. Seguirás teniendo acceso hasta el {$accessUntil}. No se realizarán más cobros automáticos.");
    }

    public function cancelPending(Request $request)
    {
        $user = $request->user();

        $pending = $user->venueAdminSubscriptions()
            ->where('status', 'PENDING_PAYMENT')
            ->latest()
            ->first();

        if ($pending?->mp_preapproval_id) {
            app(MercadoPagoSubscriptionService::class)->cancelPreapproval($pending->mp_preapproval_id);
        }

        $user->venueAdminSubscriptions()
            ->where('status', 'PENDING_PAYMENT')
            ->update(['status' => 'CANCELLED']);

        return redirect()->route('membership.become')
            ->with('success', 'El intento anterior fue cancelado. Ya podés iniciar una nueva suscripción.');
    }

    public function success()
    {
        return redirect()
            ->route('membership.become')
            ->with('success', 'Suscripción iniciada. En cuanto MercadoPago confirme la aprobación, tu acceso se activará automáticamente.');
    }

    public function pending()
    {
        return redirect()
            ->route('membership.become')
            ->with('success', 'Tu suscripción está pendiente de aprobación.');
    }

    public function failure()
    {
        return redirect()
            ->route('membership.become')
            ->with('error', 'No se pudo completar la suscripción.');
    }
}
