<?php

namespace App\Notifications;

use App\Models\Reservation;
use App\Notifications\Concerns\HasWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ReservationPostGameNotification extends Notification implements ShouldQueue
{
    use HasWebPush, Queueable;

    public function __construct(public readonly Reservation $reservation) {}

    public function toArray($notifiable): array
    {
        $this->reservation->loadMissing('field.venue');

        $venue = $this->reservation->field->venue->name ?? 'el complejo';
        $field = $this->reservation->field->name ?? 'la cancha';

        return [
            'title'        => '¿Cómo te fue en el partido?',
            'body'         => "Tu partido en {$field} ({$venue}) ya terminó. Cargá el resultado y dejá una reseña.",
            'icon'         => '⚽',
            'action_url'   => route('match_history'),
            'action_label' => 'Cargar resultado',
        ];
    }
}
