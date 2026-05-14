<?php

namespace Tests\Feature;

use App\Models\FieldRecurringDiscount;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * Cobertura de RecurringReservationController (modo upfront / batch).
 *  - Crea N reservas semanales / quincenales en un batch
 *  - Aplica recurring discount tier por cantidad de occurrences
 *  - Omite fechas que ya están ocupadas (no rompe todo el batch)
 *  - Rollback si NINGUNA se pudo crear
 *  - Validaciones de payload
 */
class RecurringReservationTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    public function test_creates_weekly_batch_with_N_reservations(): void
    {
        $field = $this->makeField(price: 5000);
        $user  = $this->makeUser();
        $start = Carbon::tomorrow()->setTime(10, 0);

        $resp = $this->actingAs($user)
            ->postJson(route('reservations.recurring'), [
                'field_id'    => $field->id,
                'start_at'    => $start->toDateTimeString(),
                'frequency'   => 'weekly',
                'occurrences' => 4,
            ]);

        $resp->assertCreated()
            ->assertJsonPath('summary.created', 4)
            ->assertJsonPath('summary.failed', 0)
            ->assertJsonPath('batch.subtotal', 20000)
            ->assertJsonPath('batch.total_amount', 20000);

        $this->assertDatabaseCount('reservations', 4);
        $this->assertDatabaseCount('reservation_batches', 1);

        // Cada reserva está separada por exactamente 7 días
        $reservations = Reservation::orderBy('start_at')->get();
        $this->assertEquals(4, $reservations->count());
        for ($i = 1; $i < 4; $i++) {
            $this->assertEquals(
                7 * 24 * 60,
                $reservations[$i - 1]->start_at->diffInMinutes($reservations[$i]->start_at)
            );
        }
    }

    public function test_creates_biweekly_batch_with_correct_spacing(): void
    {
        $field = $this->makeField(price: 4000);
        $user  = $this->makeUser();
        $start = Carbon::tomorrow()->setTime(10, 0);

        $this->actingAs($user)
            ->postJson(route('reservations.recurring'), [
                'field_id'    => $field->id,
                'start_at'    => $start->toDateTimeString(),
                'frequency'   => 'biweekly',
                'occurrences' => 3,
            ])
            ->assertCreated();

        $reservations = Reservation::orderBy('start_at')->get();
        $this->assertCount(3, $reservations);
        // Cada reserva separada por 14 días
        for ($i = 1; $i < 3; $i++) {
            $this->assertEquals(14 * 24 * 60, $reservations[$i - 1]->start_at->diffInMinutes($reservations[$i]->start_at));
        }
    }

    public function test_applies_recurring_discount_tier_when_occurrences_meets_minimum(): void
    {
        $field = $this->makeField(price: 5000);
        $user  = $this->makeUser();
        $start = Carbon::tomorrow()->setTime(10, 0);

        FieldRecurringDiscount::factory()->create([
            'field_id'            => $field->id,
            'min_occurrences'     => 4,
            'discount_percentage' => 10, // 10% off al alcanzar 4 ocurrencias
        ]);

        $resp = $this->actingAs($user)
            ->postJson(route('reservations.recurring'), [
                'field_id'    => $field->id,
                'start_at'    => $start->toDateTimeString(),
                'frequency'   => 'weekly',
                'occurrences' => 4,
            ]);

        $resp->assertCreated()
            ->assertJsonPath('batch.discount_percentage', 10)
            ->assertJsonPath('batch.subtotal', 20000)
            ->assertJsonPath('batch.discount_amount', 2000)
            ->assertJsonPath('batch.total_amount', 18000);

        $this->assertDatabaseHas('reservation_batches', [
            'subtotal'        => 20000,
            'discount_amount' => 2000,
            'total_amount'    => 18000,
        ]);
    }

    public function test_picks_highest_eligible_recurring_discount_tier(): void
    {
        $field = $this->makeField(price: 5000);
        $user  = $this->makeUser();
        $start = Carbon::tomorrow()->setTime(10, 0);

        // 3 tiers: con 6 occurrences debe agarrar el de min 4 → 15%
        FieldRecurringDiscount::factory()->create(['field_id' => $field->id, 'min_occurrences' => 2, 'discount_percentage' => 5]);
        FieldRecurringDiscount::factory()->create(['field_id' => $field->id, 'min_occurrences' => 4, 'discount_percentage' => 15]);
        FieldRecurringDiscount::factory()->create(['field_id' => $field->id, 'min_occurrences' => 8, 'discount_percentage' => 25]); // no alcanza

        $this->actingAs($user)
            ->postJson(route('reservations.recurring'), [
                'field_id' => $field->id, 'start_at' => $start->toDateTimeString(),
                'frequency' => 'weekly', 'occurrences' => 6,
            ])
            ->assertCreated()
            ->assertJsonPath('batch.discount_percentage', 15);
    }

    public function test_skips_dates_that_are_already_reserved_but_creates_others(): void
    {
        $field = $this->makeField(price: 5000);
        $user  = $this->makeUser();
        $start = Carbon::tomorrow()->setTime(10, 0);

        // Bloquear la 2da fecha (semana siguiente)
        Reservation::factory()->create([
            'field_id' => $field->id,
            'user_id'  => $this->makeUser()->id,
            'start_at' => $start->copy()->addWeek(),
            'end_at'   => $start->copy()->addWeek()->addHour(),
        ]);

        $resp = $this->actingAs($user)
            ->postJson(route('reservations.recurring'), [
                'field_id'    => $field->id,
                'start_at'    => $start->toDateTimeString(),
                'frequency'   => 'weekly',
                'occurrences' => 4,
            ]);

        $resp->assertCreated()
            ->assertJsonPath('summary.created', 3)
            ->assertJsonPath('summary.failed', 1);

        // 3 nuevas + 1 que ya existía = 4
        $this->assertSame(4, Reservation::count());
    }

    public function test_returns_422_and_deletes_batch_when_no_reservations_could_be_created(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        // start_at muy pasado: incluso +1 semana sigue siendo pasado → TODAS fallan
        $start = Carbon::now()->subDays(30)->setTime(10, 0);

        $resp = $this->actingAs($user)
            ->postJson(route('reservations.recurring'), [
                'field_id'    => $field->id,
                'start_at'    => $start->toDateTimeString(),
                'frequency'   => 'weekly',
                'occurrences' => 2, // i=0 (-30d) e i=1 (-23d), ambas pasado
            ]);

        $resp->assertStatus(422)->assertJsonPath('summary.created', 0);
        $this->assertDatabaseCount('reservation_batches', 0);
        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_validates_required_fields(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user)
            ->postJson(route('reservations.recurring'), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['field_id', 'start_at', 'frequency', 'occurrences']);
    }

    public function test_rejects_occurrences_above_max(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $this->actingAs($user)
            ->postJson(route('reservations.recurring'), [
                'field_id'    => $field->id,
                'start_at'    => Carbon::tomorrow()->setTime(10, 0)->toDateTimeString(),
                'frequency'   => 'weekly',
                'occurrences' => 13,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['occurrences']);
    }

    public function test_rejects_invalid_frequency(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $this->actingAs($user)
            ->postJson(route('reservations.recurring'), [
                'field_id'    => $field->id,
                'start_at'    => Carbon::tomorrow()->setTime(10, 0)->toDateTimeString(),
                'frequency'   => 'monthly',
                'occurrences' => 4,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['frequency']);
    }

    public function test_requires_authentication(): void
    {
        $field = $this->makeField();
        $this->postJson(route('reservations.recurring'), [
            'field_id'    => $field->id,
            'start_at'    => Carbon::tomorrow()->setTime(10, 0)->toDateTimeString(),
            'frequency'   => 'weekly',
            'occurrences' => 2,
        ])->assertStatus(401);
    }

    public function test_each_reservation_has_correct_price_assigned(): void
    {
        $field = $this->makeField(price: 7500);
        $user  = $this->makeUser();
        $start = Carbon::tomorrow()->setTime(10, 0);

        $this->actingAs($user)
            ->postJson(route('reservations.recurring'), [
                'field_id'    => $field->id,
                'start_at'    => $start->toDateTimeString(),
                'frequency'   => 'weekly',
                'occurrences' => 3,
            ])
            ->assertCreated();

        Reservation::all()->each(function ($r) {
            $this->assertEquals(7500, (float) $r->total_amount);
        });
    }
}
