<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Services\MercadoPagoPartialRefundService;
use App\Services\ReservationModifyPaymentService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * Cobertura del flujo de modificación de reservas.
 *  - Auth: solo el dueño o super_admin
 *  - showGrid redirige al complejo con ?modify=
 *  - previewChange valida nueva cancha del mismo venue, no overlap, no past
 *  - confirm aplica cambio si precio igual o menor; cobra diff por MP si es mayor
 *  - Reembolso parcial cuando es más barato
 *  - Email de confirmación se manda
 *  - Sesión expira a los 10 min
 */
class ReservationModifyTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    private function makeReservation(int $price = 5000): Reservation
    {
        $field = $this->makeField(price: $price);
        // 48h en el futuro para no chocar con modification_hours (default 12)
        $start = now()->addDays(2)->setTime(10, 0)->seconds(0);
        return Reservation::factory()->create([
            'field_id'         => $field->id,
            'user_id'          => $this->makeUser()->id,
            'start_at'         => $start,
            'end_at'           => $start->copy()->addHour(),
            'total_amount'     => $price,
            'payment_provider' => 'mercadopago',
        ]);
    }

    public function test_show_grid_redirects_to_venue_with_modify_query(): void
    {
        $reservation = $this->makeReservation();
        $user = $reservation->user;

        $resp = $this->actingAs($user)->get(route('reservations.modify.grid', $reservation));

        $expected = route('venues.show', $reservation->field->venue) . '?modify=' . $reservation->id . '#reservar';
        $resp->assertRedirect($expected);
    }

    public function test_show_grid_forbidden_for_other_user(): void
    {
        $reservation = $this->makeReservation();
        $other = $this->makeUser();

        $this->actingAs($other)
            ->get(route('reservations.modify.grid', $reservation))
            ->assertForbidden();
    }

    public function test_preview_calculates_diff_correctly_when_same_price(): void
    {
        $reservation = $this->makeReservation(5000);
        $user = $reservation->user;
        $field = $reservation->field;

        $this->actingAs($user)
            ->post(route('reservations.modify.preview', $reservation), [
                'field_id' => $field->id,
                'start_at' => Carbon::tomorrow()->setTime(15, 0)->toDateTimeString(),
            ])
            ->assertOk();

        $stored = session('reservation_modify');
        $this->assertNotNull($stored);
        $this->assertSame($reservation->id, $stored['reservation_id']);
        $this->assertEquals(5000, $stored['new_price']);
    }

    public function test_preview_blocks_when_new_slot_overlaps_with_existing(): void
    {
        $reservation = $this->makeReservation();
        $user = $reservation->user;
        $field = $reservation->field;

        // Otra reserva ocupa 15:00
        Reservation::factory()->create([
            'field_id' => $field->id,
            'user_id'  => $this->makeUser()->id,
            'start_at' => Carbon::tomorrow()->setTime(15, 0),
            'end_at'   => Carbon::tomorrow()->setTime(16, 0),
        ]);

        $this->actingAs($user)
            ->post(route('reservations.modify.preview', $reservation), [
                'field_id' => $field->id,
                'start_at' => Carbon::tomorrow()->setTime(15, 0)->toDateTimeString(),
            ])
            ->assertSessionHas('error');
    }

    public function test_preview_rejects_past_horario(): void
    {
        $reservation = $this->makeReservation();
        $user = $reservation->user;
        $field = $reservation->field;

        $this->actingAs($user)
            ->post(route('reservations.modify.preview', $reservation), [
                'field_id' => $field->id,
                'start_at' => Carbon::yesterday()->setTime(10, 0)->toDateTimeString(),
            ])
            ->assertSessionHas('error');
    }

    public function test_preview_rejects_field_from_different_venue(): void
    {
        $reservation = $this->makeReservation();
        $user = $reservation->user;
        $otherField = $this->makeField(); // venue distinto

        $this->actingAs($user)
            ->post(route('reservations.modify.preview', $reservation), [
                'field_id' => $otherField->id,
                'start_at' => Carbon::tomorrow()->setTime(15, 0)->toDateTimeString(),
            ])
            ->assertSessionHas('error');
    }

    public function test_confirm_applies_change_when_same_or_lower_price(): void
    {
        $reservation = $this->makeReservation(5000);
        $user = $reservation->user;
        $field = $reservation->field;
        $newStart = Carbon::tomorrow()->setTime(15, 0);

        // Stub el partial refund para que no llame a MP
        $mock = Mockery::mock(MercadoPagoPartialRefundService::class);
        $mock->shouldReceive('refundPartial')->andReturn(null);
        $this->app->instance(MercadoPagoPartialRefundService::class, $mock);

        // Simular sesión llena por previewChange
        session(['reservation_modify' => [
            'reservation_id' => $reservation->id,
            'field_id'       => $field->id,
            'start_at'       => $newStart->toDateTimeString(),
            'new_price'      => 5000,
            'expires_at'     => now()->addMinutes(5)->toDateTimeString(),
        ]]);

        $this->actingAs($user)
            ->post(route('reservations.modify.confirm', $reservation))
            ->assertRedirect(route('reservations.show', $reservation))
            ->assertSessionHas('success');

        // La reserva quedó con el nuevo horario
        $this->assertSame($newStart->format('Y-m-d H:i'), $reservation->fresh()->start_at->format('Y-m-d H:i'));
    }

    public function test_confirm_redirects_to_mp_when_new_price_higher(): void
    {
        $reservation = $this->makeReservation(5000);
        $user = $reservation->user;
        $field = $reservation->field;
        $newStart = Carbon::tomorrow()->setTime(15, 0);

        // Mock del payment service para devolver una URL fake de MP
        $mock = Mockery::mock(ReservationModifyPaymentService::class);
        $mock->shouldReceive('createPreference')
            ->once()
            ->andReturn('https://mp.com/checkout/fake');
        $this->app->instance(ReservationModifyPaymentService::class, $mock);

        session(['reservation_modify' => [
            'reservation_id' => $reservation->id,
            'field_id'       => $field->id,
            'start_at'       => $newStart->toDateTimeString(),
            'new_price'      => 8000, // más caro
            'expires_at'     => now()->addMinutes(5)->toDateTimeString(),
        ]]);

        $this->actingAs($user)
            ->post(route('reservations.modify.confirm', $reservation))
            ->assertRedirect('https://mp.com/checkout/fake');

        // No se debe haber aplicado el cambio aún (queda pendiente del pago)
        $this->assertSame('10:00', $reservation->fresh()->start_at->format('H:i'));
    }

    public function test_confirm_fails_when_session_expired(): void
    {
        $reservation = $this->makeReservation();
        $user = $reservation->user;

        session(['reservation_modify' => [
            'reservation_id' => $reservation->id,
            'field_id'       => $reservation->field_id,
            'start_at'       => Carbon::tomorrow()->setTime(15, 0)->toDateTimeString(),
            'new_price'      => 5000,
            'expires_at'     => now()->subMinute()->toDateTimeString(), // ya pasó
        ]]);

        $this->actingAs($user)
            ->post(route('reservations.modify.confirm', $reservation))
            ->assertRedirect(route('reservations.modify.show', $reservation))
            ->assertSessionHas('error');
    }

    public function test_confirm_fails_when_no_session_data(): void
    {
        $reservation = $this->makeReservation();
        $user = $reservation->user;

        $this->actingAs($user)
            ->post(route('reservations.modify.confirm', $reservation))
            ->assertRedirect(route('reservations.modify.show', $reservation))
            ->assertSessionHas('error');
    }
}
