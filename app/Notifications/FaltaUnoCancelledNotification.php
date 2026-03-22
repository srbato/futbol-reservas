<?php

namespace App\Notifications;

use App\Models\FaltaUnoGame;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FaltaUnoCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly FaltaUnoGame $game) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $this->game->loadMissing('field.venue');

        $venue = $this->game->field->venue->name ?? 'el complejo';
        $date  = $this->game->start_at->format('d/m H:i');

        return [
            'title'        => 'Partido cancelado',
            'body'         => "El partido en {$venue} el {$date} fue cancelado por el organizador.",
            'icon'         => '❌',
            'action_url'   => route('falta-uno.index'),
            'action_label' => 'Buscar otros partidos',
        ];
    }
}
