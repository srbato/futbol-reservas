<?php

namespace App\Events;

use App\Models\FaltaUnoMessage;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FaltaUnoChatMessageSent implements ShouldBroadcast
{
    public function __construct(public readonly FaltaUnoMessage $message) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("falta-uno.{$this->message->game_id}")];
    }

    public function broadcastAs(): string
    {
        return 'chat.message';
    }

    public function broadcastWith(): array
    {
        $user = $this->message->user;

        return [
            'user' => [
                'id'     => $user->id,
                'name'   => $user->name,
                'avatar' => $user->avatar_path
                    ? \Illuminate\Support\Facades\Storage::url($user->avatar_path)
                    : null,
            ],
            'body'       => $this->message->body,
            'created_at' => $this->message->created_at->toISOString(),
        ];
    }
}
