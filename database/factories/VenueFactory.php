<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Venue>
 */
class VenueFactory extends Factory
{
    protected $model = Venue::class;

    public function definition(): array
    {
        return [
            'owner_user_id'      => User::factory()->state(['role' => 'super_admin', 'is_active' => true]),
            'name'               => fake()->company(),
            'address'            => fake()->streetAddress(),
            'zone'               => fake()->city(),
            'phone'              => '+5491100000000',
            'is_active'          => true,
            'cancellation_hours' => 12,
            'modification_hours' => 12,
            'recurring_payment_mode' => 'upfront',
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function subscriptionMode(): static
    {
        return $this->state(fn () => ['recurring_payment_mode' => 'subscription']);
    }
}
