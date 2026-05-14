<?php

namespace Database\Factories;

use App\Models\Field;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        // Siempre a 48h del momento actual para no chocar con políticas de cancelación
        // de los venues (default 12h) ni con horarios pasados.
        $start = now()->addDays(2)->setTime(10, 0)->seconds(0);
        return [
            'field_id'          => Field::factory(),
            'user_id'           => User::factory()->state(['is_active' => true]),
            'start_at'          => $start,
            'end_at'            => $start->copy()->addHour(),
            'status'            => 'PAID',
            'total_amount'      => 5000,
            'currency'          => 'ARS',
            'verification_code' => strtoupper(Str::random(8)),
        ];
    }

    public function pendingPayment(int $expiresInMinutes = 10): static
    {
        return $this->state(fn () => [
            'status'     => 'PENDING_PAYMENT',
            'expires_at' => now()->addMinutes($expiresInMinutes),
        ]);
    }

    public function pendingCash(): static
    {
        return $this->state(fn () => ['status' => 'PENDING_CASH']);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => ['status' => 'CANCELLED', 'expires_at' => null]);
    }

    public function expired(): static
    {
        return $this->state(fn () => ['status' => 'EXPIRED', 'expires_at' => now()->subMinute()]);
    }

    public function startingAt(\Carbon\Carbon $start, int $minutes = 60): static
    {
        return $this->state(fn () => [
            'start_at' => $start,
            'end_at'   => $start->copy()->addMinutes($minutes),
        ]);
    }
}
