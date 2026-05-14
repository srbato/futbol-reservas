<?php

namespace Database\Factories;

use App\Models\FaltaUnoGame;
use App\Models\FaltaUnoParticipant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FaltaUnoParticipant>
 */
class FaltaUnoParticipantFactory extends Factory
{
    protected $model = FaltaUnoParticipant::class;

    public function definition(): array
    {
        return [
            'game_id'       => FaltaUnoGame::factory(),
            'user_id'       => User::factory()->state(['is_active' => true]),
            'status'        => 'confirmed',
            'is_late_leave' => false,
            'was_kicked'    => false,
        ];
    }

    public function noShow(): static
    {
        return $this->state(fn () => ['status' => 'no_show', 'no_show_at' => now()]);
    }

    public function lateLeave(): static
    {
        return $this->state(fn () => ['is_late_leave' => true, 'left_at' => now()]);
    }

    public function withResult(string $result): static
    {
        // result: 'win' | 'draw' | 'loss'
        return $this->state(fn () => ['result' => $result, 'stats_submitted_at' => now()]);
    }
}
