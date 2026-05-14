<?php

namespace Database\Factories;

use App\Models\FaltaUnoGame;
use App\Models\FaltaUnoRating;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FaltaUnoRating>
 */
class FaltaUnoRatingFactory extends Factory
{
    protected $model = FaltaUnoRating::class;

    public function definition(): array
    {
        return [
            'game_id'       => FaltaUnoGame::factory(),
            'rater_user_id' => User::factory()->state(['is_active' => true]),
            'rated_user_id' => User::factory()->state(['is_active' => true]),
            'assessment'    => 'match',
            'comment'       => null,
        ];
    }

    public function above(): static  { return $this->state(fn () => ['assessment' => 'above']); }
    public function below(): static  { return $this->state(fn () => ['assessment' => 'below']); }
    public function match(): static  { return $this->state(fn () => ['assessment' => 'match']); }
}
