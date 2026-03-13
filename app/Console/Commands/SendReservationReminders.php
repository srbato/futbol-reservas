<?php

namespace App\Console\Commands;

use App\Mail\ReservationReminderMail;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendReservationReminders extends Command
{
    protected $signature   = 'reservations:send-reminders';
    protected $description = 'Envía recordatorios a usuarios con reservas próximas en aprox. 2 horas';

    public function handle(): void
    {
        $from = Carbon::now()->addMinutes(90);
        $to   = Carbon::now()->addMinutes(150);

        $reservations = Reservation::with(['user', 'field.venue'])
            ->where('status', 'PAID')
            ->where('reminder_sent', false)
            ->whereBetween('start_at', [$from, $to])
            ->get();

        foreach ($reservations as $reservation) {
            if (!$reservation->user?->email) {
                continue;
            }

            Mail::to($reservation->user->email)
                ->send(new ReservationReminderMail($reservation));

            $reservation->update(['reminder_sent' => true]);

            $this->info("Recordatorio enviado: reserva #{$reservation->id}");
        }

        $this->info("Total: {$reservations->count()} recordatorio(s) enviado(s).");
    }
}