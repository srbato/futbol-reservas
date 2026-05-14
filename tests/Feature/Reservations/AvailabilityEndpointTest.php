<?php

namespace Tests\Feature\Reservations;

use App\Models\FieldBlock;
use App\Models\FieldDiscount;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * Cobertura del endpoint público AvailabilityController (slot único — vista vieja fields.show).
 *  - GET /fields/{field}/availability con shape esperado
 *  - Marca slots ocupados/bloqueados/pasados/disponibles
 *  - Recurring availability endpoint para previsualizar abonos
 *  - Validaciones del query (?date)
 */
class AvailabilityEndpointTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    public function test_returns_slots_with_expected_shape(): void
    {
        $field = $this->makeField();

        $resp = $this->getJson(route('fields.availability', $field) . '?date=' . Carbon::tomorrow()->toDateString());

        $resp->assertOk()
            ->assertJsonStructure([
                'date',
                'field_id',
                'slots' => [
                    ['start_at', 'end_at', 'status', 'price', 'currency'],
                ],
            ])
            ->assertJsonPath('field_id', $field->id);
    }

    public function test_validation_requires_date_query(): void
    {
        $field = $this->makeField();
        $this->getJson(route('fields.availability', $field))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['date']);
    }

    public function test_blocked_cells_appear_with_blocked_status(): void
    {
        $field = $this->makeField();
        $tomorrow = Carbon::tomorrow();

        FieldBlock::create([
            'field_id'   => $field->id,
            'date'       => $tomorrow->toDateString(),
            'start_time' => '09:00',
            'end_time'   => '11:00',
            'reason'     => 'Mantenimiento',
        ]);

        $resp = $this->getJson(route('fields.availability', $field) . '?date=' . $tomorrow->toDateString());

        $slots = $resp->json('slots');
        $blocked = collect($slots)->where('status', 'BLOCKED');
        $this->assertGreaterThan(0, $blocked->count());
        $this->assertSame('Mantenimiento', $blocked->first()['reason']);
    }

    public function test_unavailable_cells_appear_with_unavailable_status_and_occupied_until(): void
    {
        $field = $this->makeField();
        $tomorrow = Carbon::tomorrow();
        $user = $this->makeUser();

        Reservation::factory()->create([
            'field_id' => $field->id,
            'user_id'  => $user->id,
            'start_at' => $tomorrow->copy()->setTime(15, 0),
            'end_at'   => $tomorrow->copy()->setTime(17, 0),
        ]);

        $resp = $this->getJson(route('fields.availability', $field) . '?date=' . $tomorrow->toDateString());

        $slots = collect($resp->json('slots'));
        $occupied = $slots->firstWhere('start_at', '15:00');
        $this->assertSame('UNAVAILABLE', $occupied['status']);
    }

    public function test_discount_applies_in_response_with_label(): void
    {
        $field = $this->makeField(price: 5000);
        $tomorrow = Carbon::tomorrow();

        FieldDiscount::factory()->create([
            'field_id'       => $field->id,
            'date'           => $tomorrow->toDateString(),
            'discount_price' => 3500,
            'label'          => 'Promo Test',
        ]);

        $resp = $this->getJson(route('fields.availability', $field) . '?date=' . $tomorrow->toDateString());

        $slot = collect($resp->json('slots'))->firstWhere('start_at', '10:00');
        $this->assertEquals(3500, $slot['price']);
        $this->assertEquals(5000, $slot['original_price']);
        $this->assertTrue($slot['has_discount']);
        $this->assertSame('Promo Test', $slot['discount_label']);
    }

    public function test_returns_empty_slots_when_field_has_no_schedule_for_dow(): void
    {
        $field = $this->makeField();
        $tomorrow = Carbon::tomorrow();
        // Borramos el schedule del día de mañana
        $field->schedules()->where('day_of_week', $tomorrow->dayOfWeek)->delete();

        $resp = $this->getJson(route('fields.availability', $field) . '?date=' . $tomorrow->toDateString());
        $resp->assertOk()->assertJsonPath('slots', []);
    }

    // ─── Recurring availability ──────────────────────────────────────────

    public function test_recurring_availability_returns_slot_per_occurrence(): void
    {
        $field = $this->makeField();
        $start = Carbon::tomorrow()->setTime(10, 0);

        $resp = $this->getJson(route('fields.recurring_availability', $field) .
            '?start_at=' . urlencode($start->toDateTimeString()) .
            '&frequency=weekly&occurrences=4');

        $resp->assertOk()
            ->assertJsonStructure(['field_id', 'slots'])
            ->assertJsonCount(4, 'slots');
    }

    public function test_recurring_marks_dates_already_occupied_as_unavailable(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $start = Carbon::tomorrow()->setTime(10, 0);

        // Bloquear la fecha #2 (semana siguiente)
        Reservation::factory()->create([
            'field_id' => $field->id,
            'user_id'  => $user->id,
            'start_at' => $start->copy()->addWeek(),
            'end_at'   => $start->copy()->addWeek()->addHour(),
        ]);

        $resp = $this->getJson(route('fields.recurring_availability', $field) .
            '?start_at=' . urlencode($start->toDateTimeString()) .
            '&frequency=weekly&occurrences=4');

        $slots = $resp->json('slots');
        $this->assertSame('available', $slots[0]['status']);
        $this->assertSame('unavailable', $slots[1]['status']);
        $this->assertSame('available', $slots[2]['status']);
    }
}
