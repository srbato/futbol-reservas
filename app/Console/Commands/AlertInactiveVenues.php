<?php

namespace App\Console\Commands;

use App\Mail\InactiveVenueAlertMail;
use App\Models\Venue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class AlertInactiveVenues extends Command
{
    protected $signature = 'app:alert-inactive-venues';
    protected $description = 'Alerta al admin sobre complejos que no reciben reservas hace 7+ dias';

    public function handle(): void
    {
        $inactiveVenues = Venue::where('is_active', true)
            ->whereHas('owner', function ($q) {
                $q->where('is_active', true);
            })
            ->with('owner')
            ->get()
            ->filter(function ($venue) {
                $lastReservation = $venue->fields()
                    ->join('reservations', 'fields.id', '=', 'reservations.field_id')
                    ->where('reservations.status', 'PAID')
                    ->max('reservations.created_at');

                if (!$lastReservation) {
                    return $venue->created_at->lt(now()->subDays(7));
                }

                return \Carbon\Carbon::parse($lastReservation)->lt(now()->subDays(7));
            });

        if ($inactiveVenues->isEmpty()) {
            $this->info('No hay complejos inactivos.');
            return;
        }

        Mail::to('tucancha10@gmail.com')->queue(
            new InactiveVenueAlertMail($inactiveVenues)
        );

        $this->info("Alerta enviada: {$inactiveVenues->count()} complejo(s) inactivo(s).");
    }
}
