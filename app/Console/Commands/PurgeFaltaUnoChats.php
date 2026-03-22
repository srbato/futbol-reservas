<?php

namespace App\Console\Commands;

use App\Models\FaltaUnoGame;
use App\Models\FaltaUnoMessage;
use Illuminate\Console\Command;

class PurgeFaltaUnoChats extends Command
{
    protected $signature = 'falta-uno:purge-chats';

    protected $description = 'Elimina mensajes de chat de partidos que terminaron hace más de 3 horas';

    public function handle(): void
    {
        $cutoff = now()->subHours(3);

        FaltaUnoGame::where('start_at', '<', $cutoff)
            ->select('id')
            ->chunk(100, function ($games) {
                $ids = $games->pluck('id');
                $deleted = FaltaUnoMessage::whereIn('game_id', $ids)->delete();
                if ($deleted > 0) {
                    $this->info("Eliminados {$deleted} mensajes de chat.");
                }
            });

        $this->info('Purge de chats Falta Uno completado.');
    }
}
