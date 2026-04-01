<?php

namespace App\Notifications;

use App\Models\Venue;
use App\Notifications\Concerns\HasWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ReviewReminderNotification extends Notification implements ShouldQueue
{
    use HasWebPush, Queueable;

    public function __construct(
        public readonly Venue $venue,
    ) {}

    public function toArray($notifiable): array
    {
        return [
            'title'        => 'Como estuvo tu experiencia?',
            'body'         => "Jugaste en {$this->venue->name}. Dejale una resena para ayudar a otros jugadores.",
            'icon'         => '⭐',
            'action_url'   => route('venues.show', $this->venue) . '#resenas',
            'action_label' => 'Dejar resena',
        ];
    }
}
