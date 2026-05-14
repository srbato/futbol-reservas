<?php

namespace Tests\Feature;

use App\Models\FaltaUnoGame;
use App\Models\FaltaUnoParticipant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * Chat de partidos Falta Uno:
 *  - Solo participantes (initiator + confirmed) pueden ver y enviar mensajes
 *  - No-participants → 403
 *  - Chat cerrado en games cancelled/expired/finished
 *  - Validación: body requerido, max 1000 chars
 */
class FaltaUnoChatTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    public function test_initiator_can_view_chat(): void
    {
        $field = $this->makeField();
        $game  = FaltaUnoGame::factory()->create(['field_id' => $field->id]);

        $this->actingAs($game->initiator)
            ->get(route('falta-uno.chat', $game))
            ->assertOk();
    }

    public function test_confirmed_participant_can_view_chat(): void
    {
        $field = $this->makeField();
        $game  = FaltaUnoGame::factory()->create(['field_id' => $field->id]);
        $user  = $this->makeUser();
        FaltaUnoParticipant::factory()->create([
            'game_id' => $game->id, 'user_id' => $user->id, 'status' => 'confirmed',
        ]);

        $this->actingAs($user)
            ->get(route('falta-uno.chat', $game))
            ->assertOk();
    }

    public function test_non_participant_cannot_view_chat(): void
    {
        $field = $this->makeField();
        $game  = FaltaUnoGame::factory()->create(['field_id' => $field->id]);
        $stranger = $this->makeUser();

        $this->actingAs($stranger)
            ->get(route('falta-uno.chat', $game))
            ->assertForbidden();
    }

    public function test_cancelled_participant_cannot_view_chat(): void
    {
        $field = $this->makeField();
        $game  = FaltaUnoGame::factory()->create(['field_id' => $field->id]);
        $user  = $this->makeUser();
        FaltaUnoParticipant::factory()->create([
            'game_id' => $game->id, 'user_id' => $user->id, 'status' => 'cancelled',
        ]);

        $this->actingAs($user)
            ->get(route('falta-uno.chat', $game))
            ->assertForbidden();
    }

    public function test_initiator_can_send_message(): void
    {
        $field = $this->makeField();
        $game  = FaltaUnoGame::factory()->create(['field_id' => $field->id]);

        $this->actingAs($game->initiator)
            ->post(route('falta-uno.chat.store', $game), ['body' => 'Hola equipo!'])
            ->assertOk();

        $this->assertDatabaseHas('falta_uno_messages', [
            'game_id' => $game->id,
            'user_id' => $game->initiator_user_id,
            'body'    => 'Hola equipo!',
        ]);
    }

    public function test_message_validation_requires_body(): void
    {
        $field = $this->makeField();
        $game  = FaltaUnoGame::factory()->create(['field_id' => $field->id]);

        $this->actingAs($game->initiator)
            ->postJson(route('falta-uno.chat.store', $game), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['body']);
    }

    public function test_message_max_1000_chars(): void
    {
        $field = $this->makeField();
        $game  = FaltaUnoGame::factory()->create(['field_id' => $field->id]);

        $this->actingAs($game->initiator)
            ->postJson(route('falta-uno.chat.store', $game), [
                'body' => str_repeat('a', 1001),
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['body']);
    }

    public function test_chat_closed_for_cancelled_game(): void
    {
        $field = $this->makeField();
        $game  = FaltaUnoGame::factory()->cancelled()->create(['field_id' => $field->id]);

        $this->actingAs($game->initiator)
            ->postJson(route('falta-uno.chat.store', $game), ['body' => 'tarde'])
            ->assertStatus(422);
    }

    public function test_non_participant_cannot_send_message(): void
    {
        $field = $this->makeField();
        $game  = FaltaUnoGame::factory()->create(['field_id' => $field->id]);
        $stranger = $this->makeUser();

        $this->actingAs($stranger)
            ->post(route('falta-uno.chat.store', $game), ['body' => 'hack'])
            ->assertForbidden();

        $this->assertDatabaseCount('falta_uno_messages', 0);
    }
}
