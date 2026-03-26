<?php

namespace App\Notifications;

use App\Models\FaltaUnoGame;
use App\Notifications\Concerns\HasWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FaltaUnoPostGameNotification extends Notification implements ShouldQueue
{
    use HasWebPush, Queueable;

    public function __construct(public readonly FaltaUnoGame $game) {}


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
