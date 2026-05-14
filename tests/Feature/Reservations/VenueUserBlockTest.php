<?php

namespace Tests\Feature\Reservations;

use App\Exceptions\VenueBlockedException;
use App\Models\VenueUserBlock;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * VenueUserBlock — el dueño bloquea a un usuario problemático para que no
 * pueda reservar más en su complejo.
 *  - Owner crea/elimina bloqueos
 *  - No bloquearse a uno mismo, no bloquear al dueño
 *  - No duplicar bloqueos
 *  - ReservationService respeta el bloqueo (lanza VenueBlockedException)
 *  - Bloqueo en venue A no afecta venue B
 */
class VenueUserBlockTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    public function test_owner_can_block_user_in_their_venue(): void
    {
        $venue = $this->makeVenue();
        $owner = $venue->owner;
        $target = $this->makeUser();

        $this->actingAs($owner)
            ->post(route('va.user-blocks.store'), [
                'venue_id' => $venue->id,
                'user_id'  => $target->id,
                'reason'   => 'Mala conducta repetida',
            ])
            ->assertRedirect(route('va.user-blocks.index'));

        $this->assertDatabaseHas('venue_user_blocks', [
            'venue_id'   => $venue->id,
            'user_id'    => $target->id,
            'blocked_by' => $owner->id,
            'reason'     => 'Mala conducta repetida',
        ]);
    }

    public function test_cannot_block_yourself(): void
    {
        $venue = $this->makeVenue();
        $owner = $venue->owner;

        $this->actingAs($owner)
            ->post(route('va.user-blocks.store'), [
                'venue_id' => $venue->id,
                'user_id'  => $owner->id,
            ])
            ->assertSessionHas('error');
    }

    public function test_cannot_block_venue_owner(): void
    {
        $venue   = $this->makeVenue();
        $owner   = $venue->owner;
        $sa      = $this->makeUser(['role' => 'super_admin']); // bypassa accessibleBy

        $this->actingAs($sa)
            ->post(route('va.user-blocks.store'), [
                'venue_id' => $venue->id,
                'user_id'  => $owner->id,
            ])
            ->assertSessionHas('error');
    }

    public function test_cannot_duplicate_block(): void
    {
        $venue  = $this->makeVenue();
        $owner  = $venue->owner;
        $target = $this->makeUser();

        VenueUserBlock::create([
            'venue_id'   => $venue->id,
            'user_id'    => $target->id,
            'blocked_by' => $owner->id,
        ]);

        $this->actingAs($owner)
            ->post(route('va.user-blocks.store'), [
                'venue_id' => $venue->id,
                'user_id'  => $target->id,
            ])
            ->assertSessionHas('error');

        $this->assertSame(1, VenueUserBlock::where('venue_id', $venue->id)->where('user_id', $target->id)->count());
    }

    public function test_owner_can_unblock_user(): void
    {
        $venue = $this->makeVenue();
        $owner = $venue->owner;
        $block = VenueUserBlock::create([
            'venue_id'   => $venue->id,
            'user_id'    => $this->makeUser()->id,
            'blocked_by' => $owner->id,
        ]);

        $this->actingAs($owner)
            ->post(route('va.user-blocks.destroy', $block))
            ->assertRedirect();

        $this->assertDatabaseMissing('venue_user_blocks', ['id' => $block->id]);
    }

    public function test_blocked_user_cannot_reserve_in_that_venue(): void
    {
        $field = $this->makeField();
        $owner = $field->venue->owner;
        $target = $this->makeUser();

        VenueUserBlock::create([
            'venue_id'   => $field->venue_id,
            'user_id'    => $target->id,
            'blocked_by' => $owner->id,
            'reason'     => 'No-show repetidos',
        ]);

        $this->expectException(VenueBlockedException::class);

        app(ReservationService::class)->createSingle(
            $field->fresh(['venue', 'price', 'schedules']),
            Carbon::tomorrow()->setTime(10, 0),
            $target->id
        );
    }

    public function test_block_in_venue_a_does_not_affect_venue_b(): void
    {
        $fieldA = $this->makeField();
        $venueB = $this->makeVenue();
        $fieldB = $this->makeField($venueB);
        $owner  = $fieldA->venue->owner;
        $target = $this->makeUser();

        // Bloqueado en A
        VenueUserBlock::create([
            'venue_id'   => $fieldA->venue_id,
            'user_id'    => $target->id,
            'blocked_by' => $owner->id,
        ]);

        // Pero puede reservar en B
        $reservation = app(ReservationService::class)->createSingle(
            $fieldB->fresh(['venue', 'price', 'schedules']),
            Carbon::tomorrow()->setTime(10, 0),
            $target->id
        );

        $this->assertNotNull($reservation);
        $this->assertSame($target->id, $reservation->user_id);
    }

    public function test_static_isBlocked_returns_correctly(): void
    {
        $venue  = $this->makeVenue();
        $target = $this->makeUser();

        $this->assertFalse(VenueUserBlock::isBlocked($target->id, $venue->id));

        VenueUserBlock::create([
            'venue_id'   => $venue->id,
            'user_id'    => $target->id,
            'blocked_by' => $venue->owner_user_id,
        ]);

        $this->assertTrue(VenueUserBlock::isBlocked($target->id, $venue->id));
    }

    public function test_user_search_returns_users_for_blocking(): void
    {
        $venue = $this->makeVenue();
        $owner = $venue->owner;
        $matching = $this->makeUser(['name' => 'Juan Test', 'email' => 'juan@test.com']);
        $this->makeUser(['name' => 'María Test', 'email' => 'maria@test.com']);

        $resp = $this->actingAs($owner)
            ->getJson(route('va.user-blocks.search') . '?q=juan&venue_id=' . $venue->id);

        $resp->assertOk();
        // Devuelve un array con al menos el usuario que matchea
        $found = collect($resp->json())->pluck('email')->all();
        $this->assertContains('juan@test.com', $found);
    }
}
