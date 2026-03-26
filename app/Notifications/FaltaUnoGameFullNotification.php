<?php

namespace App\Notifications;

use App\Models\FaltaUnoGame;
use App\Notifications\Concerns\HasWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FaltaUnoGameFullNotification extends Notification implements ShouldQueue
{
    use HasWebPush, Queueable;

    public function __construct(public readonly FaltaUnoGame $game) {}


    public function toArray($notifiable): array
    {
        $this->game->loadMissing('field.venue');

        $venue = $this->game->field->venue->name ?? 'el complejo';
        $date  = $this->game->start_at->format('d/m H:i');

        return [
            'title'        => '¡Tu partido se completó!',
            'body'         => "El partido en {$venue} el {$date} ya tiene todos los jugadores. ¡Todo listo!",
            'icon'         => '🎉',
            'action_url'   => route('falta-uno.index'),
            'action_label' => 'Ver partido',
        ];
    }
}
