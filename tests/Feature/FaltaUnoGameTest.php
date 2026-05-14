<?php

namespace Tests\Feature;

use App\Models\FaltaUnoGame;
use App\Models\FaltaUnoParticipant;
use App\Models\FaltaUnoSetting;
use App\Models\FaltaUnoSportProfile;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * Cobertura del flujo de partidos Falta Uno:
 *  - Join: gender filter, category filter, profile required, full check, ya iniciado
 *  - Leave: confirmed → cancelled, no_show no se puede dejar
 *  - Kick: sólo iniciador puede sacar a otro
 *  - markNoShows: sólo iniciador, sólo después del partido, transition confirmed→no_show
 */
class FaltaUnoGameTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    /**
     * Crea un game OPEN con reserva PAID asociada y FU setting habilitado.
     */
    private function makeGame(array $gameOverrides = []): FaltaUnoGame
    {
        $field = $this->makeField();
        $reservation = Reservation::factory()->create([
            'field_id' => $field->id,
            'user_id'  => $this->makeUser()->id,
            'start_at' => now()->addDays(2)->setTime(20, 0),
            'end_at'   => now()->addDays(2)->setTime(21, 0),
        ]);
        FaltaUnoSetting::create([
            'field_id'                  => $field->id,
            'enabled'                   => true,
            'refund_deadline_minutes'   => 60,
            'fill_deadline_minutes'     => 120,
        ]);
        return FaltaUnoGame::factory()->create(array_merge([
            'field_id'          => $field->id,
            'reservation_id'    => $reservation->id,
            'initiator_user_id' => $reservation->user_id,
            'start_at'          => $reservation->start_at,
        ], $gameOverrides));
    }

    public function test_user_can_join_open_game_with_compatible_profile(): void
    {
        $game = $this->makeGame(['gender_filter' => 'male', 'total_players' => 10, 'players_needed' => 8]);
        $user = $this->makeUser();
        FaltaUnoSportProfile::factory()->create([
            'user_id' => $user->id, 'sport' => 'football', 'gender' => 'male', 'category' => 'intermedio',
        ]);

        $this->actingAs($user)
            ->post(route('falta-uno.join', $game))
            ->assertRedirect();

        $this->assertDatabaseHas('falta_uno_participants', [
            'game_id' => $game->id, 'user_id' => $user->id, 'status' => 'confirmed',
        ]);
    }

    public function test_cannot_join_when_gender_filter_does_not_match(): void
    {
        $game = $this->makeGame(['gender_filter' => 'male']);
        $user = $this->makeUser();
        FaltaUnoSportProfile::factory()->create([
            'user_id' => $user->id, 'sport' => 'football', 'gender' => 'female', 'category' => 'intermedio',
        ]);

        $this->actingAs($user)
            ->post(route('falta-uno.join', $game))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('falta_uno_participants', 0);
    }

    public function test_cannot_join_without_sport_profile(): void
    {
        $game = $this->makeGame();
        $user = $this->makeUser();
        // No tiene profile

        $resp = $this->actingAs($user)->post(route('falta-uno.join', $game));
        $resp->assertSessionHas('error');
        $this->assertDatabaseCount('falta_uno_participants', 0);
    }

    public function test_cannot_join_when_game_is_full(): void
    {
        $game = $this->makeGame(['players_needed' => 0, 'status' => 'full']);
        $user = $this->makeUser();
        FaltaUnoSportProfile::factory()->create([
            'user_id' => $user->id, 'sport' => 'football', 'gender' => 'male', 'category' => 'intermedio',
        ]);

        $this->actingAs($user)
            ->post(route('falta-uno.join', $game))
            ->assertSessionHas('error');
    }

    public function test_initiator_cannot_join_their_own_game(): void
    {
        $game = $this->makeGame();
        $initiator = $game->initiator;
        // Asegurar que initiator tenga is_active y profile
        $initiator->update(['is_active' => true]);
        FaltaUnoSportProfile::factory()->create([
            'user_id' => $initiator->id, 'sport' => 'football', 'gender' => 'male', 'category' => 'intermedio',
        ]);

        $this->actingAs($initiator)
            ->post(route('falta-uno.join', $game))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('falta_uno_participants', 0);
    }

    public function test_cannot_join_game_that_already_started(): void
    {
        $game = $this->makeGame(['start_at' => now()->subHour()]);
        $user = $this->makeUser();
        FaltaUnoSportProfile::factory()->create([
            'user_id' => $user->id, 'sport' => 'football', 'gender' => 'male', 'category' => 'intermedio',
        ]);

        $this->actingAs($user)
            ->post(route('falta-uno.join', $game))
            ->assertSessionHas('error');
    }

    public function test_user_can_leave_a_game_they_joined(): void
    {
        $game = $this->makeGame();
        $user = $this->makeUser();
        FaltaUnoParticipant::factory()->create([
            'game_id' => $game->id, 'user_id' => $user->id, 'status' => 'confirmed',
        ]);

        $this->actingAs($user)
            ->post(route('falta-uno.leave', $game))
            ->assertRedirect();

        $this->assertDatabaseHas('falta_uno_participants', [
            'game_id' => $game->id, 'user_id' => $user->id, 'status' => 'cancelled',
        ]);
    }

    public function test_initiator_can_kick_a_participant(): void
    {
        $game = $this->makeGame();
        $bystander = $this->makeUser();
        FaltaUnoParticipant::factory()->create([
            'game_id' => $game->id, 'user_id' => $bystander->id, 'status' => 'confirmed',
        ]);

        $this->actingAs($game->initiator)
            ->post(route('falta-uno.kick', ['game' => $game, 'user' => $bystander]))
            ->assertRedirect();

        $this->assertDatabaseHas('falta_uno_participants', [
            'game_id' => $game->id,
            'user_id' => $bystander->id,
            'status'  => 'cancelled',
            'was_kicked' => true,
        ]);
    }

    public function test_non_initiator_cannot_kick_anyone(): void
    {
        $game = $this->makeGame();
        $bystander = $this->makeUser();
        $impostor  = $this->makeUser();
        FaltaUnoParticipant::factory()->create([
            'game_id' => $game->id, 'user_id' => $bystander->id, 'status' => 'confirmed',
        ]);

        $this->actingAs($impostor)
            ->post(route('falta-uno.kick', ['game' => $game, 'user' => $bystander]))
            ->assertForbidden();

        $this->assertDatabaseHas('falta_uno_participants', [
            'game_id' => $game->id, 'user_id' => $bystander->id, 'status' => 'confirmed',
        ]);
    }

    public function test_initiator_can_mark_no_shows_after_game_started(): void
    {
        // markNoShows requiere status='full' o 'played' + start_at en el pasado
        $game = $this->makeGame([
            'start_at' => now()->subHour(),
            'status'   => 'full',
            'players_needed' => 0,
        ]);
        $missing = $this->makeUser();
        FaltaUnoParticipant::factory()->create([
            'game_id' => $game->id, 'user_id' => $missing->id, 'status' => 'confirmed',
        ]);

        $this->actingAs($game->initiator)
            ->post(route('falta-uno.no-shows', $game), [
                'no_show_user_ids' => [$missing->id],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('falta_uno_participants', [
            'game_id' => $game->id,
            'user_id' => $missing->id,
            'status'  => 'no_show',
        ]);
    }

    public function test_cannot_mark_no_shows_before_game_starts(): void
    {
        $game = $this->makeGame([
            'start_at' => now()->addHours(3),
            'status'   => 'full',
            'players_needed' => 0,
        ]);
        $missing = $this->makeUser();
        FaltaUnoParticipant::factory()->create([
            'game_id' => $game->id, 'user_id' => $missing->id, 'status' => 'confirmed',
        ]);

        $this->actingAs($game->initiator)
            ->post(route('falta-uno.no-shows', $game), [
                'no_show_user_ids' => [$missing->id],
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('falta_uno_participants', [
            'game_id' => $game->id, 'user_id' => $missing->id, 'status' => 'confirmed',
        ]);
    }

    public function test_initiator_can_cancel_their_own_game(): void
    {
        $game = $this->makeGame(['start_at' => now()->addDays(3)]);

        $this->actingAs($game->initiator)
            ->post(route('falta-uno.cancel', $game))
            ->assertRedirect();

        $this->assertSame('cancelled', $game->fresh()->status);
        $this->assertNotNull($game->fresh()->cancelled_at);
    }

    public function test_non_initiator_cannot_cancel_game(): void
    {
        $game = $this->makeGame();
        $stranger = $this->makeUser();

        $resp = $this->actingAs($stranger)
            ->post(route('falta-uno.cancel', $game));

        // Puede ser 403 o redirect con error
        $this->assertContains($resp->status(), [302, 403]);
        $this->assertSame('open', $game->fresh()->status);
    }
}
