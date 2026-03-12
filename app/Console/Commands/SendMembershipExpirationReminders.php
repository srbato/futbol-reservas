<?php

namespace App\Console\Commands;

use App\Mail\MembershipExpirationReminderMail;
use App\Models\SystemMessage;
use App\Models\VenueAdminSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendMembershipExpirationReminders extends Command
{
    protected $signature = 'membership:send-expiration-reminders';
    protected $description = 'Envía recordatorios de vencimiento de membresía';

    public function handle(): int
    {
        $subscriptions = VenueAdminSubscription::query()
            ->with('user')
            ->where('status', 'ACTIVE')
            ->whereNotNull('starts_at')
            ->where('starts_at', '<=', now())
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->get();

        $sentCount = 0;

        foreach ($subscriptions as $subscription) {
            if (!$subscription->user || !$subscription->user->email) {
                continue;
            }

            $daysRemaining = (int) now()->startOfDay()->diffInDays(
                $subscription->expires_at->copy()->startOfDay(),
                false
            );

            if ($daysRemaining === 7 && !$subscription->reminder_sent_7d_at) {
                Mail::to($subscription->user->email)
                    ->send(new MembershipExpirationReminderMail($subscription, 7));

                $this->createInternalReminder($subscription, 7);

                $subscription->update([
                    'reminder_sent_7d_at' => now(),
                ]);

                $sentCount++;
                continue;
            }

            if ($daysRemaining === 2 && !$subscription->reminder_sent_2d_at) {
                Mail::to($subscription->user->email)
                    ->send(new MembershipExpirationReminderMail($subscription, 2));

                $this->createInternalReminder($subscription, 2);

                $subscription->update([
                    'reminder_sent_2d_at' => now(),
                ]);

                $sentCount++;
                continue;
            }

            if ($daysRemaining === 0 && !$subscription->reminder_sent_0d_at) {
                Mail::to($subscription->user->email)
                    ->send(new MembershipExpirationReminderMail($subscription, 0));

                $this->createInternalReminder($subscription, 0);

                $subscription->update([
                    'reminder_sent_0d_at' => now(),
                ]);

                $sentCount++;
            }
        }

        $this->info("Recordatorios enviados: {$sentCount}");

        return self::SUCCESS;
    }

    private function createInternalReminder(VenueAdminSubscription $subscription, int $daysRemaining): void
    {
        $user = $subscription->user;

        if (!$user) {
            return;
        }

        $this->deactivatePreviousMembershipReminders($user->id);

        $title = match ($daysRemaining) {
            7 => 'Tu membresía vence en 7 días',
            2 => 'Tu membresía vence en 2 días',
            0 => 'Tu membresía vence hoy',
            default => 'Recordatorio de membresía',
        };

        $body = match ($daysRemaining) {
            7 => "Tu membresía de socio en TuCancha vence el {$subscription->expires_at?->format('d/m/Y H:i')}.\n\nTe recomendamos renovarla con tiempo para no perder acceso al panel admin.",
            2 => "Tu membresía de socio en TuCancha vence el {$subscription->expires_at?->format('d/m/Y H:i')}.\n\nQuedan solo 2 días para renovarla y mantener tu acceso al panel admin.",
            0 => "Tu membresía de socio en TuCancha vence hoy ({$subscription->expires_at?->format('d/m/Y H:i')}).\n\nRenovala tu plan para seguir administrando tus complejos sin interrupciones.",
            default => "Tu membresía está por vencer.",
        };

        SystemMessage::create([
            'title' => $title,
            'body' => $body,
            'target_user_id' => $user->id,
            'is_active' => true,
        ]);
    }

    private function deactivatePreviousMembershipReminders(int $userId): void
    {
        SystemMessage::query()
            ->where('target_user_id', $userId)
            ->where('is_active', true)
            ->whereIn('title', [
                'Tu membresía vence en 7 días',
                'Tu membresía vence en 2 días',
                'Tu membresía vence hoy',
            ])
            ->update([
                'is_active' => false,
            ]);
    }
}