<?php

namespace App\Http\Controllers;

use App\Models\PlatformSetting;
use App\Models\VenueAdminSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VenueAdminMembershipController extends Controller
{
    private const DEFAULT_MONTHLY_PRICE = 29990;
    private const CURRENCY = 'ARS';

    public function show(Request $request)
    {
        $user = $request->user();

        $activeSubscription = $user->activeVenueAdminSubscription()->first();

        $latestSubscription = $user->venueAdminSubscriptions()
            ->latest()
            ->first();

        $subscriptionHistory = $user->venueAdminSubscriptions()
            ->latest()
            ->get();

        $monthlyPrice = $this->getMembershipPrice();

        return view('membership.become-partner', compact(
            'activeSubscription',
            'latestSubscription',
            'subscriptionHistory',
            'monthlyPrice'
        ));
    }

    public function checkout(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'super_admin') {
            return back()->with('error', 'Tu usuario ya tiene acceso total al sistema.');
        }

        // Evita múltiples pagos pendientes al mismo tiempo
        $pendingSubscription = $user->venueAdminSubscriptions()
            ->where('status', 'PENDING_PAYMENT')
            ->latest()
            ->first();

        if ($pendingSubscription) {
            return back()->with('error', 'Ya tenés una renovación o membresía pendiente de pago.');
        }

        $monthlyPrice = $this->getMembershipPrice();

        $subscription = VenueAdminSubscription::create([
            'user_id' => $user->id,
            'status' => 'PENDING_PAYMENT',
            'monthly_price' => $monthlyPrice,
            'currency' => self::CURRENCY,
            'payment_provider' => 'mercadopago',
        ]);

        $baseUrl = rtrim(config('app.url'), '/');
        $externalReference = 'venue_admin_subscription:' . $subscription->id;

        $payload = [
            'items' => [
                [
                    'title' => 'Membresía socio TuCancha',
                    'quantity' => 1,
                    'currency_id' => self::CURRENCY,
                    'unit_price' => (float) $monthlyPrice,
                ]
            ],
            'external_reference' => $externalReference,
            'back_urls' => [
                'success' => $baseUrl . '/membership/success',
                'failure' => $baseUrl . '/membership/failure',
                'pending' => $baseUrl . '/membership/pending',
            ],
        ];

        // Solo agrega webhook/auto_return si APP_URL es pública y válida para MP
        if (!str_contains($baseUrl, '127.0.0.1') && !str_contains($baseUrl, 'localhost')) {
            $payload['notification_url'] = $baseUrl . '/webhooks/mercadopago';
            $payload['auto_return'] = 'approved';
        }

        $response = Http::withoutVerifying()
            ->withToken(config('services.mercadopago.access_token'))
            ->post('https://api.mercadopago.com/checkout/preferences', $payload);

        if (!$response->successful()) {
            $subscription->update([
                'status' => 'CANCELLED',
            ]);

            dd($response->status(), $response->json(), $response->body());
        }

        $data = $response->json();

        if (!isset($data['init_point'])) {
            $subscription->update([
                'status' => 'CANCELLED',
            ]);

            dd('Mercado Pago no devolvió init_point', $data);
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

    private function getMembershipPrice(): float
    {
        return (float) PlatformSetting::getValue(
            'venue_admin_membership_monthly_price',
            self::DEFAULT_MONTHLY_PRICE
        );
    }
}