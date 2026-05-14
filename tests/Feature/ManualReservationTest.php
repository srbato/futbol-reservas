<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * Cobertura del flujo de reserva manual del admin (cargada por el dueño/staff
 * para clientes que pagan en efectivo o WhatsApp).
 *  - Crea una reserva PAID directamente con payment_provider=manual
 *  - Sólo lo puede hacer el dueño del complejo o super_admin
 *  - Validaciones de date/time/field/amount
 *  - Anti-overlap con reservas existentes
 *  - Notas opcionales
 *  - Asignación de cliente vía client_user_id (autocompletado)
 */
class ManualReservationTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    public function test_owner_can_create_manual_reservation_without_assigned_client(): void
    {
        $field = $this->makeField();
        $owner = $field->venue->owner;

        $this->actingAs($owner)
            ->post(route('va.reservations.manual_store'), [
                'field_id'    => $field->id,
                'date'        => Carbon::tomorrow()->toDateString(),
                'time'        => '14:00',
                'amount_paid' => 5000,
                'notes'       => 'Cliente recurrente, pagó por transferencia',
            ])
            ->assertRedirect(route('va.reservations.index', ['date' => Carbon::tomorrow()->toDateString()]))
            ->assertSessionHas('success');

        $r = Reservation::first();
        $this->assertSame('PAID', $r->status);
        $this->assertSame('manual', $r->payment_provider);
        $this->assertEquals(5000, $r->total_amount);
        $this->assertSame('Cliente recurrente, pagó por transferencia', $r->notes);
        $this->assertSame($owner->id, $r->user_id); // sin client_user_id, queda asignada al admin
    }

    public function test_owner_can_create_manual_reservation_assigned_to_client(): void
    {
        $field  = $this->makeField();
        $owner  = $field->venue->owner;
        $client = $this->makeUser();

        $this->actingAs($owner)
            ->post(route('va.reservations.manual_store'), [
                'field_id'       => $field->id,
                'date'           => Carbon::tomorrow()->toDateString(),
                'time'           => '15:00',
                'client_user_id' => $client->id,
                'amount_paid'    => 4500,
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reservations', [
            'field_id' => $field->id,
            'user_id'  => $client->id,
            'status'   => 'PAID',
        ]);
    }

    public function test_other_users_cannot_create_manual_reservation_in_someone_elses_venue(): void
    {
        $field   = $this->makeField();
        $stranger = $this->makeUser(['role' => 'venue_admin']);

        $resp = $this->actingAs($stranger)
            ->post(route('va.reservations.manual_store'), [
                'field_id'    => $field->id,
                'date'        => Carbon::tomorrow()->toDateString(),
                'time'        => '14:00',
                'amount_paid' => 5000,
            ]);

        // Puede ser 403 (controller authz) o 302 (middleware venue.onboarding redirige a otro lado).
        // Lo importante es que NO se haya creado la reserva.
        $this->assertContains($resp->status(), [302, 403, 419]);
        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_validates_required_fields(): void
    {
        $field = $this->makeField();
        $owner = $field->venue->owner;

        $this->actingAs($owner)
            ->post(route('va.reservations.manual_store'), [])
            ->assertSessionHasErrors(['field_id', 'date', 'time']);
    }

    public function test_rejects_past_dates(): void
    {
        $field = $this->makeField();
        $owner = $field->venue->owner;

        $this->actingAs($owner)
            ->post(route('va.reservations.manual_store'), [
                'field_id'    => $field->id,
                'date'        => Carbon::yesterday()->toDateString(),
                'time'        => '14:00',
                'amount_paid' => 5000,
            ])
            ->assertSessionHasErrors(['date']);
    }

    public function test_rejects_invalid_time_format(): void
    {
        $field = $this->makeField();
        $owner = $field->venue->owner;

        $this->actingAs($owner)
            ->post(route('va.reservations.manual_store'), [
                'field_id'    => $field->id,
                'date'        => Carbon::tomorrow()->toDateString(),
                'time'        => '14:00:00', // formato H:i:s, debería ser H:i
                'amount_paid' => 5000,
            ])
            ->assertSessionHasErrors(['time']);
    }

    public function test_rejects_negative_amount(): void
    {
        $field = $this->makeField();
        $owner = $field->venue->owner;

        $this->actingAs($owner)
            ->post(route('va.reservations.manual_store'), [
                'field_id'    => $field->id,
                'date'        => Carbon::tomorrow()->toDateString(),
                'time'        => '14:00',
                'amount_paid' => -100,
            ])
            ->assertSessionHasErrors(['amount_paid']);
    }

    public function test_rejects_amount_too_large(): void
    {
        $field = $this->makeField();
        $owner = $field->venue->owner;

        $this->actingAs($owner)
            ->post(route('va.reservations.manual_store'), [
                'field_id'    => $field->id,
                'date'        => Carbon::tomorrow()->toDateString(),
                'time'        => '14:00',
                'amount_paid' => 999_999_999,
            ])
            ->assertSessionHasErrors(['amount_paid']);
    }

    public function test_cannot_create_when_overlap_with_existing_active_reservation(): void
    {
        $field = $this->makeField();
        $owner = $field->venue->owner;
        $tomorrow = Carbon::tomorrow();

        Reservation::factory()->create([
            'field_id' => $field->id,
            'user_id'  => $this->makeUser()->id,
            'start_at' => $tomorrow->copy()->setTime(14, 0),
            'end_at'   => $tomorrow->copy()->setTime(15, 0),
        ]);

        $this->actingAs($owner)
            ->post(route('va.reservations.manual_store'), [
                'field_id'    => $field->id,
                'date'        => $tomorrow->toDateString(),
                'time'        => '14:00',
                'amount_paid' => 5000,
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('reservations', 1);
    }

    public function test_user_search_endpoint_returns_matching_users(): void
    {
        $field  = $this->makeField();
        $owner  = $field->venue->owner;
        $matchingUser = $this->makeUser(['name' => 'Juan Pérez', 'email' => 'juanperez@test.com']);
        $this->makeUser(['name' => 'María López', 'email' => 'maria@test.com']);

        $this->actingAs($owner)
            ->getJson(route('va.users.search') . '?q=juan')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['email' => 'juanperez@test.com']);
    }

    public function test_user_search_requires_at_least_3_chars(): void
    {
        $field = $this->makeField();
        $owner = $field->venue->owner;

        $this->actingAs($owner)
            ->getJson(route('va.users.search') . '?q=ju')
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_user_search_does_not_return_inactive_users(): void
    {
        $field = $this->makeField();
        $owner = $field->venue->owner;
        $this->makeUser(['name' => 'Inactive User', 'email' => 'inactive@test.com', 'is_active' => false]);

        $this->actingAs($owner)
            ->getJson(route('va.users.search') . '?q=inactive')
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_unauthenticated_users_cannot_create_manual_reservation(): void
    {
        $field = $this->makeField();

        $this->post(route('va.reservations.manual_store'), [
            'field_id'    => $field->id,
            'date'        => Carbon::tomorrow()->toDateString(),
            'time'        => '14:00',
            'amount_paid' => 5000,
        ])->assertRedirect(route('login'));
    }
}
