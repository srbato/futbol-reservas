<?php

namespace App\Http\Controllers;

use App\Models\MembershipPlan;
use App\Models\VenueAdminSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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

        // If no plan in URL, try to infer context
        if (!$planSlug) {
            // Use plan from their existing subscription if available
            $inferredSlug = $activeSubscription?->plan_slug ?? $latestSubscription?->plan_slug;

            if ($inferredSlug) {
                $planSlug = $inferredSlug;
                if (!$request->query('billing') && ($activeSubscription?->billing_cycle ?? $latestSubscription?->billing_cycle)) {
                    $billingCycle = $activeSubscription?->billing_cycle ?? $latestSubscription?->billing_cycle;
                }
            } elseif ($activeSubscription || $latestSubscription) {
                // Old subscriber with no plan_slug — default to featured plan
                $planSlug = MembershipPlan::where('is_featured', true)->where('is_active', true)->value('slug')
                    ?? MembershipPlan::where('is_active', true)->orderBy('sort_order')->value('slug');
            } else {
                // Brand new user — must choose a plan first
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

        return view('membership.become-partner', compact(
            'plan',
            'billingCycle',
            'price',
            'activeSubscription',
            'latestSubscription',
            'subscriptionHistory'
        ));
    }

    public function checkout(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'super_admin') {
            return back()->with('error', 'Tu usuario ya tiene acceso total al sistema.');
        }

        $data = $request->validate([
            'plan_slug'    => ['required', 'string', 'exists:membership_plans,slug'],
            'billing_cycle' => ['required', 'in:monthly,annual'],
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

        // Evita múltiples pagos pendientes al mismo tiempo
        $pendingSubscription = $user->venueAdminSubscriptions()
            ->where('status', 'PENDING_PAYMENT')
            ->latest()
            ->first();

        if ($pendingSubscription) {
            return back()->with('error', 'Ya tenés una renovación o membresía pendiente de pago.');
        }

        $subscription = VenueAdminSubscription::create([
            'user_id'             => $user->id,
            'plan_slug'           => $plan->slug,
            'billing_cycle'       => $billingCycle,
            'status'              => 'PENDING_PAYMENT',
            'monthly_price'       => $price,
            'currency'            => self::CURRENCY,
            'payment_provider'    => 'mercadopago',
            'referral_code_used'  => $referralCode?->code,
        ]);

        $baseUrl = rtrim(config('app.url'), '/');
        $externalReference = 'venue_admin_subscription:' . $subscription->id;

        $periodLabel = $billingCycle === 'annual' ? '12 meses' : '1 mes';

        $payload = [
            'items' => [
                [
                    'title'       => "Plan {$plan->name} TuCancha — {$periodLabel}",
                    'quantity'    => 1,
                    'currency_id' => self::CURRENCY,
                    'unit_price'  => (float) $price,
                ]
            ],
            'external_reference' => $externalReference,
            'back_urls' => [
                'success' => $baseUrl . '/membership/success',
                'failure' => $baseUrl . '/membership/failure',
                'pending' => $baseUrl . '/membership/pending',
            ],
        ];

        if (!str_contains($baseUrl, '127.0.0.1') && !str_contains($baseUrl, 'localhost')) {
            $payload['notification_url'] = $baseUrl . '/webhooks/mercadopago';
            $payload['auto_return'] = 'approved';
        }

        $response = Http::withOptions(['verify' => !app()->isLocal()])
            ->withToken(config('services.mercadopago.access_token'))
            ->post('https://api.mercadopago.com/checkout/preferences', $payload);

        if (!$response->successful()) {
            $subscription->update(['status' => 'CANCELLED']);
            Log::error('MercadoPago: error al crear preferencia de membresía', [
                'subscription_id' => $subscription->id,
                'status'          => $response->status(),
            ]);
            return back()->with('error', 'No se pudo iniciar el pago de membresía. Por favor, intentá de nuevo.');
        }

        $data = $response->json();

        if (!isset($data['init_point'])) {
            $subscription->update(['status' => 'CANCELLED']);
            Log::error('MercadoPago: respuesta sin init_point en membresía', [
                'subscription_id' => $subscription->id,
                'response'        => $data,
            ]);
            return back()->with('error', 'Respuesta inesperada de Mercado Pago. Por favor, intentá de nuevo.');
        }

        $subscription->update([
            'mp_preference_id' => $data['id'] ?? null,
        ]);

        return redirect()->away($data['init_point']);
    }

    public function success()
    {
        return redirect()
            ->route('membership.become')
            ->with('success', 'Volviste desde Mercado Pago. Si el pago fue aprobado, tu renovación se procesará automáticamente.');
    }

    public function pending()
    {
        return redirect()
            ->route('membership.become')
            ->with('success', 'Tu pago quedó pendiente.');
    }

    public function failure()
    {
        return redirect()
            ->route('membership.become')
            ->with('error', 'No se pudo completar el pago de la membresía.');
    }
}
