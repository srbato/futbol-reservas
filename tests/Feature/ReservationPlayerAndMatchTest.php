<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\ReservationPlayer;
use App\Models\ReservationResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * Tag de jugadores en la reserva + carga de resultado del partido.
 *  - Owner agrega jugadores por email (case-insensitive, exact match anti-enumeration)
 *  - No auto-tag, no duplicados, no email inexistente
 *  - Owner remueve jugadores
 *  - updateResult: guarda outcome (W/D/L) por usuario, sólo si reserva PAID y ya pasó
 */
class ReservationPlayerAndMatchTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    public function test_owner_can_add_player_to_reservation_by_email(): void
    {
        $field = $this->makeField();
        $owner = $this->makeUser();
        $reservation = Reservation::factory()->create([
            'field_id' => $field->id, 'user_id' => $owner->id,
        ]);
        $player = $this->makeUser(['email' => 'player@test.com']);

        $this->actingAs($owner)
            ->post(route('reservations.players.store', $reservation), [
                'search' => 'player@test.com',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reservation_players', [
            'reservation_id' => $reservation->id,
            'user_id'        => $player->id,
        ]);
    }

    public function test_email_search_is_case_insensitive(): void
    {
        $field = $this->makeField();
        $owner = $this->makeUser();
        $reservation = Reservation::factory()->create([
            'field_id' => $field->id, 'user_id' => $owner->id,
        ]);
        $player = $this->makeUser(['email' => 'player@test.com']);

        $this->actingAs($owner)
            ->post(route('reservations.players.store', $reservation), [
                'search' => 'PLAYER@TEST.COM',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reservation_players', [
            'reservation_id' => $reservation->id, 'user_id' => $player->id,
        ]);
    }

    public function test_cannot_add_nonexistent_email(): void
    {
        $field = $this->makeField();
        $owner = $this->makeUser();
        $reservation = Reservation::factory()->create([
            'field_id' => $field->id, 'user_id' => $owner->id,
        ]);

        $this->actingAs($owner)
            ->post(route('reservations.players.store', $reservation), [
                'search' => 'noexiste@test.com',
            ])
            ->assertSessionHasErrors(['search']);

        $this->assertDatabaseCount('reservation_players', 0);
    }

    public function test_cannot_add_inactive_user(): void
    {
        $field = $this->makeField();
        $owner = $this->makeUser();
        $reservation = Reservation::factory()->create([
            'field_id' => $field->id, 'user_id' => $owner->id,
        ]);
        $this->makeUser(['email' => 'inactive@test.com', 'is_active' => false]);

        $this->actingAs($owner)
            ->post(route('reservations.players.store', $reservation), [
                'search' => 'inactive@test.com',
            ])
            ->assertSessionHasErrors(['search']);
    }

    public function test_cannot_add_self_as_player(): void
    {
        $field = $this->makeField();
        $owner = $this->makeUser();
        $reservation = Reservation::factory()->create([
            'field_id' => $field->id, 'user_id' => $owner->id,
        ]);

        $this->actingAs($owner)
            ->post(route('reservations.players.store', $reservation), [
                'search' => $owner->email,
            ])
            ->assertSessionHasErrors(['search']);
    }

    public function test_cannot_add_same_player_twice(): void
    {
        $field = $this->makeField();
        $owner = $this->makeUser();
        $reservation = Reservation::factory()->create([
            'field_id' => $field->id, 'user_id' => $owner->id,
        ]);
        $player = $this->makeUser(['email' => 'twice@test.com']);

        ReservationPlayer::create([
            'reservation_id'   => $reservation->id,
            'user_id'          => $player->id,
            'added_by_user_id' => $owner->id,
        ]);

        $this->actingAs($owner)
            ->post(route('reservations.players.store', $reservation), [
                'search' => 'twice@test.com',
            ])
            ->assertSessionHasErrors(['search']);

        $this->assertSame(1, ReservationPlayer::where('reservation_id', $reservation->id)->count());
    }

    public function test_only_owner_can_tag_players(): void
    {
        $field = $this->makeField();
        $owner = $this->makeUser();
        $reservation = Reservation::factory()->create([
            'field_id' => $field->id, 'user_id' => $owner->id,
        ]);
        $stranger = $this->makeUser();

        $this->actingAs($stranger)
            ->post(route('reservations.players.store', $reservation), [
                'search' => 'whatever@test.com',
            ])
            ->assertForbidden();
    }

    public function test_cannot_tag_players_on_unpaid_reservation(): void
    {
        $field = $this->makeField();
        $owner = $this->makeUser();
        $reservation = Reservation::factory()->pendingPayment()->create([
            'field_id' => $field->id, 'user_id' => $owner->id,
        ]);
        $this->makeUser(['email' => 'p@test.com']);

        $this->actingAs($owner)
            ->post(route('reservations.players.store', $reservation), [
                'search' => 'p@test.com',
            ])
            ->assertStatus(422);
    }

    public function test_owner_can_remove_player(): void
    {
        $field = $this->makeField();
        $owner = $this->makeUser();
        $reservation = Reservation::factory()->create([
            'field_id' => $field->id, 'user_id' => $owner->id,
        ]);
        $player = $this->makeUser();
        ReservationPlayer::create([
            'reservation_id'   => $reservation->id,
            'user_id'          => $player->id,
            'added_by_user_id' => $owner->id,
        ]);

        $this->actingAs($owner)
            ->post(route('reservations.players.destroy', ['reservation' => $reservation, 'player' => $player]))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('reservation_players', [
            'reservation_id' => $reservation->id, 'user_id' => $player->id,
        ]);
    }

    // ─── Match result ────────────────────────────────────────────────────

    public function test_owner_can_save_match_result_for_past_paid_reservation(): void
    {
        $field = $this->makeField();
        $owner = $this->makeUser();
        $reservation = Reservation::factory()->create([
            'field_id' => $field->id, 'user_id' => $owner->id,
            'start_at' => now()->subHours(2),
            'end_at'   => now()->subHour(),
        ]);

        $this->actingAs($owner)
            ->post(route('reservations.update_result', $reservation), [
                'match_result'  => '5-3',
                'match_outcome' => 'W',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reservation_results', [
            'reservation_id' => $reservation->id,
            'user_id'        => $owner->id,
            'match_result'   => '5-3',
            'match_outcome'  => 'W',
        ]);
    }

    public function test_cannot_save_result_for_future_reservation(): void
    {
        $field = $this->makeField();
        $owner = $this->makeUser();
        $reservation = Reservation::factory()->create([
            'field_id' => $field->id, 'user_id' => $owner->id,
            'start_at' => now()->addHours(2),
        ]);

        $this->actingAs($owner)
            ->post(route('reservations.update_result', $reservation), [
                'match_result'  => '5-3',
                'match_outcome' => 'W',
            ])
            ->assertStatus(422);
    }

    public function test_only_owner_or_tagged_player_can_save_result(): void
    {
        $field = $this->makeField();
        $owner = $this->makeUser();
        $reservation = Reservation::factory()->create([
            'field_id' => $field->id, 'user_id' => $owner->id,
            'start_at' => now()->subHours(2), 'end_at' => now()->subHour(),
        ]);
        $stranger = $this->makeUser();

        $this->actingAs($stranger)
            ->post(route('reservations.update_result', $reservation), [
                'match_result' => '1-0', 'match_outcome' => 'W',
            ])
            ->assertForbidden();
    }

    public function test_tagged_player_can_save_their_own_result(): void
    {
        $field = $this->makeField();
        $owner  = $this->makeUser();
        $player = $this->makeUser();
        $reservation = Reservation::factory()->create([
            'field_id' => $field->id, 'user_id' => $owner->id,
            'start_at' => now()->subHours(2), 'end_at' => now()->subHour(),
        ]);
        ReservationPlayer::create([
            'reservation_id'   => $reservation->id,
            'user_id'          => $player->id,
            'added_by_user_id' => $owner->id,
        ]);

        $this->actingAs($player)
            ->post(route('reservations.update_result', $reservation), [
                'match_outcome' => 'L',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reservation_results', [
            'reservation_id' => $reservation->id,
            'user_id'        => $player->id,
            'match_outcome'  => 'L',
        ]);
    }

    public function test_each_user_saves_their_own_result_independently(): void
    {
        $field = $this->makeField();
        $owner  = $this->makeUser();
        $player = $this->makeUser();
        $reservation = Reservation::factory()->create([
            'field_id' => $field->id, 'user_id' => $owner->id,
            'start_at' => now()->subHours(2), 'end_at' => now()->subHour(),
        ]);
        ReservationPlayer::create([
            'reservation_id' => $reservation->id, 'user_id' => $player->id, 'added_by_user_id' => $owner->id,
        ]);

        // Owner saves W, player saves L (perspectivas distintas)
        $this->actingAs($owner)->post(route('reservations.update_result', $reservation), ['match_outcome' => 'W']);
        $this->actingAs($player)->post(route('reservations.update_result', $reservation), ['match_outcome' => 'L']);

        $this->assertSame('W', ReservationResult::where('reservation_id', $reservation->id)->where('user_id', $owner->id)->first()->match_outcome);
        $this->assertSame('L', ReservationResult::where('reservation_id', $reservation->id)->where('user_id', $player->id)->first()->match_outcome);
    }

    public function test_match_history_index_renders_for_authed_user(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user)
            ->get(route('match_history'))
            ->assertOk();
    }

    public function test_match_history_index_requires_auth(): void
    {
        $this->get(route('match_history'))
            ->assertRedirect(route('login'));
    }
}
