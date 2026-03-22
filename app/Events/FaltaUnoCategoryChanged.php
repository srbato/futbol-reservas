<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FaltaUnoCategoryChanged implements ShouldBroadcast
{
    public function __construct(
        public readonly User   $user,
        public readonly string $sport,
        public readonly string $oldCategory,
        public readonly string $newCategory,
        public readonly string $direction,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("user.{$this->user->id}")];
    }

    public function broadcastAs(): string
    {
        return 'category.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'sport'        => $this->sport,
            'old_category' => $this->oldCategory,
            'new_category' => $this->newCategory,
            'direction'    => $this->direction,
        ];
    }
}
