<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Services\FieldAvailabilityService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

class ReservationGridTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    public function test_field_availability_returns_slots_within_schedule(): void
    {
        $field = $this->makeField();
        $tomorrow = Carbon::tomorrow();

        $slots = app(FieldAvailabilityService::class)->computeSlots($field, $tomorrow);

        $this->assertCount(14, $slots);
        $this->assertSame('08:00', $slots[0]['start_at']);
        $this->assertSame('AVAILABLE', $slots[0]['status']);
    }

    public function test_field_availability_marks_reserved_slots_as_unavailable(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $tomorrow = Carbon::tomorrow();

        Reservation::factory()->create([
            'field_id' => $field->id,
            'user_id'  => $user->id,
            'start_at' => $tomorrow->copy()->setTime(10, 0),
            'end_at'   => $tomorrow->copy()->setTime(11, 0),
        ]);

        $slots = app(FieldAvailabilityService::class)->computeSlots($field, $tomorrow);
        $tenAm = collect($slots)->firstWhere('start_at', '10:00');

        $this->assertSame('UNAVAILABLE', $tenAm['status']);
        $this->assertSame('11:00', $tenAm['occupied_until']);
        $this->assertNotNull($tenAm['entity_key']);
    }

    public function test_venue_grid_availability_returns_json(): void
    {
        $field = $this->makeField();

        $resp = $this->getJson(route('venues.grid_availability', $field->venue) . '?date=' . Carbon::tomorrow()->toDateString());

        $resp->assertOk()
            ->assertJsonStructure(['date', 'venue_id', 'fields' => [['id', 'name', 'slots']]])
            ->assertJsonPath('venue_id', $field->venue_id);
    }

    public function test_contiguous_single_slot_creates_one_reservation(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $start = Carbon::tomorrow()->setTime(10, 0);

        $this->actingAs($user)
            ->postJson(route('reservations.contiguous'), [
                'field_id' => $field->id,
                'start_at' => $start->toDateTimeString(),
                'slots'    => 1,
            ])
            ->assertCreated()
            ->assertJsonPath('type', 'single');

        $this->assertDatabaseCount('reservations', 1);
        $this->assertDatabaseHas('reservations', [
            'field_id' => $field->id,
            'user_id'  => $user->id,
            'status'   => 'PENDING_PAYMENT',
        ]);
    }

    public function test_contiguous_multi_slot_creates_a_batch(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $start = Carbon::tomorrow()->setTime(10, 0);

        $this->actingAs($user)
            ->postJson(route('reservations.contiguous'), [
                'field_id' => $field->id,
                'start_at' => $start->toDateTimeString(),
                'slots'    => 3,
            ])
            ->assertCreated()
            ->assertJsonPath('type', 'batch');

        $this->assertDatabaseCount('reservations', 3);
        $this->assertDatabaseCount('reservation_batches', 1);
        $this->assertDatabaseHas('reservation_batches', [
            'subtotal'     => 15000,
            'total_amount' => 15000,
        ]);
    }

    public function test_contiguous_rolls_back_when_one_slot_overlaps(): void
    {
        $field = $this->makeField();
        $userA = $this->makeUser();
        $userB = $this->makeUser();
        $start = Carbon::tomorrow()->setTime(10, 0);

        Reservation::factory()->create([
            'field_id' => $field->id,
            'user_id'  => $userA->id,
            'start_at' => $start->copy()->addHour(),
            'end_at'   => $start->copy()->addHours(2),
        ]);

        $before = Reservation::count();

        $this->actingAs($userB)
            ->postJson(route('reservations.contiguous'), [
                'field_id' => $field->id,
                'start_at' => $start->toDateTimeString(),
                'slots'    => 3,
            ])
            ->assertStatus(409);

        $this->assertSame($before, Reservation::count());
        $this->assertDatabaseCount('reservation_batches', 0);
    }

    public function test_block_creation_fails_when_overlapping_reservation(): void
    {
        $field = $this->makeField();
        $owner = $field->venue->owner;
        $reservedDate = Carbon::tomorrow();

        Reservation::factory()->create([
            'field_id' => $field->id,
            'user_id'  => $this->makeUser()->id,
            'start_at' => $reservedDate->copy()->setTime(15, 0),
            'end_at'   => $reservedDate->copy()->setTime(16, 0),
        ]);

        $this->actingAs($owner)
            ->post(route('va.blocks.store'), [
                'field_id'   => $field->id,
                'date'       => $reservedDate->toDateString(),
                'start_time' => '14:00',
                'end_time'   => '17:00',
                'reason'     => 'Mantenimiento',
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('field_blocks', 0);
    }

    public function test_block_creation_succeeds_when_no_overlap(): void
    {
        $field = $this->makeField();
        $owner = $field->venue->owner;

        $this->actingAs($owner)
            ->post(route('va.blocks.store'), [
                'field_id'   => $field->id,
                'date'       => Carbon::tomorrow()->toDateString(),
                'start_time' => '14:00',
                'end_time'   => '15:00',
                'reason'     => 'Mantenimiento',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseCount('field_blocks', 1);
    }
}
