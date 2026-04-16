<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Notifications\ReservationPostGameNotification;
use App\Notifications\ReviewReminderNotification;
use Illuminate\Console\Command;

class ReservationNotifyPostGame extends Command
{
    protected $signature   = 'reservations:notify-post-game';
    protected $description = 'Notifica a los usuarios 30 minutos después de que terminó su partido convencional para que carguen resultado y dejen reseña';

    public function handle(): int
    {
        // Busca reservas pagas o validadas en cancha cuyo end_at ya pasó hace >= 30 min
        // y que todavía no se les envió la notificación post-partido
        $reservations = Reservation::whereIn('status', ['PAID', 'PENDING_CASH'])
            ->where('end_at', '<', now()->subMinutes(30))
            ->whereNull('post_game_notified_at')
            ->with(['user', 'field.venue', 'players.user'])
            ->get();

        $total = 0;

        foreach ($reservations as $reservation) {
            // Notificar al dueño de la reserva
            if ($reservation->user) {
                $reservation->user->notify(new ReservationPostGameNotification($reservation));
                $total++;

                // También enviar reminder de reseña del venue
                if ($reservation->field?->venue) {
                    $reservation->user->notify(new ReviewReminderNotification($reservation->field->venue));
                }
            }

            // Notificar a los jugadores agregados a la reserva (si existen)
            foreach ($reservation->players as $player) {
                if ($player->user && $player->user_id !== $reservation->user_id) {
                    $player->user->notify(new ReservationPostGameNotification($reservation));
                    $total++;
                }
            }

            // Marcar como notificada
            $reservation->update([
                'post_game_notified_at' => now(),
            ]);

            $this->info("Reserva #{$reservation->id}: notificación post-partido enviada.");
        }

        $this->info("Total: {$total} notificación(es) enviadas en {$reservations->count()} reserva(s).");
        return self::SUCCESS;
    }
}
