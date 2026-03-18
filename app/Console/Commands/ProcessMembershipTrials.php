<?php

namespace App\Console\Commands;

use App\Mail\TrialEndingMail;
use App\Models\SystemMessage;
use App\Models\VenueAdminSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ProcessMembershipTrials extends Command
{
    protected $signature = 'membership:process-trials';
    protected $description = 'Envía aviso cuando el trial vence hoy y revoca acceso cuando ya expiró';

    public function handle(): int
    {
        $this->warnExpiringToday();
        $this->revokeExpired();

        return self::SUCCESS;
    }

    private function warnExpiringToday(): void
    {
        $subscriptions = VenueAdminSubscription::query()
            ->with('user')
            ->where('status', 'TRIAL')
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', today())
            ->get();

        foreach ($subscriptions as $subscription) {
            if (!$subscription->user || !$subscription->user->email) {
                continue;
            }

            Mail::to($subscription->user->email)
                ->send(new TrialEndingMail($subscription));

            SystemMessage::create([
                'title'          => 'Tu período de prueba vence hoy',
                'body'           => "Tu período de prueba gratuito en TuCancha vence hoy ({$subscription->expires_at?->format('d/m/Y H:i')}). Contratá un plan para seguir administrando tus complejos.",
                'target_user_id' => $subscription->user_id,
                'is_active'      => true,
                'is_system'      => true,
            ]);

            $this->info("Aviso de trial enviado a: {$subscription->user->email}");
        }
    }

    private function revokeExpired(): void
    {
        $subscriptions = VenueAdminSubscription::query()
            ->with('user')
            ->where('status', 'TRIAL')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($subscriptions as $subscription) {
            $subscription->update(['status' => 'EXPIRED']);

            $user = $subscription->user;
            if (!$user) {
                continue;
            }

            // Only revoke role if the user has no other active/trial subscription
            $hasOtherActive = $user->venueAdminSubscriptions()
                ->whereIn('status', ['ACTIVE', 'TRIAL'])
                ->whereNotNull('expires_at')
                ->where('expires_at', '>', now())
                ->where('id', '!=', $subscription->id)
                ->exists();

            if (!$hasOtherActive && $user->role === 'venue_admin') {
                $user->role = 'user';
                $user->save();
            }

            $this->info("Trial expirado y acceso revocado para: {$user->email}");
        }
    }
}
