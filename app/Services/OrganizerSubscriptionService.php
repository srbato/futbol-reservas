<?php

namespace App\Services;

use App\Models\OrganizerPlan;
use App\Models\OrganizerSubscription;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrganizerSubscriptionService
{
    public function getTier(User $user): string
    {
        return $user->organizerTier();
    }

    public function canCreateTournament(User $user): array
    {
        $tier = $this->getTier($user);
        $plan = $this->getPlanModel($tier);

        if ($plan && !$plan->isUnlimited()) {
            $activeCount = Tournament::where('organizer_user_id', $user->id)
                ->whereNotIn('status', ['finished', 'cancelled'])
                ->count();

            if ($activeCount >= $plan->max_tournaments) {
                return [false, 'Con el plan ' . $plan->name . ' solo podes tener ' . $plan->max_tournaments . ' torneo(s) activo(s). Finalizalo o cancelalo para crear otro, o suscribite a Pro.'];
            }
        }

        return [true, null];
    }

    public function getMaxTeams(User $user): int
    {
        $plan = $this->getPlanModel($this->getTier($user));
        return $plan ? $plan->max_teams_per_tournament : 8;
    }

    public function getAvailableFormats(User $user): array
    {
        $plan = $this->getPlanModel($this->getTier($user));
        return $plan ? ($plan->available_formats ?? ['single_elimination']) : ['single_elimination'];
    }

    public function subscribe(User $user, string $email): array
    {
        $existing = $user->activeOrganizerSubscription;
        if ($existing && $existing->isPro()) {
            return [false, 'Ya tenes una suscripcion Pro activa.'];
        }

        $proPlan = OrganizerPlan::where('slug', 'pro')->where('is_active', true)->first();
        $price = $proPlan ? (float) $proPlan->monthly_price : 9999;

        $subscription = OrganizerSubscription::create([
            'user_id'       => $user->id,
            'plan'          => 'pro',
            'status'        => 'PENDING_PAYMENT',
            'monthly_price' => $price,
            'currency'      => 'ARS',
        ]);

        $baseUrl = rtrim(config('app.url'), '/');

        $payload = [
            'reason'             => 'Plan Organizador Pro TuCancha',
            'payer_email'        => $email,
            'auto_recurring'     => [
                'frequency'          => 1,
                'frequency_type'     => 'months',
                'transaction_amount' => $price,
                'currency_id'        => 'ARS',
            ],
            'back_url'           => $baseUrl . '/organizador/suscripcion/exito',
            'notification_url'   => $baseUrl . '/webhooks/mercadopago',
            'external_reference' => 'organizer_subscription:' . $subscription->id,
            'status'             => 'pending',
        ];

        $token = config('services.mercadopago.access_token');

        $response = Http::withOptions(['verify' => app()->isProduction()])
            ->withToken($token)
            ->post('https://api.mercadopago.com/preapproval', $payload);

        if (!$response->successful()) {
            Log::error('OrganizerSubscription: error al crear preapproval', [
                'subscription_id' => $subscription->id,
                'status'          => $response->status(),
                'body'            => $response->body(),
            ]);
            $subscription->delete();
            return [false, 'No se pudo iniciar la suscripcion con MercadoPago.'];
        }

        $data = $response->json();

        if (!isset($data['init_point'])) {
            $subscription->delete();
            return [false, 'No se pudo iniciar la suscripcion con MercadoPago.'];
        }

        $subscription->update(['mp_preapproval_id' => $data['id'] ?? null]);

        return [true, $data['init_point']];
    }

    public function cancel(User $user): bool
    {
        $subscription = $user->activeOrganizerSubscription;
        if (!$subscription) {
            return false;
        }

        if ($subscription->mp_preapproval_id) {
            $token = config('services.mercadopago.access_token');

            $response = Http::withOptions(['verify' => app()->isProduction()])
                ->withToken($token)
                ->put("https://api.mercadopago.com/preapproval/{$subscription->mp_preapproval_id}", [
                    'status' => 'cancelled',
                ]);

            if (!$response->successful()) {
                Log::error('OrganizerSubscription: error al cancelar preapproval', [
                    'preapproval_id' => $subscription->mp_preapproval_id,
                    'status'         => $response->status(),
                    'body'           => $response->body(),
                ]);
            }
        }

        $subscription->update([
            'status'       => 'CANCELLED',
            'cancelled_at' => now(),
        ]);

        return true;
    }

    public function handleWebhook(string $preapprovalId, array $data): void
    {
        $subscription = OrganizerSubscription::where('mp_preapproval_id', $preapprovalId)->first();
        if (!$subscription) {
            return;
        }

        $mpStatus = $data['status'] ?? null;

        if ($mpStatus === 'authorized') {
            $subscription->update([
                'status'                 => 'ACTIVE',
                'mp_subscription_status' => 'authorized',
                'starts_at'              => $subscription->starts_at ?? now(),
                'expires_at'             => now()->addMonth(),
            ]);
        } elseif ($mpStatus === 'cancelled') {
            $subscription->update([
                'status'                 => 'CANCELLED',
                'mp_subscription_status' => 'cancelled',
                'cancelled_at'           => now(),
            ]);
        } elseif ($mpStatus === 'paused') {
            $subscription->update([
                'mp_subscription_status' => 'paused',
            ]);
        }
    }

    private function getPlanModel(string $tier): ?OrganizerPlan
    {
        return OrganizerPlan::where('slug', $tier)->where('is_active', true)->first();
    }
}
