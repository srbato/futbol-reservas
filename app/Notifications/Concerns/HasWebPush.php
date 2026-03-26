<?php

namespace App\Notifications\Concerns;

use NotificationChannels\WebPush\WebPushMessage;

trait HasWebPush
{
    public function via($notifiable): array
    {
        $channels = ['database'];

        if ($notifiable->pushSubscriptions()->exists()) {
            $channels[] = \NotificationChannels\WebPush\WebPushChannel::class;
        }

        return $channels;
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        $data = $this->toArray($notifiable);

        return (new WebPushMessage)
            ->title($data['title'] ?? 'TuCancha')
            ->body($data['body'] ?? '')
            ->action($data['action_label'] ?? 'Ver', 'open')
            ->data(['url' => $data['action_url'] ?? url('/')]);
    }
}
