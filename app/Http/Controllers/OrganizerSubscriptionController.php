<?php

namespace App\Http\Controllers;

use App\Services\OrganizerSubscriptionService;
use Illuminate\Http\Request;

class OrganizerSubscriptionController extends Controller
{
    public function plans()
    {
        $user = auth()->user();
        $tier = $user->organizerTier();
        $subscription = $user->activeOrganizerSubscription;
        $plans = \App\Models\OrganizerPlan::where('is_active', true)->orderBy('sort_order')->get();

        return view('organizador.planes', compact('tier', 'subscription', 'plans'));
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'mp_email' => 'required|email',
        ]);

        $service = app(OrganizerSubscriptionService::class);
        [$ok, $result] = $service->subscribe(auth()->user(), $request->mp_email);

        if (!$ok) {
            return back()->with('error', $result);
        }

        // $result is the init_point URL from MercadoPago
        return redirect()->away($result);
    }

    public function cancel()
    {
        $service = app(OrganizerSubscriptionService::class);
        $service->cancel(auth()->user());

        return redirect()->route('organizador.planes')
            ->with('success', 'Suscripcion cancelada.');
    }

    public function success(Request $request)
    {
        $user = auth()->user();

        // Check if already active
        $sub = $user->organizerSubscription()->first();
        if ($sub && $sub->status === 'ACTIVE') {
            return redirect()->route('organizador.planes')
                ->with('success', 'Pago confirmado! Tu plan Pro ya esta activo.');
        }

        // Wait briefly for webhook (up to 2 seconds)
        for ($i = 0; $i < 4; $i++) {
            usleep(500000);
            $sub = $user->organizerSubscription()->first();
            if ($sub && $sub->status === 'ACTIVE') {
                return redirect()->route('organizador.planes')
                    ->with('success', 'Pago confirmado! Tu plan Pro ya esta activo.');
            }
        }

        // Webhook didn't arrive — check MP directly
        if ($sub && $sub->mp_preapproval_id) {
            $token = config('services.mercadopago.access_token');
            $response = \Illuminate\Support\Facades\Http::withOptions(['verify' => app()->isProduction()])
                ->withToken($token)
                ->get("https://api.mercadopago.com/preapproval/{$sub->mp_preapproval_id}");

            if ($response->successful()) {
                $mpStatus = $response->json('status');
                if ($mpStatus === 'authorized') {
                    $sub->update([
                        'status' => 'ACTIVE',
                        'starts_at' => $sub->starts_at ?? now(),
                        'expires_at' => now()->addMonth(),
                    ]);
                    return redirect()->route('organizador.planes')
                        ->with('success', 'Pago confirmado! Tu plan Pro ya esta activo.');
                }
            }
        }

        // Also try from preapproval_id in query params
        $preapprovalId = $request->query('preapproval_id');
        if ($preapprovalId && $sub && !$sub->mp_preapproval_id) {
            $sub->update(['mp_preapproval_id' => $preapprovalId]);

            $token = config('services.mercadopago.access_token');
            $response = \Illuminate\Support\Facades\Http::withOptions(['verify' => app()->isProduction()])
                ->withToken($token)
                ->get("https://api.mercadopago.com/preapproval/{$preapprovalId}");

            if ($response->successful() && $response->json('status') === 'authorized') {
                $sub->update([
                    'status' => 'ACTIVE',
                    'starts_at' => $sub->starts_at ?? now(),
                    'expires_at' => now()->addMonth(),
                ]);
                return redirect()->route('organizador.planes')
                    ->with('success', 'Pago confirmado! Tu plan Pro ya esta activo.');
            }
        }

        return redirect()->route('organizador.planes')
            ->with('info', 'Pago recibido! Tu plan Pro se activara en unos instantes cuando MercadoPago confirme el pago.');
    }
}
