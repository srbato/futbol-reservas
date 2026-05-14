<?php

namespace Tests\Feature\Admin;

use App\Models\FieldDiscount;
use App\Models\FieldRecurringDiscount;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * Cobertura de los CRUD de descuentos del admin (regular + recurring).
 */
class DiscountTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    public function test_owner_can_create_discount_for_specific_date(): void
    {
        $field = $this->makeField();
        $owner = $field->venue->owner;

        $this->actingAs($owner)
            ->post(route('va.discounts.store'), [
                'field_id'       => $field->id,
                'date'           => Carbon::tomorrow()->toDateString(),
                'discount_price' => 3000,
                'label'          => 'Promo Verano',
            ])
            ->assertRedirect(route('va.discounts.index'));

        $this->assertDatabaseHas('field_discounts', [
            'field_id'       => $field->id,
            'discount_price' => 3000,
            'label'          => 'Promo Verano',
            'is_active'      => true,
        ]);
    }

    public function test_can_create_discount_for_day_of_week_with_time_range(): void
    {
        $field = $this->makeField();
        $owner = $field->venue->owner;

        $this->actingAs($owner)
            ->post(route('va.discounts.store'), [
                'field_id'       => $field->id,
                'day_of_week'    => 1,
                'start_time'     => '14:00',
                'end_time'       => '17:00',
                'discount_price' => 4000,
                'label'          => 'Lunes Mañana',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('field_discounts', [
            'field_id'    => $field->id,
            'day_of_week' => 1,
            'start_time'  => '14:00',
            'end_time'    => '17:00',
        ]);
    }

    public function test_end_time_must_be_after_start_time(): void
    {
        $field = $this->makeField();
        $owner = $field->venue->owner;

        $this->actingAs($owner)
            ->post(route('va.discounts.store'), [
                'field_id'       => $field->id,
                'date'           => Carbon::tomorrow()->toDateString(),
                'start_time'     => '17:00',
                'end_time'       => '14:00',
                'discount_price' => 3000,
            ])
            ->assertSessionHasErrors(['end_time']);
    }

    public function test_discount_price_must_be_non_negative(): void
    {
        $field = $this->makeField();
        $owner = $field->venue->owner;

        $this->actingAs($owner)
            ->post(route('va.discounts.store'), [
                'field_id'       => $field->id,
                'date'           => Carbon::tomorrow()->toDateString(),
                'discount_price' => -100,
            ])
            ->assertSessionHasErrors(['discount_price']);
    }

    public function test_owner_can_destroy_discount(): void
    {
        $field = $this->makeField();
        $owner = $field->venue->owner;
        $discount = FieldDiscount::factory()->create(['field_id' => $field->id]);

        $this->actingAs($owner)
            ->post(route('va.discounts.destroy', $discount))
            ->assertRedirect();

        $this->assertDatabaseMissing('field_discounts', ['id' => $discount->id]);
    }

    public function test_other_users_cannot_destroy_discount_in_other_venue(): void
    {
        $field = $this->makeField();
        $discount = FieldDiscount::factory()->create(['field_id' => $field->id]);
        $stranger = $this->makeUser(['role' => 'venue_admin']);

        $resp = $this->actingAs($stranger)
            ->post(route('va.discounts.destroy', $discount));

        $this->assertContains($resp->status(), [302, 403]);
        $this->assertDatabaseHas('field_discounts', ['id' => $discount->id]);
    }

    // ─── Recurring discounts ─────────────────────────────────────────────

    public function test_owner_can_create_recurring_discount(): void
    {
        $field = $this->makeField();
        $owner = $field->venue->owner;

        $this->actingAs($owner)
            ->post(route('va.recurring_discounts.store'), [
                'field_id'            => $field->id,
                'min_occurrences'     => 4,
                'discount_percentage' => 15,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('field_recurring_discounts', [
            'field_id'            => $field->id,
            'min_occurrences'     => 4,
            'discount_percentage' => 15,
        ]);
    }

    public function test_recurring_discount_validations(): void
    {
        $field = $this->makeField();
        $owner = $field->venue->owner;

        // min_occurrences fuera de rango (>12)
        $this->actingAs($owner)
            ->post(route('va.recurring_discounts.store'), [
                'field_id' => $field->id, 'min_occurrences' => 15, 'discount_percentage' => 10,
            ])
            ->assertSessionHasErrors(['min_occurrences']);

        // discount_percentage 0 (debe ser >=1)
        $this->actingAs($owner)
            ->post(route('va.recurring_discounts.store'), [
                'field_id' => $field->id, 'min_occurrences' => 4, 'discount_percentage' => 0,
            ])
            ->assertSessionHasErrors(['discount_percentage']);

        // discount_percentage > 100
        $this->actingAs($owner)
            ->post(route('va.recurring_discounts.store'), [
                'field_id' => $field->id, 'min_occurrences' => 4, 'discount_percentage' => 150,
            ])
            ->assertSessionHasErrors(['discount_percentage']);
    }

    public function test_owner_can_destroy_recurring_discount(): void
    {
        $field = $this->makeField();
        $owner = $field->venue->owner;
        $rd = FieldRecurringDiscount::factory()->create(['field_id' => $field->id]);

        $this->actingAs($owner)
            ->post(route('va.recurring_discounts.destroy', $rd))
            ->assertRedirect();

        $this->assertDatabaseMissing('field_recurring_discounts', ['id' => $rd->id]);
    }
}
