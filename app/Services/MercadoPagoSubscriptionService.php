<?php

namespace App\Services;

use App\Models\VenueAdminSubscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MercadoPagoSubscriptionService
{
    private function token(): string
    {
        return config('services.mercadopago.access_token');
    }

    private function http()
    {
        return Http::withOptions(['verify' => !app()->isLocal()])
            ->withToken($this->token());
    }

    public function createPreapproval(VenueAdminSubscription $subscription, string $userEmail, string $planName, float $price, string $billingCycle, string $backUrl): array
    {
        $frequency = $billingCycle === 'annual' ? $subscription->long_term_months : 1;

        $baseUrl = rtrim(config('app.url'), '/');

        $autoRecurring = [
            'frequency'          => $frequency,
            'frequency_type'     => 'months',
            'transaction_amount' => $price,
            'currency_id'        => 'ARS',
        ];

        $payload = [
            'reason'           => "Plan {$planName} TuCancha",
            'payer_email'      => $userEmail,
            'auto_recurring'   => $autoRecurring,
            'back_url'           => $backUrl,
            'notification_url'   => $baseUrl . '/webhooks/mercadopago',
            'external_reference' => 'venue_admin_subscription:' . $subscription->id,
        ];

        $response = $this->http()->post('https://api.mercadopago.com/preapproval', $payload);

        if (!$response->successful()) {
            Log::error('MP Subscription: error al crear preapproval', [
                'subscription_id' => $subscription->id,
                'status'          => $response->status(),
                'body'            => $response->body(),
            ]);
        }

        return [$response->successful(), $response->json()];
    }

    public function cancelPreapproval(string $preapprovalId): bool
    {
        $response = $this->http()->put("https://api.mercadopago.com/preapproval/{$preapprovalId}", [
            'status' => 'cancelled',
        ]);

        if (!$response->successful()) {
            Log::error('MP Subscription: error al cancelar preapproval', [
                'preapproval_id' => $preapprovalId,
                'status'         => $response->status(),
                'body'           => $response->body(),
            ]);
        }

        return $response->successful();
    }

    public function getPreapproval(string $preapprovalId): array
    {
        $response = $this->http()->get("https://api.mercadopago.com/preapproval/{$preapprovalId}");
        return [$response->successful(), $response->json()];
    }

    public function getAuthorizedPayment(string $paymentId): array
    {
        $response = $this->http()->get("https://api.mercadopago.com/authorized_payments/{$paymentId}");
        return [$response->successful(), $response->json()];
    }
}
