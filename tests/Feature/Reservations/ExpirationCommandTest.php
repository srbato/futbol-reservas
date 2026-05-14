<?php

namespace Tests\Feature\Reservations;

use App\Models\FaltaUnoGame;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * Cobertura de los commands de scheduler que mantienen la salud de las reservas:
 *  - reservations:expire — PENDING_PAYMENT vencidas → EXPIRED
 *  - reservations:purge-dead — borra CANCELLED/EXPIRED viejas (>2 meses)
 *  - dry-run no modifica nada
 *  - Falta Uno games asociados a reservas expiradas también se cancelan
 */
class ExpirationCommandTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    public function test_expire_command_marks_pending_payment_with_expired_at_as_expired(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();

        // 2 reservas vencidas + 1 vigente
        $expired1 = Reservation::factory()->pendingPayment()->create([
            'field_id' => $field->id, 'user_id' => $user->id, 'expires_at' => now()->subMinutes(5),
        ]);
        $expired2 = Reservation::factory()->pendingPayment()->create([
            'field_id' => $field->id, 'user_id' => $user->id, 'expires_at' => now()->subHours(2),
        ]);
        $alive = Reservation::factory()->pendingPayment()->create([
            'field_id' => $field->id, 'user_id' => $user->id, 'expires_at' => now()->addMinutes(5),
        ]);

        $this->artisan('reservations:expire')->assertSuccessful();

        $this->assertSame('EXPIRED', $expired1->fresh()->status);
        $this->assertSame('EXPIRED', $expired2->fresh()->status);
        $this->assertSame('PENDING_PAYMENT', $alive->fresh()->status);
    }

    public function test_expire_command_does_not_touch_paid_reservations(): void
    {
        $field = $this->makeField();
        $paid = Reservation::factory()->create([
            'field_id'   => $field->id,
            'user_id'    => $this->makeUser()->id,
            'status'     => 'PAID',
            'expires_at' => now()->subDay(), // por algún motivo, expires_at vencido
        ]);

        $this->artisan('reservations:expire')->assertSuccessful();

        $this->assertSame('PAID', $paid->fresh()->status);
    }

    public function test_dry_run_does_not_modify_reservations(): void
    {
        $field = $this->makeField();
        $r = Reservation::factory()->pendingPayment()->create([
            'field_id'   => $field->id,
            'user_id'    => $this->makeUser()->id,
            'expires_at' => now()->subMinutes(5),
        ]);

        $this->artisan('reservations:expire', ['--dry-run' => true])->assertSuccessful();

        $this->assertSame('PENDING_PAYMENT', $r->fresh()->status);
    }

    public function test_expire_command_cancels_falta_uno_game_associated_with_expired_reservation(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $reservation = Reservation::factory()->pendingPayment()->create([
            'field_id'   => $field->id,
            'user_id'    => $user->id,
            'expires_at' => now()->subMinutes(5),
        ]);
        $game = FaltaUnoGame::factory()->create([
            'field_id'       => $field->id,
            'reservation_id' => $reservation->id,
            'status'         => 'open',
        ]);

        $this->artisan('reservations:expire')->assertSuccessful();

        $this->assertSame('EXPIRED', $reservation->fresh()->status);
        $this->assertSame('cancelled', $game->fresh()->status);
        $this->assertNotNull($game->fresh()->cancelled_at);
    }

    public function test_purge_dead_deletes_cancelled_and_expired_older_than_2_months(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();

        $oldCancelled = Reservation::factory()->cancelled()->create([
            'field_id' => $field->id, 'user_id' => $user->id,
            'updated_at' => now()->subMonths(3),
        ]);
        $recentCancelled = Reservation::factory()->cancelled()->create([
            'field_id' => $field->id, 'user_id' => $user->id,
            'updated_at' => now()->subDays(10),
        ]);
        $oldExpired = Reservation::factory()->expired()->create([
            'field_id' => $field->id, 'user_id' => $user->id,
            'updated_at' => now()->subMonths(4),
        ]);
        $alivePaid = Reservation::factory()->create([
            'field_id' => $field->id, 'user_id' => $user->id,
            'updated_at' => now()->subYear(), // PAID viejos NO se purgan
        ]);

        $this->artisan('reservations:purge-dead')->assertSuccessful();

        $this->assertNull(Reservation::find($oldCancelled->id));
        $this->assertNull(Reservation::find($oldExpired->id));
        $this->assertNotNull(Reservation::find($recentCancelled->id));
        $this->assertNotNull(Reservation::find($alivePaid->id));
    }
}
