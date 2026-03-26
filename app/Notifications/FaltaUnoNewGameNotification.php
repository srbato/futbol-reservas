<?php

namespace App\Notifications;

use App\Models\FaltaUnoGame;
use App\Notifications\Concerns\HasWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FaltaUnoNewGameNotification extends Notification implements ShouldQueue
{
    use HasWebPush, Queueable;

    public function __construct(public readonly FaltaUnoGame $game) {}


    public function toArray($notifiable): array
    {
        $this->game->loadMissing('field.venue');

        $sport  = ucfirst($this->game->field->sport ?? 'fútbol');
        $venue  = $this->game->field->venue->name ?? 'un complejo';
        $date   = $this->game->start_at->format('d/m H:i');

        return [
            'title'        => "Nuevo partido de {$sport}",
            'body'         => "Hay un partido nuevo en {$venue} el {$date}. ¡Sumate!",
            'icon'         => '⚡',
            'action_url'   => route('falta-uno.index'),
            'action_label' => 'Ver partidos',
        ];
    }
}
