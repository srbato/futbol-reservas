<?php

namespace Tests\Feature;

use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * El admin/dueño confirma que el cliente pagó en efectivo en el complejo.
 * PENDING_CASH → PAID, deja una nota con quién confirmó y cuándo.
 */
class ConfirmCashPaymentTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    public function test_owner_can_confirm_cash_payment(): void
    {
        $venue = $this->makeVenue(['accepts_cash_payment' => true]);
        $field = $this->makeField($venue);
        $owner = $venue->owner;
        $reservation = Reservation::factory()->pendingCash()->create([
            'field_id' => $field->id,
            'user_id'  => $this->makeUser()->id,
        ]);

        $this->actingAs($owner)
            ->post(route('va.reservations.confirm_cash', $reservation))
            ->assertSessionHas('success');

        $r = $reservation->fresh();
        $this->assertSame('PAID', $r->status);
        $this->assertSame('approved', $r->payment_status);
        $this->assertStringContainsString('Pago en efectivo confirmado por', $r->notes);
        $this->assertStringContainsString($owner->name, $r->notes);
    }

    public function test_cannot_confirm_cash_for_reservation_not_in_pending_cash_status(): void
    {
        $field = $this->makeField();
        $owner = $field->venue->owner;
        $reservation = Reservation::factory()->create([ // PAID
            'field_id' => $field->id,
            'user_id'  => $this->makeUser()->id,
        ]);

        $this->actingAs($owner)
            ->post(route('va.reservations.confirm_cash', $reservation))
            ->assertSessionHas('error');

        $this->assertSame('PAID', $reservation->fresh()->status); // sin cambios
    }

    public function test_other_users_cannot_confirm_cash_for_reservation(): void
    {
        $field = $this->makeField();
        $reservation = Reservation::factory()->pendingCash()->create([
            'field_id' => $field->id,
            'user_id'  => $this->makeUser()->id,
        ]);
        $stranger = $this->makeUser(['role' => 'venue_admin']);

        $resp = $this->actingAs($stranger)
            ->post(route('va.reservations.confirm_cash', $reservation));

        $this->assertContains($resp->status(), [302, 403]);
        $this->assertSame('PENDING_CASH', $reservation->fresh()->status);
    }

    public function test_super_admin_can_confirm_cash_for_any_reservation(): void
    {
        $field = $this->makeField();
        $reservation = Reservation::factory()->pendingCash()->create([
            'field_id' => $field->id,
            'user_id'  => $this->makeUser()->id,
        ]);
        $sa = $this->makeUser(['role' => 'super_admin']);

        $this->actingAs($sa)
            ->post(route('va.reservations.confirm_cash', $reservation))
            ->assertSessionHas('success');

        $this->assertSame('PAID', $reservation->fresh()->status);
    }

    public function test_confirm_cash_appends_to_existing_notes(): void
    {
        $field = $this->makeField();
        $owner = $field->venue->owner;
        $reservation = Reservation::factory()->pendingCash()->create([
            'field_id' => $field->id,
            'user_id'  => $this->makeUser()->id,
            'notes'    => 'Cliente VIP',
        ]);

        $this->actingAs($owner)
            ->post(route('va.reservations.confirm_cash', $reservation))
            ->assertSessionHas('success');

        $notes = $reservation->fresh()->notes;
        $this->assertStringContainsString('Cliente VIP', $notes);
        $this->assertStringContainsString('|', $notes);
        $this->assertStringContainsString('Pago en efectivo confirmado', $notes);
    }
}
