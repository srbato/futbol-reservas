<?php

namespace App\Notifications;

use App\Models\FaltaUnoGame;
use App\Models\User;
use App\Notifications\Concerns\HasWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FaltaUnoReminderNotification extends Notification implements ShouldQueue
{
    use HasWebPush, Queueable;

    public function __construct(
        public readonly FaltaUnoGame $game,
        public readonly User $user,
    ) {}


    public function toArray($notifiable): array
    {
        $this->game->loadMissing('field.venue');

        $sport   = ucfirst($this->game->field->sport ?? 'fútbol');
        $venue   = $this->game->field->venue->name ?? 'un complejo';
        $date    = $this->game->start_at->format('d/m H:i');
        $slots   = $this->game->remainingSlots();

        return [
            'title'        => "Quedan {$slots} lugar(es) en un partido de {$sport}",
            'body'         => "Partido en {$venue} el {$date}. ¡Apurate que se llena!",
            'icon'         => '⚡',
            'action_url'   => route('falta-uno.index'),
            'action_label' => 'Unirme',
        ];
    }
}
