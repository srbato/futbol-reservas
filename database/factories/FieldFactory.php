<?php

namespace Database\Factories;

use App\Models\Field;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Field>
 */
class FieldFactory extends Factory
{
    protected $model = Field::class;

    public function definition(): array
    {
        return [
            'venue_id'     => Venue::factory(),
            'name'         => 'Cancha ' . fake()->numberBetween(1, 9),
            'sport'        => 'football',
            'format'       => '5',
            'slot_minutes' => 60,
            'is_indoor'    => false,
            'is_active'    => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function withSlotMinutes(int $minutes): static
    {
        return $this->state(fn () => ['slot_minutes' => $minutes]);
    }
}
