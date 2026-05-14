<?php

namespace Database\Factories;

use App\Models\Field;
use App\Models\FieldPrice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FieldPrice>
 */
class FieldPriceFactory extends Factory
{
    protected $model = FieldPrice::class;

    public function definition(): array
    {
        return [
            'field_id'             => Field::factory(),
            'price_per_slot'       => 5000,
            'currency'             => 'ARS',
            'night_price_per_slot' => null,
            'night_start_time'     => null,
            'night_end_time'       => null,
        ];
    }

    public function withNightPrice(float $price = 7000, string $start = '20:00', string $end = '23:00'): static
    {
        return $this->state(fn () => [
            'night_price_per_slot' => $price,
            'night_start_time'     => $start,
            'night_end_time'       => $end,
        ]);
    }
}
