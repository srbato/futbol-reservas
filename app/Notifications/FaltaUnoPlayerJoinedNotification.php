<?php

namespace App\Notifications;

use App\Models\FaltaUnoGame;
use App\Models\User;
use App\Notifications\Concerns\HasWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FaltaUnoPlayerJoinedNotification extends Notification implements ShouldQueue
{
    use HasWebPush, Queueable;

    public function __construct(
        public readonly FaltaUnoGame $game,
        public readonly User $joiner,
    ) {}


    public function toArray($notifiable): array
    {
        $this->game->loadMissing('field');

        $slots = $this->game->remainingSlots();
        $sport = ucfirst($this->game->field->sport ?? 'fútbol');

        return [
            'title'        => "{$this->joiner->name} se unió a tu partido",
            'body'         => "Faltan {$slots} lugar(es) para completar el partido de {$sport}.",
            'icon'         => '⚽',
            'action_url'   => route('falta-uno.index'),
            'action_label' => 'Ver partido',
        ];
    }
}
