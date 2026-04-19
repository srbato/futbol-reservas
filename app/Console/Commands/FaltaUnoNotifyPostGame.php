<?php

namespace App\Console\Commands;

use App\Models\FaltaUnoGame;
use App\Models\FaltaUnoSportProfile;
use App\Notifications\FaltaUnoPostGameNotification;
use Illuminate\Console\Command;

class FaltaUnoNotifyPostGame extends Command
{
    protected $signature   = 'falta-uno:notify-post-game';
    protected $description = 'Notifica a los participantes 30 minutos después del inicio del partido para que califiquen, marca el partido como finished e incrementa games_played';

    public function handle(): int
    {
        // Busca partidos que ya empezaron hace >= 30 minutos, están en estado open/full
        // y todavía no se les envió la notificación post-partido
        $games = FaltaUnoGame::whereIn('status', ['open', 'full'])
            ->where('start_at', '<', now()->subMinutes(30))
            ->whereNull('post_game_notified_at')
            ->with(['initiator', 'activeParticipants.user', 'field'])
            ->get();

        $total = 0;

        foreach ($games as $game) {
            // Notificar al iniciador
            if ($game->initiator) {
                $game->initiator->notify(new FaltaUnoPostGameNotification($game));
                $total++;
            }

            // Notificar a todos los participantes confirmados
            foreach ($game->activeParticipants as $participant) {
                if ($participant->user) {
                    $participant->user->notify(new FaltaUnoPostGameNotification($game));
                    $total++;
                }
            }

            // Recalcular stats completas para todos los participantes y el iniciador
            // (fuente única de verdad: recalcula desde FaltaUnoParticipant + ReservationResult)
            $sport = $game->field->sport ?? null;
            if ($sport) {
                $allUserIds = $game->activeParticipants
                    ->pluck('user_id')
                    ->push($game->initiator_user_id)
                    ->unique()
                    ->filter();

                $profiles = FaltaUnoSportProfile::where('sport', $sport)
                    ->whereIn('user_id', $allUserIds)
                    ->get();

                foreach ($profiles as $profile) {
                    $profile->recalculateStats();
                }
            }

            // Marcar el partido como finished
            $game->update([
                'status'               => 'finished',
                'post_game_notified_at' => now(),
            ]);

            $this->info("Juego #{$game->id}: notificaciones post-partido enviadas, games_played incrementado, estado -> finished.");
        }

        $this->info("Total: {$total} notificación(es) enviadas en {$games->count()} partido(s).");
        return self::SUCCESS;
    }
}
