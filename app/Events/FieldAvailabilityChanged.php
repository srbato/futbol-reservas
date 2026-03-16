<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class FieldAvailabilityChanged implements ShouldBroadcastNow
{
    public function __construct(
        public readonly int    $fieldId,
        public readonly string $date,
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel("field.{$this->fieldId}")];
    }

    public function broadcastAs(): string
    {
        return 'availability.changed';
    }
}
