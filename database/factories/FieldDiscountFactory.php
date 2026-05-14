<?php

namespace Database\Factories;

use App\Models\Field;
use App\Models\FieldDiscount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FieldDiscount>
 */
class FieldDiscountFactory extends Factory
{
    protected $model = FieldDiscount::class;

    public function definition(): array
    {
        return [
            'field_id'       => Field::factory(),
            'day_of_week'    => null,
            'date'           => null,
            'start_time'     => null,
            'end_time'       => null,
            'discount_price' => 3000,
            'label'          => 'OFERTA',
            'is_active'      => true,
        ];
    }

    public function forDayOfWeek(int $dow): static
    {
        return $this->state(fn () => ['day_of_week' => $dow]);
    }

    public function forDate(string $date): static
    {
        return $this->state(fn () => ['date' => $date]);
    }

    public function inTimeRange(string $start, string $end): static
    {
        return $this->state(fn () => ['start_time' => $start, 'end_time' => $end]);
    }
}
