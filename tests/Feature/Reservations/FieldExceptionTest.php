<?php

namespace Tests\Feature\Reservations;

use App\Models\FieldException;
use App\Models\Reservation;
use App\Services\FieldAvailabilityService;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * Cobertura de FieldException — overrides puntuales del schedule.
 *  - is_closed=true → la cancha no tiene slots ese día
 *  - open_time/close_time custom → reemplazan el schedule normal
 *  - ReservationService respeta exceptions
 */
class FieldExceptionTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    public function test_closed_exception_makes_field_unavailable_for_that_date(): void
    {
        $field = $this->makeField();
        $tomorrow = Carbon::tomorrow();

        FieldException::factory()->closed()->create([
            'field_id' => $field->id,
            'date'     => $tomorrow->toDateString(),
        ]);

        $slots = app(FieldAvailabilityService::class)->computeSlots($field->fresh(['exceptions', 'price', 'schedules']), $tomorrow);
        $this->assertEmpty($slots);
    }

    public function test_special_hours_exception_overrides_normal_schedule(): void
    {
        $field = $this->makeField(); // schedule normal: 08:00-22:00
        $tomorrow = Carbon::tomorrow();

        FieldException::factory()->withSpecialHours('14:00', '18:00')->create([
            'field_id' => $field->id,
            'date'     => $tomorrow->toDateString(),
        ]);

        $slots = app(FieldAvailabilityService::class)->computeSlots($field->fresh(['exceptions', 'price', 'schedules']), $tomorrow);
        $this->assertCount(4, $slots); // 14, 15, 16, 17
        $this->assertSame('14:00', $slots[0]['start_at']);
        $this->assertSame('18:00', end($slots)['end_at']);
    }

    public function test_normal_schedule_unaffected_when_exception_is_for_other_date(): void
    {
        $field = $this->makeField();
        $tomorrow = Carbon::tomorrow();
        $otherDay = Carbon::tomorrow()->addDays(3);

        FieldException::factory()->closed()->create([
            'field_id' => $field->id,
            'date'     => $otherDay->toDateString(),
        ]);

        $slots = app(FieldAvailabilityService::class)->computeSlots($field->fresh(['exceptions', 'price', 'schedules']), $tomorrow);
        $this->assertCount(14, $slots); // schedule normal sin alterar
    }

    public function test_reservation_service_aborts_when_exception_marks_closed(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $tomorrow = Carbon::tomorrow();

        FieldException::factory()->closed()->create([
            'field_id' => $field->id,
            'date'     => $tomorrow->toDateString(),
        ]);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('cerrada en esa fecha');

        app(ReservationService::class)->createSingle(
            $field->fresh(['venue', 'price', 'schedules', 'exceptions']),
            $tomorrow->copy()->setTime(10, 0),
            $user->id
        );
    }

    public function test_reservation_service_allows_reservation_within_special_hours(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $tomorrow = Carbon::tomorrow();

        FieldException::factory()->withSpecialHours('20:00', '23:00')->create([
            'field_id' => $field->id,
            'date'     => $tomorrow->toDateString(),
        ]);

        $reservation = app(ReservationService::class)->createSingle(
            $field->fresh(['venue', 'price', 'schedules', 'exceptions']),
            $tomorrow->copy()->setTime(21, 0),
            $user->id
        );

        $this->assertNotNull($reservation);
        $this->assertSame('PENDING_PAYMENT', $reservation->status);
    }

    public function test_reservation_service_aborts_outside_special_hours(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $tomorrow = Carbon::tomorrow();

        FieldException::factory()->withSpecialHours('20:00', '23:00')->create([
            'field_id' => $field->id,
            'date'     => $tomorrow->toDateString(),
        ]);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('fuera del rango disponible');

        // 10:00 está fuera del rango especial 20-23
        app(ReservationService::class)->createSingle(
            $field->fresh(['venue', 'price', 'schedules', 'exceptions']),
            $tomorrow->copy()->setTime(10, 0),
            $user->id
        );
    }
}
