<?php

namespace App\Notifications;

use App\Models\FaltaUnoGame;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FaltaUnoPostGameNotification extends Notification implements ShouldQueue
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

        return [
            'title'        => '¿Cómo te fue en el partido?',
            'body'         => "El partido en {$venue} ya terminó. ¡Calificá a tus compañeros!",
            'icon'         => '⭐',
            'action_url'   => route('falta-uno.rate', $this->game),
            'action_label' => 'Calificar',
        ];
    }
}
