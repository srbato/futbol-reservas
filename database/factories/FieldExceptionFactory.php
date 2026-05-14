<?php

namespace Database\Factories;

use App\Models\Field;
use App\Models\FieldException;
use Illuminate\Database\Eloquent\Factories\Factory;

class FieldExceptionFactory extends Factory
{
    protected $model = FieldException::class;

    public function definition(): array
    {
        return [
            'field_id'   => Field::factory(),
            'date'       => now()->addDay()->toDateString(),
            'is_closed'  => false,
            'open_time'  => null,
            'close_time' => null,
            'note'       => null,
        ];
    }

    public function closed(): static
    {
        return $this->state(fn () => ['is_closed' => true]);
    }

    public function withSpecialHours(string $open, string $close): static
    {
        return $this->state(fn () => ['open_time' => $open, 'close_time' => $close]);
    }
}
