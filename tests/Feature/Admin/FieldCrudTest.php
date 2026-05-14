<?php

namespace Tests\Feature\Admin;

use App\Models\Field;
use App\Models\FieldPrice;
use App\Models\FieldSchedule;
use App\Models\FaltaUnoSetting;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * CRUD admin de canchas (FieldController):
 *  - Owner crea cancha con price + FU setting
 *  - Validaciones (sport, format, slot_minutes, currency, night_price coherencia)
 *  - Ownership: stranger no puede crear/editar canchas en venue ajeno
 *  - toggle_active alterna is_active
 */
class FieldCrudTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    public function test_owner_can_create_field_with_basic_data(): void
    {
        $venue = $this->makeVenue();
        $owner = $venue->owner;

        $this->actingAs($owner)
            ->post(route('va.fields.store', $venue), [
                'name'            => 'Cancha 5',
                'sport'           => 'football',
                'format'          => 5,
                'slot_minutes'    => 60,
                'price_per_slot'  => 6000,
                'currency'        => 'ARS',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('fields', [
            'venue_id'     => $venue->id,
            'name'         => 'Cancha 5',
            'sport'        => 'football',
            'slot_minutes' => 60,
        ]);
        $this->assertDatabaseHas('field_prices', [
            'price_per_slot' => 6000,
            'currency'       => 'ARS',
        ]);
        $this->assertDatabaseCount('falta_uno_settings', 1);
    }

    public function test_create_field_with_night_price_requires_start_and_end(): void
    {
        $venue = $this->makeVenue();
        $owner = $venue->owner;

        $this->actingAs($owner)
            ->post(route('va.fields.store', $venue), [
                'name'                 => 'Cancha X',
                'sport'                => 'football',
                'format'               => 5,
                'slot_minutes'         => 60,
                'price_per_slot'       => 5000,
                'currency'             => 'ARS',
                'night_price_per_slot' => 8000,
                // sin night_start_time / night_end_time
            ])
            ->assertSessionHasErrors(['night_start_time', 'night_end_time']);
    }

    public function test_night_end_time_must_be_after_start(): void
    {
        $venue = $this->makeVenue();
        $owner = $venue->owner;

        $this->actingAs($owner)
            ->post(route('va.fields.store', $venue), [
                'name'                 => 'Cancha X',
                'sport'                => 'football',
                'format'               => 5,
                'slot_minutes'         => 60,
                'price_per_slot'       => 5000,
                'currency'             => 'ARS',
                'night_price_per_slot' => 8000,
                'night_start_time'     => '22:00',
                'night_end_time'       => '20:00',
            ])
            ->assertSessionHasErrors(['night_end_time']);
    }

    public function test_rejects_invalid_sport(): void
    {
        $venue = $this->makeVenue();
        $owner = $venue->owner;

        $this->actingAs($owner)
            ->post(route('va.fields.store', $venue), [
                'name'           => 'Cancha X',
                'sport'          => 'cricket', // invalido
                'format'         => 5,
                'slot_minutes'   => 60,
                'price_per_slot' => 5000,
                'currency'       => 'ARS',
            ])
            ->assertSessionHasErrors(['sport']);
    }

    public function test_rejects_slot_minutes_below_30(): void
    {
        $venue = $this->makeVenue();
        $owner = $venue->owner;

        $this->actingAs($owner)
            ->post(route('va.fields.store', $venue), [
                'name'           => 'Cancha X',
                'sport'          => 'football',
                'format'         => 5,
                'slot_minutes'   => 15,
                'price_per_slot' => 5000,
                'currency'       => 'ARS',
            ])
            ->assertSessionHasErrors(['slot_minutes']);
    }

    public function test_rejects_slot_minutes_above_180(): void
    {
        $venue = $this->makeVenue();
        $owner = $venue->owner;

        $this->actingAs($owner)
            ->post(route('va.fields.store', $venue), [
                'name'           => 'Cancha X',
                'sport'          => 'football',
                'format'         => 5,
                'slot_minutes'   => 240,
                'price_per_slot' => 5000,
                'currency'       => 'ARS',
            ])
            ->assertSessionHasErrors(['slot_minutes']);
    }

    public function test_currency_must_be_3_chars(): void
    {
        $venue = $this->makeVenue();
        $owner = $venue->owner;

        $this->actingAs($owner)
            ->post(route('va.fields.store', $venue), [
                'name'           => 'Cancha X',
                'sport'          => 'football',
                'format'         => 5,
                'slot_minutes'   => 60,
                'price_per_slot' => 5000,
                'currency'       => 'ARSAR',
            ])
            ->assertSessionHasErrors(['currency']);
    }

    public function test_stranger_venue_admin_cannot_create_field_in_other_venue(): void
    {
        $venue = $this->makeVenue();
        $stranger = $this->makeUser(['role' => 'venue_admin']);

        $resp = $this->actingAs($stranger)
            ->post(route('va.fields.store', $venue), [
                'name'           => 'Cancha X',
                'sport'          => 'football',
                'format'         => 5,
                'slot_minutes'   => 60,
                'price_per_slot' => 5000,
                'currency'       => 'ARS',
            ]);

        $this->assertContains($resp->status(), [302, 403]);
        $this->assertDatabaseCount('fields', 0);
    }

    public function test_owner_can_update_field_basic_info(): void
    {
        $field = $this->makeField();
        $owner = $field->venue->owner;

        $this->actingAs($owner)
            ->post(route('va.fields.update', $field), [
                'name'           => 'Cancha Renombrada',
                'sport'          => 'tennis',
                'format'         => 1,
                'slot_minutes'   => 90,
                'price_per_slot' => 7000,
                'currency'       => 'ARS',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('fields', [
            'id'           => $field->id,
            'name'         => 'Cancha Renombrada',
            'sport'        => 'tennis',
            'slot_minutes' => 90,
        ]);
        $this->assertDatabaseHas('field_prices', [
            'field_id'       => $field->id,
            'price_per_slot' => 7000,
        ]);
    }

    public function test_owner_can_toggle_field_active_status(): void
    {
        $field = $this->makeField();
        $owner = $field->venue->owner;

        $this->actingAs($owner)
            ->post(route('va.fields.toggle_active', $field))
            ->assertRedirect();

        $this->assertFalse((bool) $field->fresh()->is_active);

        $this->actingAs($owner)
            ->post(route('va.fields.toggle_active', $field))
            ->assertRedirect();

        $this->assertTrue((bool) $field->fresh()->is_active);
    }

    // ─── Schedule update ─────────────────────────────────────────────────

    public function test_owner_can_update_schedules(): void
    {
        $field = $this->makeField();
        $owner = $field->venue->owner;

        $this->actingAs($owner)
            ->post(route('va.schedule.update', $field), [
                'days' => [
                    1 => ['open_time' => '10:00', 'close_time' => '23:00'],
                    2 => ['open_time' => '09:00', 'close_time' => '22:00'],
                    3 => ['is_closed' => '1'],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('field_schedules', [
            'field_id' => $field->id, 'day_of_week' => 1,
            'open_time' => '10:00', 'close_time' => '23:00',
        ]);
        // dia 3 marcado como is_closed → schedule eliminado
        $this->assertDatabaseMissing('field_schedules', [
            'field_id' => $field->id, 'day_of_week' => 3,
        ]);
    }

    public function test_schedule_rejects_invalid_time_format(): void
    {
        $field = $this->makeField();
        $owner = $field->venue->owner;

        $this->actingAs($owner)
            ->post(route('va.schedule.update', $field), [
                'days' => [
                    1 => ['open_time' => '10:00 AM', 'close_time' => '23:00'],
                ],
            ])
            ->assertSessionHasErrors();
    }
}
