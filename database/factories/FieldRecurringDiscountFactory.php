<?php

namespace Database\Factories;

use App\Models\Field;
use App\Models\FieldRecurringDiscount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FieldRecurringDiscount>
 */
class FieldRecurringDiscountFactory extends Factory
{
    protected $model = FieldRecurringDiscount::class;

    public function definition(): array
    {
        return [
            'field_id'            => Field::factory(),
            'min_occurrences'     => 4,
            'discount_percentage' => 10,
            'is_active'           => true,
        ];
    }
}
