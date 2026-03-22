<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ReservationReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Reservation $reservation) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $this->reservation->loadMissing('field.venue');

        $venue = $this->reservation->field->venue->name ?? 'el complejo';
        $date  = $this->reservation->start_at->format('d/m H:i');

        return [
            'title'        => 'Tu reserva es en ~2 horas',
            'body'         => "Tenés cancha reservada en {$venue} a las {$date}.",
            'icon'         => '📅',
            'action_url'   => route('reservations.show', $this->reservation),
            'action_label' => 'Ver reserva',
        ];
    }
}
