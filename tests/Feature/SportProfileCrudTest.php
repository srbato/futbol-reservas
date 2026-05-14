<?php

namespace Tests\Feature;

use App\Models\FaltaUnoSportProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * CRUD del perfil deportivo del usuario (FaltaUnoSportProfileController):
 *  - User crea perfil de un deporte (con category válida del deporte)
 *  - No se puede crear 2 perfiles del mismo deporte
 *  - User edita perfil propio
 *  - Bloquea cambio de category cuando ya tiene 3+ partidos (anti-cheating)
 *  - Validación de gender, category, sport
 *  - 404 al editar perfil que no existe
 */
class SportProfileCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_sport_profile(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->post(route('sport-profile.store'), [
                'sport'    => 'football',
                'category' => 'intermedio',
                'gender'   => 'male',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('falta_uno_sport_profiles', [
            'user_id'  => $user->id,
            'sport'    => 'football',
            'category' => 'intermedio',
            'gender'   => 'male',
        ]);
    }

    public function test_cannot_create_two_profiles_for_same_sport(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        FaltaUnoSportProfile::factory()->create([
            'user_id' => $user->id, 'sport' => 'football',
        ]);

        $this->actingAs($user)
            ->post(route('sport-profile.store'), [
                'sport'    => 'football',
                'category' => 'avanzado',
                'gender'   => 'male',
            ])
            ->assertSessionHas('error');

        $this->assertSame(1, FaltaUnoSportProfile::where('user_id', $user->id)->where('sport', 'football')->count());
    }

    public function test_validates_sport_must_be_in_allowed_list(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->post(route('sport-profile.store'), [
                'sport'    => 'cricket', // no permitido
                'category' => 'intermedio',
                'gender'   => 'male',
            ])
            ->assertSessionHasErrors(['sport']);
    }

    public function test_validates_category_must_belong_to_sport(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        // 'primera' es de padel, no de football
        $this->actingAs($user)
            ->post(route('sport-profile.store'), [
                'sport'    => 'football',
                'category' => 'primera',
                'gender'   => 'male',
            ])
            ->assertSessionHasErrors(['category']);
    }

    public function test_validates_gender_must_be_male_or_female(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->post(route('sport-profile.store'), [
                'sport'    => 'football',
                'category' => 'intermedio',
                'gender'   => 'other',
            ])
            ->assertSessionHasErrors(['gender']);
    }

    public function test_user_can_edit_own_profile(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $profile = FaltaUnoSportProfile::factory()->create([
            'user_id' => $user->id, 'sport' => 'football',
            'category' => 'recreativo', 'games_played' => 2,
        ]);

        $this->actingAs($user)
            ->put(route('sport-profile.update', 'football'), [
                'category' => 'intermedio',
                'gender'   => 'male',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame('intermedio', $profile->fresh()->category);
    }

    public function test_cannot_change_category_when_3_or_more_games_played(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $profile = FaltaUnoSportProfile::factory()->create([
            'user_id' => $user->id, 'sport' => 'football',
            'category' => 'intermedio', 'games_played' => 3,
        ]);

        $this->actingAs($user)
            ->put(route('sport-profile.update', 'football'), [
                'category' => 'avanzado',
                'gender'   => 'male',
            ])
            ->assertSessionHasErrors(['category']);

        $this->assertSame('intermedio', $profile->fresh()->category);
    }

    public function test_can_still_update_gender_with_3_games_played(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $profile = FaltaUnoSportProfile::factory()->create([
            'user_id' => $user->id, 'sport' => 'football',
            'category' => 'intermedio', 'gender' => 'male', 'games_played' => 5,
        ]);

        $this->actingAs($user)
            ->put(route('sport-profile.update', 'football'), [
                'category' => 'intermedio', // sin cambio
                'gender'   => 'female',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame('female', $profile->fresh()->gender);
    }

    public function test_returns_404_when_editing_nonexistent_profile(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->get(route('sport-profile.edit', 'football'))
            ->assertNotFound();
    }

    public function test_index_responds_for_authed_user(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        // El index puede redirigir al profile principal (depende del setup) — lo crítico
        // es que devuelva 200 o 302 (no 401/403/500)
        $resp = $this->actingAs($user)->get(route('sport-profile.index'));
        $this->assertContains($resp->status(), [200, 302]);
    }
}
