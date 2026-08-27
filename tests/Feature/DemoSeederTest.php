<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\FaltaUnoGame;
use App\Models\Field;
use App\Models\User;
use App\Models\Venue;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Verifica que el seeder de desarrollo deje la app navegable.
 *
 * Es la primera experiencia de cualquiera que clone el proyecto: si esto se
 * rompe, quien levante el entorno se encuentra la app vacía sin saber por qué.
 */
class DemoSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        // El seeder se bloquea si APP_URL parece productiva.
        config(['app.url' => 'http://localhost']);
    }

    public function test_seeder_creates_a_navigable_environment(): void
    {
        $this->seed(DatabaseSeeder::class);

        // Usuarios base
        $this->assertDatabaseHas('users', ['email' => 'usuario1@test.com']);
        $this->assertGreaterThanOrEqual(6, User::count());

        // Complejos y canchas
        $this->assertGreaterThanOrEqual(2, Venue::count());
        $this->assertGreaterThanOrEqual(5, Field::count());

        // Toda cancha debe tener precio y horarios, si no no se puede reservar
        foreach (Field::with(['price', 'schedules'])->get() as $field) {
            $this->assertNotNull($field->price, "La cancha {$field->name} no tiene precio");
            $this->assertCount(7, $field->schedules, "La cancha {$field->name} no tiene los 7 días de horario");
        }

        // Contenido
        $this->assertGreaterThanOrEqual(1, FaltaUnoGame::where('status', 'open')->count());
        $this->assertGreaterThanOrEqual(3, BlogPost::where('status', 'published')->count());
    }

    public function test_seeded_venues_are_visible_on_the_public_listing(): void
    {
        $this->seed(DatabaseSeeder::class);

        // withActiveOwner() esconde los complejos cuyo dueño no tiene
        // suscripción vigente: sin ella el listado sale vacío.
        $visible = Venue::where('is_active', true)->withActiveOwner()->count();

        $this->assertGreaterThanOrEqual(2, $visible, 'Los complejos del seeder no son visibles públicamente');
    }

    public function test_seeded_falta_uno_games_appear_in_the_feed(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('falta-uno.index'))
            ->assertOk()
            ->assertViewHas('games', fn ($games) => $games->isNotEmpty());
    }
}
