<?php

namespace App\Notifications;

use App\Models\FaltaUnoGame;
use App\Models\User;
use App\Notifications\Concerns\HasWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FaltaUnoGameReminderParticipantNotification extends Notification implements ShouldQueue
{
    use HasWebPush, Queueable;

    public function __construct(
        public readonly FaltaUnoGame $game,
        public readonly User $recipient,
    ) {}

    public function toArray($notifiable): array
    {
        $this->game->loadMissing('field.venue');

        $venue = $this->game->field->venue->name ?? 'el complejo';
        $time  = $this->game->start_at->format('H:i');

        return [
            'title'        => 'Tu partido es en 2 horas',
            'body'         => "Jugás en {$venue} a las {$time} hs. ¡No llegues tarde!",
            'icon'         => '⚽',
            'action_url'   => route('falta-uno.show', $this->game),
            'action_label' => 'Ver partido',
        ];
    }
}
