<?php

namespace App\Console\Commands;

use App\Mail\FaltaUnoExpiredMail;
use App\Mail\FaltaUnoExpiredParticipantMail;
use App\Models\FaltaUnoGame;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ProcessFaltaUnoExpired extends Command
{
    protected $signature   = 'falta-uno:process-expired {--dry-run : Solo muestra cuántos vencerían}';
    protected $description = 'Expira partidos Falta Uno que no se llenaron a tiempo y libera el slot';

    public function handle(): int
    {
        // Buscamos partidos abiertos con reserva pagada cuyo fill_deadline ya pasó
        $games = FaltaUnoGame::where('status', 'open')
            ->whereHas('reservation', fn($q) => $q->where('status', 'PAID'))
            ->with(['field.faltaUnoSetting', 'field.venue', 'initiator', 'activeParticipants.user'])
            ->get()
            ->filter(fn($game) => $game->hasPassedFillDeadline());

        $count = $games->count();

        if ($this->option('dry-run')) {
            $this->info("DRY RUN: vencerían {$count} partido(s).");
            return self::SUCCESS;
        }

        foreach ($games as $game) {
            // Expirar el partido
            $game->update([
                'status'       => 'expired',
                'cancelled_at' => now(),
            ]);

            // Liberar la reserva (volver a CANCELLED para que el slot quede libre)
            if ($game->reservation) {
                $game->reservation->update(['status' => 'CANCELLED']);
            }

            // Notificar al iniciador
            try {
                Mail::to($game->initiator->email)->send(new FaltaUnoExpiredMail($game));
            } catch (\Throwable $e) {
                $this->warn("No se pudo enviar mail al iniciador del juego #{$game->id}: {$e->getMessage()}");
            }

            // Notificar a los participantes confirmados
            foreach ($game->activeParticipants as $participant) {
                if (!$participant->user) {
                    continue;
                }
                try {
                    Mail::to($participant->user->email)
                        ->send(new FaltaUnoExpiredParticipantMail($game, $participant->user));
                } catch (\Throwable $e) {
                    $this->warn("No se pudo enviar mail al participante #{$participant->user_id} del juego #{$game->id}: {$e->getMessage()}");
                }
            }

            $this->info("Juego #{$game->id} expirado. Slot liberado. Participantes notificados.");
        }

        $this->info("OK: {$count} partido(s) procesado(s).");
        return self::SUCCESS;
    }
}
