<?php

namespace Tests\Feature;

use App\Models\FaltaUnoGame;
use App\Models\FaltaUnoRating;
use App\Models\FaltaUnoSportProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * El observer FaltaUnoRatingObserver dispara recalculateStats + recalculateCategory
 * cuando se crea, modifica o elimina un rating.
 *
 * Garantiza que el promedio del perfil del jugador refleja siempre los ratings actuales.
 */
class FaltaUnoRatingObserverTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    public function test_creating_a_rating_recalculates_rated_user_profile(): void
    {
        $field = $this->makeField(); // football
        $rated = $this->makeUser();
        $rater = $this->makeUser();
        $game  = FaltaUnoGame::factory()->create(['field_id' => $field->id]);

        $profile = FaltaUnoSportProfile::factory()->create([
            'user_id'        => $rated->id,
            'sport'          => 'football',
            'average_rating' => 0,
        ]);

        FaltaUnoRating::factory()->above()->create([
            'game_id'       => $game->id,
            'rater_user_id' => $rater->id,
            'rated_user_id' => $rated->id,
        ]);

        // Después del observer: el rating "above" da score 5 → average_rating = 5
        $this->assertEquals(5.0, (float) $profile->fresh()->average_rating);
    }

    public function test_updating_a_rating_recalculates_profile(): void
    {
        $field = $this->makeField();
        $rated = $this->makeUser();
        $rater = $this->makeUser();
        $game  = FaltaUnoGame::factory()->create(['field_id' => $field->id]);

        $profile = FaltaUnoSportProfile::factory()->create(['user_id' => $rated->id, 'sport' => 'football']);

        $rating = FaltaUnoRating::factory()->above()->create([
            'game_id' => $game->id, 'rater_user_id' => $rater->id, 'rated_user_id' => $rated->id,
        ]);

        $this->assertEquals(5.0, (float) $profile->fresh()->average_rating);

        // Cambiar a "below" (score 1)
        $rating->update(['assessment' => 'below']);

        $this->assertEquals(1.0, (float) $profile->fresh()->average_rating);
    }

    public function test_deleting_a_rating_recalculates_profile(): void
    {
        $field = $this->makeField();
        $rated = $this->makeUser();
        $rater = $this->makeUser();
        $game  = FaltaUnoGame::factory()->create(['field_id' => $field->id]);

        $profile = FaltaUnoSportProfile::factory()->create(['user_id' => $rated->id, 'sport' => 'football']);

        $r1 = FaltaUnoRating::factory()->above()->create([
            'game_id' => $game->id, 'rater_user_id' => $rater->id, 'rated_user_id' => $rated->id,
        ]);
        $r2 = FaltaUnoRating::factory()->below()->create([
            'game_id' => $game->id, 'rater_user_id' => $this->makeUser()->id, 'rated_user_id' => $rated->id,
        ]);

        // Promedio: (5 + 1) / 2 = 3
        $this->assertEquals(3.0, (float) $profile->fresh()->average_rating);

        $r2->delete();

        // Sólo queda el "above" → 5
        $this->assertEquals(5.0, (float) $profile->fresh()->average_rating);
    }

    public function test_observer_does_nothing_when_no_profile_exists(): void
    {
        $field = $this->makeField();
        $rated = $this->makeUser();
        $rater = $this->makeUser();
        $game  = FaltaUnoGame::factory()->create(['field_id' => $field->id]);

        // No hay profile creado para $rated → no debe explotar
        FaltaUnoRating::factory()->above()->create([
            'game_id' => $game->id, 'rater_user_id' => $rater->id, 'rated_user_id' => $rated->id,
        ]);

        $this->assertDatabaseCount('falta_uno_sport_profiles', 0);
    }

    public function test_observer_only_affects_matching_sport_profile(): void
    {
        $footballField = $this->makeField();
        $tennisField   = $this->makeField(null, ['sport' => 'tennis']);
        $rated = $this->makeUser();
        $rater = $this->makeUser();

        $footballProfile = FaltaUnoSportProfile::factory()->create([
            'user_id' => $rated->id, 'sport' => 'football',
        ]);
        $tennisProfile = FaltaUnoSportProfile::factory()->create([
            'user_id' => $rated->id, 'sport' => 'tennis',
        ]);

        $tennisGame = FaltaUnoGame::factory()->create(['field_id' => $tennisField->id]);
        FaltaUnoRating::factory()->above()->create([
            'game_id' => $tennisGame->id,
            'rater_user_id' => $rater->id,
            'rated_user_id' => $rated->id,
        ]);

        // Sólo el perfil de tennis se actualiza, el de football queda en 0
        $this->assertEquals(5.0, (float) $tennisProfile->fresh()->average_rating);
        $this->assertEquals(0.0, (float) $footballProfile->fresh()->average_rating);
    }
}
