<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Models\VenueReview;
use App\Notifications\ReviewReminderNotification;
use Illuminate\Console\Command;

class SendReviewReminders extends Command
{
    protected $signature = 'reviews:send-reminders';
    protected $description = 'Envía recordatorios de reseña a usuarios que jugaron y no dejaron reseña';

    public function handle(): int
    {
        // Reservas PAID que ya pasaron (entre 2 y 48 horas atrás),
        // sin reminder enviado, y cuyo usuario no tiene reseña en ese venue
        $reservations = Reservation::query()
            ->where('status', 'PAID')
            ->whereBetween('start_at', [now()->subHours(48), now()->subHours(2)])
            ->whereNull('review_reminder_sent_at')
            ->with(['user', 'field.venue'])
            ->get();

        $sent = 0;

        foreach ($reservations as $reservation) {
            $user  = $reservation->user;
            $venue = $reservation->field?->venue;

            if (!$user || !$venue) {
                continue;
            }

            // Si ya dejó reseña en este venue, no enviar
            $hasReview = VenueReview::where('user_id', $user->id)
                ->where('venue_id', $venue->id)
                ->exists();

            if ($hasReview) {
                $reservation->update(['review_reminder_sent_at' => now()]);
                continue;
            }

            $user->notify(new ReviewReminderNotification($venue));
            $reservation->update(['review_reminder_sent_at' => now()]);
            $sent++;
        }

        $this->info("Enviados {$sent} recordatorios de resena.");

        return self::SUCCESS;
    }
}
