<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Command;

class PurgeDeadReservations extends Command
{
    protected $signature   = 'reservations:purge-dead';
    protected $description = 'Delete CANCELLED and EXPIRED reservations older than 2 months';

    public function handle(): void
    {
        $deleted = Reservation::whereIn('status', ['CANCELLED', 'EXPIRED'])
            ->where('updated_at', '<', now()->subMonths(2))
            ->delete();

        $this->info("Purged {$deleted} dead reservation(s).");
    }
}
