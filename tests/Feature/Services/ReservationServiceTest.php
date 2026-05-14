<?php

namespace Tests\Feature\Services;

use App\Exceptions\VenueBlockedException;
use App\Models\FieldBlock;
use App\Models\FieldDiscount;
use App\Models\FieldPrice;
use App\Models\Reservation;
use App\Models\VenueUserBlock;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * Cobertura del corazón del flujo de reservas: precios, descuentos,
 * validaciones de horario, bloqueos, locking y conflictos de overlap.
 *
 * Si algún test falla, NO se ajusta el test: hay que arreglar la lógica subyacente.
 */
class ReservationServiceTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    private ReservationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ReservationService::class);
    }

    // ─── PRECIO ──────────────────────────────────────────────────────────

    public function test_calculate_price_returns_base_price_when_no_modifiers(): void
    {
        $field = $this->makeField(price: 5000);
        $start = Carbon::tomorrow()->setTime(10, 0);

        $this->assertSame(5000.0, $this->service->calculatePrice($field->fresh(['price', 'discounts']), $start));
    }

    public function test_calculate_price_uses_night_price_when_in_night_window(): void
    {
        $field = $this->makeField(price: 5000);
        $field->price->update([
            'night_price_per_slot' => 7500,
            'night_start_time'     => '20:00',
            'night_end_time'       => '23:00',
        ]);
        $start = Carbon::tomorrow()->setTime(21, 0);

        $this->assertSame(7500.0, $this->service->calculatePrice($field->fresh(['price', 'discounts']), $start));
    }

    public function test_calculate_price_keeps_base_when_outside_night_window(): void
    {
        $field = $this->makeField(price: 5000);
        $field->price->update([
            'night_price_per_slot' => 7500,
            'night_start_time'     => '20:00',
            'night_end_time'       => '23:00',
        ]);
        $start = Carbon::tomorrow()->setTime(15, 0);

        $this->assertSame(5000.0, $this->service->calculatePrice($field->fresh(['price', 'discounts']), $start));
    }

    public function test_calculate_price_applies_active_discount_for_specific_date(): void
    {
        $field = $this->makeField(price: 5000);
        $tomorrow = Carbon::tomorrow();

        FieldDiscount::factory()->create([
            'field_id'       => $field->id,
            'date'           => $tomorrow->toDateString(),
            'discount_price' => 3000,
            'label'          => 'PROMO',
            'is_active'      => true,
        ]);

        $this->assertSame(3000.0, $this->service->calculatePrice($field->fresh(['price', 'discounts']), $tomorrow->copy()->setTime(10, 0)));
    }

    public function test_calculate_price_picks_lowest_discount_when_multiple_apply(): void
    {
        $field = $this->makeField(price: 5000);
        $tomorrow = Carbon::tomorrow();

        FieldDiscount::factory()->create([
            'field_id'       => $field->id,
            'date'           => $tomorrow->toDateString(),
            'discount_price' => 4000,
            'is_active'      => true,
        ]);
        FieldDiscount::factory()->create([
            'field_id'       => $field->id,
            'date'           => $tomorrow->toDateString(),
            'discount_price' => 2500,
            'is_active'      => true,
        ]);

        // Debe elegir el más bajo (2500) — mejor para el usuario
        $this->assertSame(2500.0, $this->service->calculatePrice($field->fresh(['price', 'discounts']), $tomorrow->copy()->setTime(10, 0)));
    }

    public function test_calculate_price_ignores_discount_that_would_increase_price(): void
    {
        $field = $this->makeField(price: 5000);
        $tomorrow = Carbon::tomorrow();

        FieldDiscount::factory()->create([
            'field_id'       => $field->id,
            'date'           => $tomorrow->toDateString(),
            'discount_price' => 7000, // mayor al base — NO debe aplicarse
            'is_active'      => true,
        ]);

        $this->assertSame(5000.0, $this->service->calculatePrice($field->fresh(['price', 'discounts']), $tomorrow->copy()->setTime(10, 0)));
    }

    public function test_calculate_price_ignores_inactive_discount(): void
    {
        $field = $this->makeField(price: 5000);
        $tomorrow = Carbon::tomorrow();

        FieldDiscount::factory()->create([
            'field_id'       => $field->id,
            'date'           => $tomorrow->toDateString(),
            'discount_price' => 1000,
            'is_active'      => false,
        ]);

        $this->assertSame(5000.0, $this->service->calculatePrice($field->fresh(['price', 'discounts']), $tomorrow->copy()->setTime(10, 0)));
    }

    public function test_calculate_price_discount_by_day_of_week_only_applies_that_dow(): void
    {
        $field = $this->makeField(price: 5000);
        $tomorrow = Carbon::tomorrow();

        // Descuento sólo para el dow de mañana
        FieldDiscount::factory()->create([
            'field_id'       => $field->id,
            'day_of_week'    => $tomorrow->dayOfWeek,
            'discount_price' => 2000,
            'is_active'      => true,
        ]);

        $this->assertSame(2000.0, $this->service->calculatePrice($field->fresh(['price', 'discounts']), $tomorrow->copy()->setTime(10, 0)));

        // Otro día: sin descuento
        $otherDay = $tomorrow->copy()->addDays(($tomorrow->dayOfWeek + 3) % 7 + 1);
        $this->assertSame(5000.0, $this->service->calculatePrice($field->fresh(['price', 'discounts']), $otherDay->setTime(10, 0)));
    }

    public function test_calculate_price_discount_in_time_range_only_applies_within_range(): void
    {
        $field = $this->makeField(price: 5000);
        $tomorrow = Carbon::tomorrow();

        FieldDiscount::factory()->create([
            'field_id'       => $field->id,
            'date'           => $tomorrow->toDateString(),
            'start_time'     => '10:00',
            'end_time'       => '14:00',
            'discount_price' => 2500,
            'is_active'      => true,
        ]);

        // 11:00 está dentro del rango → descuento aplica
        $this->assertSame(2500.0, $this->service->calculatePrice($field->fresh(['price', 'discounts']), $tomorrow->copy()->setTime(11, 0)));
        // 16:00 fuera del rango → sin descuento
        $this->assertSame(5000.0, $this->service->calculatePrice($field->fresh(['price', 'discounts']), $tomorrow->copy()->setTime(16, 0)));
    }

    // ─── createSingle: VALIDACIONES ───────────────────────────────────────

    public function test_create_single_aborts_when_field_inactive(): void
    {
        $field = $this->makeField();
        $field->update(['is_active' => false]);
        $user = $this->makeUser();
        $start = Carbon::tomorrow()->setTime(10, 0);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('La cancha no está activa');

        $this->service->createSingle($field->fresh(['venue', 'price', 'schedules']), $start, $user->id);
    }

    public function test_create_single_aborts_when_venue_inactive(): void
    {
        $field = $this->makeField();
        $field->venue->update(['is_active' => false]);
        $user = $this->makeUser();
        $start = Carbon::tomorrow()->setTime(10, 0);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('El complejo no está activo');

        $this->service->createSingle($field->fresh(['venue', 'price', 'schedules']), $start, $user->id);
    }

    public function test_create_single_aborts_for_past_horarios(): void
    {
        $field = $this->makeField();
        $user = $this->makeUser();
        $start = Carbon::yesterday()->setTime(10, 0);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('horarios pasados');

        $this->service->createSingle($field, $start, $user->id);
    }

    public function test_create_single_aborts_when_outside_schedule_range(): void
    {
        $field = $this->makeField(); // schedule 08:00-22:00
        $user = $this->makeUser();
        $start = Carbon::tomorrow()->setTime(23, 0); // fuera

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('fuera del rango disponible');

        $this->service->createSingle($field, $start, $user->id);
    }

    public function test_create_single_aborts_when_field_has_no_schedule_for_dow(): void
    {
        $field = $this->makeField();
        // Borrar schedule del día de mañana
        $field->schedules()->where('day_of_week', Carbon::tomorrow()->dayOfWeek)->delete();
        $user = $this->makeUser();
        $start = Carbon::tomorrow()->setTime(10, 0);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('horario configurado');

        $this->service->createSingle($field->fresh(['venue', 'price', 'schedules']), $start, $user->id);
    }

    public function test_create_single_aborts_when_blocked_by_field_block(): void
    {
        $field = $this->makeField();
        $user = $this->makeUser();
        $tomorrow = Carbon::tomorrow();

        FieldBlock::create([
            'field_id'   => $field->id,
            'date'       => $tomorrow->toDateString(),
            'start_time' => '09:00',
            'end_time'   => '12:00',
            'reason'     => 'Mantenimiento',
        ]);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('bloqueado');

        $this->service->createSingle($field->fresh(['venue', 'price', 'schedules', 'blocks']), $tomorrow->copy()->setTime(10, 0), $user->id);
    }

    public function test_create_single_throws_venue_blocked_for_blocked_user(): void
    {
        $field = $this->makeField();
        $user = $this->makeUser();
        $tomorrow = Carbon::tomorrow();

        VenueUserBlock::create([
            'venue_id'   => $field->venue_id,
            'user_id'    => $user->id,
            'reason'     => 'Conducta',
            'blocked_by' => $field->venue->owner_user_id,
        ]);

        $this->expectException(VenueBlockedException::class);
        $this->service->createSingle($field, $tomorrow->copy()->setTime(10, 0), $user->id);
    }

    public function test_create_single_aborts_when_overlap_with_paid_reservation(): void
    {
        $field = $this->makeField();
        $userA = $this->makeUser();
        $userB = $this->makeUser();
        $tomorrow = Carbon::tomorrow();

        Reservation::factory()->create([
            'field_id' => $field->id,
            'user_id'  => $userA->id,
            'start_at' => $tomorrow->copy()->setTime(10, 0),
            'end_at'   => $tomorrow->copy()->setTime(11, 0),
        ]);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('ya está reservado');

        $this->service->createSingle($field->fresh(['venue', 'price', 'schedules']), $tomorrow->copy()->setTime(10, 0), $userB->id);
    }

    public function test_create_single_allows_reservation_after_pending_payment_expires(): void
    {
        $field = $this->makeField();
        $userA = $this->makeUser();
        $userB = $this->makeUser();
        $tomorrow = Carbon::tomorrow();

        // Reserva pendiente pero ya expirada
        Reservation::factory()->pendingPayment(-5)->create([
            'field_id' => $field->id,
            'user_id'  => $userA->id,
            'start_at' => $tomorrow->copy()->setTime(10, 0),
            'end_at'   => $tomorrow->copy()->setTime(11, 0),
            'expires_at' => now()->subMinutes(5),
        ]);

        // userB debería poder reservar el mismo slot
        $reservation = $this->service->createSingle(
            $field->fresh(['venue', 'price', 'schedules']),
            $tomorrow->copy()->setTime(10, 0),
            $userB->id
        );

        $this->assertSame($userB->id, $reservation->user_id);
        $this->assertSame('PENDING_PAYMENT', $reservation->status);
    }

    public function test_create_single_creates_pending_payment_with_correct_price_and_expiry(): void
    {
        $field = $this->makeField(price: 7500);
        $user = $this->makeUser();
        $start = Carbon::tomorrow()->setTime(10, 0);

        $reservation = $this->service->createSingle($field, $start, $user->id, expiresInMinutes: 15);

        $this->assertSame('PENDING_PAYMENT', $reservation->status);
        $this->assertEquals(7500, (float) $reservation->total_amount);
        $this->assertSame('ARS', $reservation->currency);
        $this->assertNotNull($reservation->expires_at);
        $this->assertTrue($reservation->expires_at->between(now()->addMinutes(14), now()->addMinutes(16)));
        $this->assertSame(8, strlen($reservation->verification_code));
    }

    public function test_create_single_with_subscription_id_marks_as_paid(): void
    {
        $field = $this->makeField();
        $user = $this->makeUser();
        $start = Carbon::tomorrow()->setTime(10, 0);

        // Crear una suscripción real para satisfacer la FK
        $sub = \App\Models\RecurringSubscription::create([
            'user_id'        => $user->id,
            'field_id'       => $field->id,
            'status'         => 'ACTIVE',
            'frequency'      => 'weekly',
            'occurrences'    => 4,
            'day_of_week'    => $start->dayOfWeek,
            'start_time'     => '10:00:00',
            'slot_minutes'   => 60,
            'monthly_amount' => 5000,
            'currency'       => 'ARS',
        ]);

        $reservation = $this->service->createSingle(
            $field, $start, $user->id,
            expiresInMinutes: 10,
            batchId: null,
            recurringSubscriptionId: $sub->id
        );

        $this->assertSame('PAID', $reservation->status);
        $this->assertNull($reservation->expires_at);
        $this->assertSame($sub->id, $reservation->recurring_subscription_id);
    }
}
