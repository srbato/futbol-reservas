<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\SystemMessage;
use App\Models\User;
use App\Models\Venue;
use App\Models\VenueReview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * Features misceláneas:
 *  - SystemMessage dismiss
 *  - Favoritos (add/remove venue)
 *  - Reviews de venue (validación, anti-duplicado, anti-self-review)
 *  - Vista /favorites
 */
class MiscFeaturesTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    public function test_user_can_dismiss_system_message(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $msg  = SystemMessage::create([
            'title' => 'Aviso', 'body' => 'Texto', 'is_active' => true,
        ]);

        $this->actingAs($user)
            ->postJson(route('system-messages.dismiss', $msg))
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertTrue($msg->dismissedByUsers()->where('user_id', $user->id)->exists());
    }

    public function test_dismissing_twice_is_idempotent(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $msg  = SystemMessage::create(['title' => 't', 'body' => 'b', 'is_active' => true]);

        $this->actingAs($user)->postJson(route('system-messages.dismiss', $msg));
        $this->actingAs($user)->postJson(route('system-messages.dismiss', $msg));

        $this->assertSame(1, $msg->dismissedByUsers()->where('user_id', $user->id)->count());
    }

    // ─── Favorites ───────────────────────────────────────────────────────

    public function test_user_can_favorite_a_venue(): void
    {
        $user  = $this->makeUser();
        $venue = $this->makeVenue();

        $this->actingAs($user)
            ->post(route('venues.favorite', $venue))
            ->assertSessionHas('success');

        $this->assertTrue($user->favoriteVenues()->where('venue_id', $venue->id)->exists());
    }

    public function test_user_can_unfavorite_a_venue(): void
    {
        $user  = $this->makeUser();
        $venue = $this->makeVenue();
        $user->favoriteVenues()->syncWithoutDetaching([$venue->id]);

        $this->actingAs($user)
            ->post(route('venues.unfavorite', $venue))
            ->assertSessionHas('success');

        $this->assertFalse($user->favoriteVenues()->where('venue_id', $venue->id)->exists());
    }

    public function test_favoriting_same_venue_twice_does_not_duplicate(): void
    {
        $user  = $this->makeUser();
        $venue = $this->makeVenue();

        $this->actingAs($user)->post(route('venues.favorite', $venue));
        $this->actingAs($user)->post(route('venues.favorite', $venue));

        $this->assertSame(1, $user->favoriteVenues()->where('venue_id', $venue->id)->count());
    }

    public function test_favorites_index_renders(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user)
            ->get(route('venues.favorites'))
            ->assertOk();
    }

    // ─── Reviews ─────────────────────────────────────────────────────────

    /**
     * Para reseñar el usuario debe tener una reserva PAID pasada en el venue.
     * Helper crea ese setup.
     */
    private function makeUserWhoReservedAt(Venue $venue): User
    {
        $field = $this->makeField($venue);
        $user  = $this->makeUser();
        Reservation::factory()->create([
            'field_id' => $field->id, 'user_id' => $user->id,
            'start_at' => now()->subDays(5),
            'end_at'   => now()->subDays(5)->addHour(),
            'status'   => 'PAID',
        ]);
        return $user;
    }

    public function test_user_can_post_a_review_after_reserving(): void
    {
        $venue = $this->makeVenue();
        $user  = $this->makeUserWhoReservedAt($venue);

        $this->actingAs($user)
            ->post(route('venues.reviews.store', $venue), [
                'rating'  => 5,
                'comment' => 'Excelente complejo, muy recomendable.',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('venue_reviews', [
            'venue_id' => $venue->id,
            'user_id'  => $user->id,
            'rating'   => 5,
        ]);
    }

    public function test_user_without_reservation_cannot_review(): void
    {
        $venue = $this->makeVenue();
        $user  = $this->makeUser(); // sin reserva

        $this->actingAs($user)
            ->post(route('venues.reviews.store', $venue), [
                'rating'  => 5,
                'comment' => 'X',
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('venue_reviews', 0);
    }

    public function test_user_cannot_review_same_venue_twice(): void
    {
        $venue = $this->makeVenue();
        $user  = $this->makeUserWhoReservedAt($venue);

        VenueReview::create([
            'venue_id' => $venue->id, 'user_id' => $user->id, 'rating' => 4,
        ]);

        $this->actingAs($user)
            ->post(route('venues.reviews.store', $venue), [
                'rating'  => 5,
                'comment' => 'Otra',
            ])
            ->assertSessionHas('error');

        $this->assertSame(1, VenueReview::where('user_id', $user->id)->where('venue_id', $venue->id)->count());
    }

    public function test_review_rejects_rating_above_5(): void
    {
        $venue = $this->makeVenue();
        $user  = $this->makeUserWhoReservedAt($venue);

        $this->actingAs($user)
            ->post(route('venues.reviews.store', $venue), [
                'rating'  => 6,
                'comment' => 'X',
            ])
            ->assertSessionHasErrors(['rating']);
    }

    public function test_review_rejects_rating_below_1(): void
    {
        $venue = $this->makeVenue();
        $user  = $this->makeUserWhoReservedAt($venue);

        $this->actingAs($user)
            ->post(route('venues.reviews.store', $venue), [
                'rating'  => 0,
                'comment' => 'X',
            ])
            ->assertSessionHasErrors(['rating']);
    }

    public function test_unauthenticated_users_cannot_review(): void
    {
        $venue = $this->makeVenue();

        $this->post(route('venues.reviews.store', $venue), [
            'rating' => 4,
        ])->assertRedirect(route('login'));
    }
}
