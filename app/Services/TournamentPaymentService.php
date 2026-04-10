<?php

namespace App\Services;

use App\Models\TournamentTeam;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TournamentPaymentService
{
    /**
     * Organizer manually confirms payment received (free or pro tier)
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
     * Create MercadoPago checkout preference for team inscription (Pro tier only)
     * The payment goes to the ORGANIZER's MercadoPago account
     */
    public function createCheckoutPreference(TournamentTeam $team): ?string
    {
        $tournament = $team->tournament;

        if (!$tournament->inscription_price || $tournament->inscription_price <= 0) {
            return null;
        }

        // Get organizer's MP access token (from their OAuth connection)
        // For now, use platform token — in future, organizers can connect their MP via OAuth
        $accessToken = config('services.mercadopago.access_token');

        $baseUrl = rtrim(config('app.url'), '/');

        $payload = [
            'items' => [
                [
                    'title' => "Inscripcion: {$team->name} — {$tournament->name}",
                    'quantity' => 1,
                    'currency_id' => 'ARS',
                    'unit_price' => (float) $tournament->inscription_price,
                ]
            ],
            'external_reference' => 'tournament_team:' . $team->id,
            'back_urls' => [
                'success' => $baseUrl . "/torneos/{$tournament->slug}/equipos/{$team->id}?payment=success",
                'failure' => $baseUrl . "/torneos/{$tournament->slug}/equipos/{$team->id}?payment=failure",
                'pending' => $baseUrl . "/torneos/{$tournament->slug}/equipos/{$team->id}?payment=pending",
            ],
            'notification_url' => $baseUrl . '/webhooks/mercadopago',
            'auto_return' => 'approved',
        ];

        $response = Http::withOptions(['verify' => app()->isProduction()])
            ->withToken($accessToken)
            ->post('https://api.mercadopago.com/checkout/preferences', $payload);

        if (!$response->successful()) {
            Log::error('Tournament Payment: error creating checkout preference', [
                'team_id' => $team->id,
                'tournament_id' => $tournament->id,
                'response' => $response->body(),
            ]);
            return null;
        }

        $data = $response->json();

        $team->update([
            'mp_preference_id' => $data['id'] ?? null,
        ]);

        return $data['init_point'] ?? $data['sandbox_init_point'] ?? null;
    }

    /**
     * Handle webhook payment confirmation
     */
    public function handlePaymentWebhook(string $teamId, string $paymentStatus, string $paymentId): void
    {
        $team = TournamentTeam::find($teamId);
        if (!$team) return;

        if ($paymentStatus === 'approved') {
            $team->update([
                'payment_confirmed' => true,
                'payment_confirmed_at' => now(),
                'payment_method' => 'mercadopago',
                'payment_external_id' => $paymentId,
            ]);

            Log::info('Tournament team payment confirmed via webhook', [
                'team_id' => $team->id,
                'tournament_id' => $team->tournament_id,
                'payment_id' => $paymentId,
            ]);
        }
    }
}
