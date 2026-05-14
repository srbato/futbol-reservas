<?php

namespace Database\Factories;

use App\Models\FaltaUnoGame;
use App\Models\Field;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FaltaUnoGame>
 */
class FaltaUnoGameFactory extends Factory
{
    protected $model = FaltaUnoGame::class;

    public function definition(): array
    {
        $start = now()->addDay()->setTime(20, 0);
        return [
            'field_id'          => Field::factory(),
            'reservation_id'    => null,
            'initiator_user_id' => User::factory()->state(['is_active' => true]),
            'total_players'     => 10,
            'initiator_players' => 2,
            'players_needed'    => 8,
            'status'            => 'open',
            'start_at'          => $start,
            'amount_paid'       => 5000,
        ];
    }

    public function full(): static
    {
        return $this->state(fn () => ['status' => 'full', 'players_needed' => 0]);
    }

    public function finished(): static
    {
        return $this->state(fn () => [
            'status'   => 'finished',
            'start_at' => now()->subDay()->setTime(20, 0),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => ['status' => 'cancelled', 'cancelled_at' => now()]);
    }
}
