<?php

namespace App\Events;

use App\Models\FaltaUnoGame;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FaltaUnoParticipantJoined implements ShouldBroadcast
{
    public int $gameId;
    public int $joined;
    public int $needed;
    public string $status;

    public function __construct(FaltaUnoGame $game)
    {
        $this->gameId = $game->id;
        $this->joined = $game->activeParticipants()->count();
        $this->needed = $game->players_needed;
        $this->status = $game->status;
    }

    public function broadcastOn(): array
    {
        return [new Channel('falta-uno-games')];
    }

    public function broadcastAs(): string
    {
        return 'participant.joined';
    }

    public function broadcastWith(): array
    {
        return [
            'game_id' => $this->gameId,
            'joined'  => $this->joined,
            'needed'  => $this->needed,
            'status'  => $this->status,
        ];
    }
}
