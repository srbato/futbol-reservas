<?php

namespace Database\Factories;

use App\Models\FaltaUnoSportProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FaltaUnoSportProfile>
 */
class FaltaUnoSportProfileFactory extends Factory
{
    protected $model = FaltaUnoSportProfile::class;

    public function definition(): array
    {
        return [
            'user_id'           => User::factory()->state(['is_active' => true]),
            'sport'             => 'football',
            'category'          => 'medio',
            'gender'            => 'male',
            'games_played'      => 0,
            'wins'              => 0,
            'draws'             => 0,
            'losses'            => 0,
            'average_rating'    => 0,
            'attendance_rate'   => 100,
            'late_leaves_count' => 0,
        ];
    }
}
