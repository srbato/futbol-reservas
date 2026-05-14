<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tournaments están detrás del middleware `torneos.soon` que muestra coming-soon.
 * Verificamos que el feature flag funcione: todas las rutas de tournament/organizer
 * deben devolver la vista coming-soon en vez de exponer la funcionalidad.
 */
class TournamentsComingSoonTest extends TestCase
{
    use RefreshDatabase;

    public function test_tournaments_index_shows_coming_soon(): void
    {
        $resp = $this->get(route('torneos.index'));
        $resp->assertOk();
        // La vista renderizada es la coming-soon
        $resp->assertViewIs('torneos.coming-soon');
    }

    public function test_tournament_create_shows_coming_soon_for_authed_users(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->get(route('torneos.create'))
            ->assertOk()
            ->assertViewIs('torneos.coming-soon');
    }

    public function test_organizer_planes_shows_coming_soon(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->get(route('organizador.planes'))
            ->assertOk()
            ->assertViewIs('torneos.coming-soon');
    }
}
