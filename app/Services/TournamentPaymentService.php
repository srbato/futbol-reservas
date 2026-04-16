<?php

namespace App\Services;

use App\Models\TournamentPayment;
use App\Models\TournamentTeam;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TournamentPaymentService
{
    /**
     * Organizer manually confirms payment received
     */
    public function confirmPaymentManually(TournamentTeam $team): void
    {
        $team->update([
            'payment_confirmed' => true,
            'payment_confirmed_at' => now(),
            'payment_method' => 'manual',
        ]);
    }

    /**
     * Create MercadoPago checkout preference for tournament inscription.
     * Payment goes to the ORGANIZER's MercadoPago account.
     */
    public function createCheckoutPreference(TournamentPayment $payment): ?string
    {
        $tournament = $payment->tournament;

        if (!$tournament->inscription_price || $tournament->inscription_price <= 0) {
            return null;
        }

        // Use organizer's MP token (direct payment to them), fallback to platform
        $accessToken = $tournament->organizer->mp_access_token
            ?? config('services.mercadopago.access_token');

        $baseUrl = rtrim(config('app.url'), '/');

        $payload = [
            'items' => [
                [
                    'title' => "Inscripcion torneo: {$tournament->name}",
                    'quantity' => 1,
                    'currency_id' => 'ARS',
                    'unit_price' => (float) $tournament->inscription_price,
                ]
            ],
            'external_reference' => 'tournament_payment:' . $payment->id,
            'back_urls' => [
                'success' => route('torneos.teams.payment-return', $tournament) . '?payment=success',
                'failure' => route('torneos.teams.payment-return', $tournament) . '?payment=failure',
                'pending' => route('torneos.teams.payment-return', $tournament) . '?payment=pending',
            ],
            'notification_url' => $baseUrl . '/webhooks/mercadopago',
            'auto_return' => 'approved',
        ];

        $response = Http::withOptions(['verify' => app()->isProduction()])
            ->withToken($accessToken)
            ->post('https://api.mercadopago.com/checkout/preferences', $payload);

        if (!$response->successful()) {
            Log::error('Tournament Payment: error creating checkout preference', [
                'payment_id' => $payment->id,
                'tournament_id' => $tournament->id,
                'response' => $response->body(),
            ]);
            return null;
        }

        $data = $response->json();

        $payment->update([
            'mp_preference_id' => $data['id'] ?? null,
        ]);

        return $data['init_point'] ?? $data['sandbox_init_point'] ?? null;
    }

    /**
     * Handle webhook payment confirmation
     */
    public function handlePaymentWebhook(string $paymentRecordId, string $paymentStatus, string $mpPaymentId): void
    {
        $payment = TournamentPayment::find($paymentRecordId);
        if (!$payment) return;

        if ($paymentStatus === 'approved') {
            // Verify payment amount against MercadoPago API
            $tournament = $payment->tournament;
            if ($tournament && $tournament->inscription_price > 0) {
                try {
                    $venue = $tournament->venue;
                    $accessToken = $venue?->mp_access_token
                        ? decrypt($venue->mp_access_token)
                        : config('services.mercadopago.access_token');

                    $mpResponse = \Illuminate\Support\Facades\Http::withOptions(['verify' => app()->isProduction()])
                        ->withToken($accessToken)
                        ->get("https://api.mercadopago.com/v1/payments/{$mpPaymentId}");

                    if ($mpResponse->successful()) {
                        $paidAmount = $mpResponse->json('transaction_amount');
                        $expectedAmount = (float) $payment->amount;

                        if (abs($paidAmount - $expectedAmount) > 0.01) {
                            Log::warning('Tournament payment amount mismatch', [
                                'payment_id' => $payment->id,
                                'expected' => $expectedAmount,
                                'paid' => $paidAmount,
                                'mp_payment_id' => $mpPaymentId,
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Tournament payment: could not verify amount', [
                        'payment_id' => $payment->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $payment->update([
                'status' => 'approved',
                'mp_payment_id' => $mpPaymentId,
            ]);

            Log::info('Tournament payment confirmed via webhook', [
                'payment_id' => $payment->id,
                'tournament_id' => $payment->tournament_id,
                'user_id' => $payment->user_id,
                'mp_payment_id' => $mpPaymentId,
            ]);
        }
    }
}
