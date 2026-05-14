<?php

namespace Tests\Feature;

use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * Cobertura del flujo "pagar en efectivo en el complejo":
 *  - Cambia PENDING_PAYMENT → PENDING_CASH (sin expiración)
 *  - Sólo lo puede hacer el dueño de la reserva o un super_admin
 *  - Sólo si el complejo tiene accepts_cash_payment = true
 *  - Sólo si la reserva está en PENDING_PAYMENT y no expiró
 */
class CashPaymentTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    public function test_user_can_switch_pending_payment_to_pending_cash_when_venue_accepts_cash(): void
    {
        $venue = $this->makeVenue(['accepts_cash_payment' => true]);
        $field = $this->makeField($venue);
        $user  = $this->makeUser();
        $reservation = Reservation::factory()->pendingPayment()->create([
            'field_id' => $field->id,
            'user_id'  => $user->id,
        ]);

        $this->actingAs($user)
            ->post(route('reservations.pay_cash', $reservation))
            ->assertRedirect(route('reservations.show', $reservation));

        $r = $reservation->fresh();
        $this->assertSame('PENDING_CASH', $r->status);
        $this->assertSame('cash', $r->payment_provider);
        $this->assertNull($r->expires_at);
    }

    public function test_cannot_pay_cash_when_venue_does_not_accept_cash(): void
    {
        $venue = $this->makeVenue(['accepts_cash_payment' => false]);
        $field = $this->makeField($venue);
        $user  = $this->makeUser();
        $reservation = Reservation::factory()->pendingPayment()->create([
            'field_id' => $field->id,
            'user_id'  => $user->id,
        ]);

        $this->actingAs($user)
            ->post(route('reservations.pay_cash', $reservation))
            ->assertStatus(422);

        $this->assertSame('PENDING_PAYMENT', $reservation->fresh()->status);
    }

    public function test_cannot_pay_cash_for_someone_elses_reservation(): void
    {
        $venue = $this->makeVenue(['accepts_cash_payment' => true]);
        $field = $this->makeField($venue);
        $reservation = Reservation::factory()->pendingPayment()->create([
            'field_id' => $field->id,
            'user_id'  => $this->makeUser()->id,
        ]);
        $other = $this->makeUser();

        $this->actingAs($other)
            ->post(route('reservations.pay_cash', $reservation))
            ->assertForbidden();
    }

    public function test_super_admin_can_switch_any_reservation_to_pending_cash(): void
    {
        $venue = $this->makeVenue(['accepts_cash_payment' => true]);
        $field = $this->makeField($venue);
        $reservation = Reservation::factory()->pendingPayment()->create([
            'field_id' => $field->id,
            'user_id'  => $this->makeUser()->id,
        ]);
        $sa = $this->makeUser(['role' => 'super_admin']);

        $this->actingAs($sa)
            ->post(route('reservations.pay_cash', $reservation))
            ->assertRedirect();

        $this->assertSame('PENDING_CASH', $reservation->fresh()->status);
    }

    public function test_cannot_pay_cash_if_reservation_already_paid(): void
    {
        $venue = $this->makeVenue(['accepts_cash_payment' => true]);
        $field = $this->makeField($venue);
        $user  = $this->makeUser();
        $reservation = Reservation::factory()->create([ // PAID por default
            'field_id' => $field->id,
            'user_id'  => $user->id,
        ]);

        $this->actingAs($user)
            ->post(route('reservations.pay_cash', $reservation))
            ->assertStatus(422);
    }

    public function test_cannot_pay_cash_if_reservation_already_cancelled(): void
    {
        $venue = $this->makeVenue(['accepts_cash_payment' => true]);
        $field = $this->makeField($venue);
        $user  = $this->makeUser();
        $reservation = Reservation::factory()->cancelled()->create([
            'field_id' => $field->id,
            'user_id'  => $user->id,
        ]);

        $this->actingAs($user)
            ->post(route('reservations.pay_cash', $reservation))
            ->assertStatus(422);
    }

    public function test_cannot_pay_cash_if_reservation_pending_already_expired(): void
    {
        $venue = $this->makeVenue(['accepts_cash_payment' => true]);
        $field = $this->makeField($venue);
        $user  = $this->makeUser();
        $reservation = Reservation::factory()->create([
            'field_id'    => $field->id,
            'user_id'     => $user->id,
            'status'      => 'PENDING_PAYMENT',
            'expires_at'  => now()->subMinutes(5),
        ]);

        $this->actingAs($user)
            ->post(route('reservations.pay_cash', $reservation))
            ->assertStatus(422);
    }

    public function test_unauthenticated_users_cannot_use_cash_endpoint(): void
    {
        $venue = $this->makeVenue(['accepts_cash_payment' => true]);
        $field = $this->makeField($venue);
        $reservation = Reservation::factory()->pendingPayment()->create([
            'field_id' => $field->id,
            'user_id'  => $this->makeUser()->id,
        ]);

        $this->post(route('reservations.pay_cash', $reservation))
            ->assertRedirect(route('login'));
    }
}
