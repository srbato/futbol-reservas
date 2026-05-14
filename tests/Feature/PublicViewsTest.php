<?php

namespace Tests\Feature;

use App\Models\FaltaUnoSportProfile;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * Vistas públicas + endpoints utilitarios:
 *  - venues.weekly-calendar (vista de un complejo con su agenda semanal)
 *  - venues.show (perfil público del complejo)
 *  - ranking.index
 *  - public sport-profile (perfil público de un jugador)
 *  - va.geocode endpoint (proxy a Google Maps Geocoding)
 */
class PublicViewsTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    public function test_venue_show_renders_public(): void
    {
        $venue = $this->makeVenue();
        $this->makeField($venue);

        $this->get(route('venues.show', $venue))->assertOk();
    }

    public function test_venue_show_returns_404_for_inactive_owner_without_subscription(): void
    {
        // Owner sin role super_admin y sin sub activa → venue inaccesible
        $owner = User::factory()->create(['role' => 'venue_admin', 'is_active' => true]);
        $venue = \App\Models\Venue::factory()->create(['owner_user_id' => $owner->id]);

        $this->get(route('venues.show', $venue))->assertNotFound();
    }

    public function test_weekly_calendar_renders(): void
    {
        $venue = $this->makeVenue();
        $this->makeField($venue);

        $this->get(route('venues.weekly-calendar', $venue))->assertOk();
    }

    public function test_ranking_index_renders(): void
    {
        // Algunos perfiles para llenar el ranking
        FaltaUnoSportProfile::factory()->create([
            'user_id' => $this->makeUser()->id,
            'sport'   => 'football',
            'games_played' => 10,
            'average_rating' => 4.5,
        ]);

        $this->get(route('ranking.index'))->assertOk();
    }

    public function test_falta_uno_index_renders(): void
    {
        $this->get(route('falta-uno.index'))->assertOk();
    }

    public function test_public_sport_profile_renders_for_user_with_profile(): void
    {
        $user = $this->makeUser();
        FaltaUnoSportProfile::factory()->create([
            'user_id' => $user->id, 'sport' => 'football',
        ]);
        $viewer = $this->makeUser();

        $this->actingAs($viewer)
            ->get(route('sport-profile.public', $user))
            ->assertOk();
    }

    public function test_public_sport_profile_requires_auth(): void
    {
        $user = $this->makeUser();
        $this->get(route('sport-profile.public', $user))
            ->assertRedirect(route('login'));
    }

    // ─── Geocode endpoint (admin) ────────────────────────────────────────

    public function test_geocode_endpoint_validates_address_required(): void
    {
        $venue = $this->makeVenue();
        $owner = $venue->owner;

        $this->actingAs($owner)
            ->getJson(route('va.geocode'))
            ->assertStatus(400);
    }

    public function test_geocode_endpoint_rejects_too_long_address(): void
    {
        $venue = $this->makeVenue();
        $owner = $venue->owner;

        $this->actingAs($owner)
            ->getJson(route('va.geocode') . '?address=' . str_repeat('a', 301))
            ->assertStatus(400);
    }

    public function test_geocode_endpoint_requires_auth(): void
    {
        // JSON request → 401 (no redirect). Lo importante es que NO procese sin auth.
        $this->getJson(route('va.geocode') . '?address=Calle+Test')
            ->assertStatus(401);
    }

    // ─── My Reservations ─────────────────────────────────────────────────

    public function test_my_reservations_renders_for_authed_user(): void
    {
        $user = $this->makeUser();
        $field = $this->makeField();
        Reservation::factory()->create([
            'field_id' => $field->id, 'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('my_reservations'))
            ->assertOk();
    }

    public function test_my_reservations_requires_auth(): void
    {
        $this->get(route('my_reservations'))
            ->assertRedirect(route('login'));
    }

    public function test_dashboard_redirects_based_on_role(): void
    {
        $user = $this->makeUser(['role' => 'user', 'email_verified_at' => now()]);
        $sa   = $this->makeUser(['role' => 'super_admin', 'email_verified_at' => now()]);

        $this->actingAs($user)->get('/dashboard')
            ->assertRedirect(route('my_reservations'));
        $this->actingAs($sa)->get('/dashboard')
            ->assertRedirect(route('sa.users.index'));
    }
}
