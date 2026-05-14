<?php

namespace Tests\Feature;

use App\Models\FaltaUnoGame;
use App\Models\FaltaUnoParticipant;
use App\Models\FaltaUnoSetting;
use App\Models\FaltaUnoSportProfile;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * Cobertura de las features nuevas de Falta Uno:
 *  - message del organizador + partido privado (is_private)
 *  - sistema de edad exacta (age_min / age_max)
 *  - filtro por dueño con suscripción vigente (hasActiveOwner)
 */
class FaltaUnoFeaturesTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    /** Crea un game OPEN con FU setting habilitado. reservation_id queda null (válido para el feed). */
    private function makeGame(array $overrides = []): FaltaUnoGame
    {
        $field = $this->makeField();
        FaltaUnoSetting::create([
            'field_id'                => $field->id,
            'enabled'                 => true,
            'refund_deadline_minutes' => 60,
            'fill_deadline_minutes'   => 120,
        ]);
        return FaltaUnoGame::factory()->create(array_merge([
            'field_id'          => $field->id,
            'reservation_id'    => null,
            'initiator_user_id' => $this->makeUser()->id,
            'start_at'          => now()->addDays(2)->setTime(20, 0),
        ], $overrides));
    }

    /** Venue cuyo dueño NO tiene suscripción vigente (rol player). */
    private function makeVenueWithInactiveOwner(): Venue
    {
        $owner = User::factory()->create(['role' => 'venue_admin', 'is_active' => true]);
        return Venue::factory()->create(['owner_user_id' => $owner->id, 'is_active' => true]);
    }

    private function makeProfiledUser(array $userOverrides = [], array $profileOverrides = []): User
    {
        $user = $this->makeUser($userOverrides);
        FaltaUnoSportProfile::factory()->create(array_merge([
            'user_id' => $user->id, 'sport' => 'football', 'gender' => 'male', 'category' => 'intermedio',
        ], $profileOverrides));
        return $user;
    }

    // ─── MENSAJE + PRIVADO ────────────────────────────────────────────────

    public function test_private_game_is_excluded_from_public_index(): void
    {
        $public  = $this->makeGame(['is_private' => false]);
        $private = $this->makeGame(['is_private' => true]);

        $this->get(route('falta-uno.index'))
            ->assertViewHas('games', function ($games) use ($public, $private) {
                return $games->contains('id', $public->id)
                    && ! $games->contains('id', $private->id);
            });
    }

    public function test_private_game_is_still_accessible_by_direct_link(): void
    {
        $private = $this->makeGame(['is_private' => true]);

        $this->actingAs($this->makeUser())
            ->get(route('falta-uno.show', $private))
            ->assertOk();
    }

    public function test_message_is_shown_on_show_page(): void
    {
        $game = $this->makeGame(['message' => 'Pelota se trae, agua hay en el complejo']);

        $this->actingAs($this->makeUser())
            ->get(route('falta-uno.show', $game))
            ->assertOk()
            ->assertSee('Pelota se trae, agua hay en el complejo');
    }

    public function test_message_and_private_are_cast_correctly(): void
    {
        $game = $this->makeGame(['message' => 'hola', 'is_private' => true]);
        $fresh = $game->fresh();

        $this->assertSame('hola', $fresh->message);
        $this->assertTrue($fresh->is_private);
    }

    // ─── EDAD EXACTA ──────────────────────────────────────────────────────

    public function test_is_in_age_range_logic(): void
    {
        $open = $this->makeGame(['age_min' => null, 'age_max' => null]);
        $this->assertTrue($open->isInAgeRange(8));
        $this->assertTrue($open->isInAgeRange(null));

        $ranged = $this->makeGame(['age_min' => 25, 'age_max' => 35]);
        $this->assertFalse($ranged->isInAgeRange(24));
        $this->assertTrue($ranged->isInAgeRange(25));
        $this->assertTrue($ranged->isInAgeRange(30));
        $this->assertTrue($ranged->isInAgeRange(35));
        $this->assertFalse($ranged->isInAgeRange(36));
        $this->assertFalse($ranged->isInAgeRange(null));

        $minOnly = $this->makeGame(['age_min' => 18, 'age_max' => null]);
        $this->assertFalse($minOnly->isInAgeRange(17));
        $this->assertTrue($minOnly->isInAgeRange(18));
    }

    public function test_cannot_join_when_age_below_range(): void
    {
        $game = $this->makeGame(['age_min' => 25, 'age_max' => 35, 'total_players' => 10, 'players_needed' => 8]);
        $user = $this->makeProfiledUser(['age' => 20]);

        $this->actingAs($user)
            ->post(route('falta-uno.join', $game))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('falta_uno_participants', 0);
    }

    public function test_cannot_join_when_age_above_range(): void
    {
        $game = $this->makeGame(['age_min' => 25, 'age_max' => 35, 'total_players' => 10, 'players_needed' => 8]);
        $user = $this->makeProfiledUser(['age' => 40]);

        $this->actingAs($user)
            ->post(route('falta-uno.join', $game))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('falta_uno_participants', 0);
    }

    public function test_can_join_when_age_in_range(): void
    {
        $game = $this->makeGame(['age_min' => 25, 'age_max' => 35, 'total_players' => 10, 'players_needed' => 8]);
        $user = $this->makeProfiledUser(['age' => 30]);

        $this->actingAs($user)
            ->post(route('falta-uno.join', $game))
            ->assertRedirect();

        $this->assertDatabaseHas('falta_uno_participants', [
            'game_id' => $game->id, 'user_id' => $user->id, 'status' => 'confirmed',
        ]);
    }

    public function test_join_requires_age_when_game_has_age_filter(): void
    {
        $game = $this->makeGame(['age_min' => 25, 'age_max' => 35, 'total_players' => 10, 'players_needed' => 8]);
        $user = $this->makeProfiledUser(['age' => null]);

        $this->actingAs($user)
            ->post(route('falta-uno.join', $game))
            ->assertRedirect('/profile#personal-info');

        $this->assertDatabaseCount('falta_uno_participants', 0);
    }

    public function test_age_filter_does_not_block_when_game_has_no_age_range(): void
    {
        $game = $this->makeGame(['age_min' => null, 'age_max' => null, 'total_players' => 10, 'players_needed' => 8]);
        $user = $this->makeProfiledUser(['age' => null]);

        $this->actingAs($user)
            ->post(route('falta-uno.join', $game))
            ->assertRedirect();

        $this->assertDatabaseHas('falta_uno_participants', [
            'game_id' => $game->id, 'user_id' => $user->id, 'status' => 'confirmed',
        ]);
    }

    public function test_profile_update_accepts_integer_age(): void
    {
        $user = $this->makeUser(['age' => null]);

        $this->actingAs($user)
            ->post(route('profile.update'), [
                'name'  => $user->name,
                'email' => $user->email,
                'age'   => 28,
            ])
            ->assertRedirect();

        $this->assertSame(28, $user->fresh()->age);
    }

    public function test_profile_update_rejects_out_of_range_age(): void
    {
        $user = $this->makeUser(['age' => null]);

        $this->actingAs($user)
            ->post(route('profile.update'), [
                'name'  => $user->name,
                'email' => $user->email,
                'age'   => 200,
            ])
            ->assertSessionHasErrors('age');

        $this->assertNull($user->fresh()->age);
    }

    // ─── DUEÑO CON SUSCRIPCIÓN VIGENTE ────────────────────────────────────

    public function test_game_from_venue_with_inactive_owner_is_excluded_from_index(): void
    {
        $active = $this->makeGame(); // venue con dueño super_admin (makeVenue)

        $inactiveVenue = $this->makeVenueWithInactiveOwner();
        $inactiveField = $this->makeField($inactiveVenue);
        FaltaUnoSetting::create(['field_id' => $inactiveField->id, 'enabled' => true]);
        $hidden = FaltaUnoGame::factory()->create([
            'field_id'          => $inactiveField->id,
            'reservation_id'    => null,
            'initiator_user_id' => $this->makeUser()->id,
            'start_at'          => now()->addDays(2)->setTime(20, 0),
        ]);

        $this->get(route('falta-uno.index'))
            ->assertViewHas('games', function ($games) use ($active, $hidden) {
                return $games->contains('id', $active->id)
                    && ! $games->contains('id', $hidden->id);
            });
    }

    public function test_cannot_join_game_when_venue_owner_inactive(): void
    {
        $inactiveVenue = $this->makeVenueWithInactiveOwner();
        $inactiveField = $this->makeField($inactiveVenue);
        FaltaUnoSetting::create(['field_id' => $inactiveField->id, 'enabled' => true]);
        $game = FaltaUnoGame::factory()->create([
            'field_id'          => $inactiveField->id,
            'reservation_id'    => null,
            'initiator_user_id' => $this->makeUser()->id,
            'start_at'          => now()->addDays(2)->setTime(20, 0),
            'total_players'     => 10,
            'players_needed'    => 8,
        ]);
        $user = $this->makeProfiledUser();

        $this->actingAs($user)
            ->post(route('falta-uno.join', $game))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('falta_uno_participants', 0);
    }

    public function test_create_form_404_when_venue_owner_inactive(): void
    {
        $inactiveVenue = $this->makeVenueWithInactiveOwner();
        $inactiveField = $this->makeField($inactiveVenue);
        FaltaUnoSetting::create(['field_id' => $inactiveField->id, 'enabled' => true]);

        $this->actingAs($this->makeUser())
            ->get(route('falta-uno.create', $inactiveField))
            ->assertNotFound();
    }
}
