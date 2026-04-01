<?php

namespace App\Notifications;

use App\Models\FaltaUnoGame;
use App\Notifications\Concerns\HasWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FaltaUnoKickedNotification extends Notification implements ShouldQueue
{
    use HasWebPush, Queueable;

    public function __construct(
        public readonly FaltaUnoGame $game,
    ) {}

    public function toArray($notifiable): array
    {
        $this->game->loadMissing('field.venue');

        $venue = $this->game->field->venue->name ?? 'el complejo';
        $date  = $this->game->start_at->translatedFormat('l j \d\e F, H:i');

        return [
            'title'        => 'Fuiste removido de un partido',
            'body'         => "El organizador te removio del partido en {$venue} el {$date}.",
            'icon'         => '🚫',
            'action_url'   => route('falta-uno.index'),
            'action_label' => 'Buscar otros partidos',
        ];
    }
}
