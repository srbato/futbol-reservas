<?php

namespace App\Events;

use App\Models\TournamentRequestMessage;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TournamentChatMessageSent implements ShouldBroadcast
{
    public function __construct(public readonly TournamentRequestMessage $message) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("tournament-request.{$this->message->venue_request_id}")];
    }

    public function broadcastAs(): string
    {
        return 'chat.message';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'user_id' => $this->message->user_id,
            'user_name' => $this->message->user->name ?? 'Usuario',
            'message' => $this->message->message,
            'created_at' => 'ahora',
        ];
    }
}
