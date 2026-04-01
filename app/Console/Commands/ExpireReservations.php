<?php

namespace App\Console\Commands;

use App\Models\FaltaUnoGame;
use App\Models\Reservation;
use Illuminate\Console\Command;

class ExpireReservations extends Command
{
    protected $signature = 'reservations:expire {--dry-run : No modifica, solo muestra cuántas expirarían}';
    protected $description = 'Expira reservas PENDING_PAYMENT vencidas (expires_at < now)';

    public function handle(): int
    {
        $query = Reservation::query()
            ->where('status', 'PENDING_PAYMENT')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now());

        $count = (clone $query)->count();

        if ($this->option('dry-run')) {
            $this->info("DRY RUN: expirarían {$count} reservas.");
            return self::SUCCESS;
        }

        // Obtener IDs antes del update masivo para cancelar Falta Uno games
        $expiringIds = (clone $query)->pluck('id');

        // update masivo (rápido)
        $updated = $query->update([
            'status' => 'EXPIRED',
        ]);

        // Cancelar Falta Uno games cuya reserva expiró
        if ($expiringIds->isNotEmpty()) {
            FaltaUnoGame::whereIn('reservation_id', $expiringIds)
                ->whereIn('status', ['open', 'full'])
                ->update([
                    'status'       => 'cancelled',
                    'cancelled_at' => now(),
                ]);
        }

        $this->info("OK: expirada(s) {$updated} reserva(s).");
        return self::SUCCESS;
    }
}