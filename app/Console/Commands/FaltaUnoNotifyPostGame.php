<?php

namespace App\Console\Commands;

use App\Models\FaltaUnoGame;
use App\Notifications\FaltaUnoPostGameNotification;
use Illuminate\Console\Command;

class FaltaUnoNotifyPostGame extends Command
{
    protected $signature   = 'falta-uno:notify-post-game';
    protected $description = 'Notifica a los participantes 30 minutos después del inicio del partido para que califiquen';

    public function handle(): int
    {
        // Busca partidos que ya empezaron hace >= 30 minutos, están en estado open/full
        // y todavía no se les envió la notificación post-partido
        $games = FaltaUnoGame::whereIn('status', ['open', 'full'])
            ->where('start_at', '<', now()->subMinutes(30))
            ->whereNull('post_game_notified_at')
            ->with(['initiator', 'activeParticipants.user'])
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

            $game->update(['post_game_notified_at' => now()]);
            $this->info("Juego #{$game->id}: notificaciones post-partido enviadas.");
        }

        $this->info("Total: {$total} notificación(es) enviadas en {$games->count()} partido(s).");
        return self::SUCCESS;
    }
}
