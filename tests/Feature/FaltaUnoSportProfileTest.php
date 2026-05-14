<?php

namespace Tests\Feature;

use App\Models\FaltaUnoGame;
use App\Models\FaltaUnoParticipant;
use App\Models\FaltaUnoRating;
use App\Models\FaltaUnoSportProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * Cobertura de FaltaUnoSportProfile.
 *  - recalculateStats() agrega games_played, wins/draws/losses, average_rating, attendance_rate
 *  - recalculateCategory() sube/baja al jugador según los últimos 10 ratings (umbrales 60%)
 *  - average_rating: below=1, match=3, above=5 (escala /5)
 *  - attendance_rate: confirmed sin late_leave / total_attendance
 */
class FaltaUnoSportProfileTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    public function test_recalculate_stats_with_no_games_returns_zeros(): void
    {
        $user = $this->makeUser();
        $profile = FaltaUnoSportProfile::factory()->create(['user_id' => $user->id, 'sport' => 'football']);

        $profile->recalculateStats();

        $profile->refresh();
        $this->assertSame(0, $profile->games_played);
        $this->assertSame(0, $profile->wins);
        $this->assertEquals(0.0, (float) $profile->average_rating);
        $this->assertEquals(100.0, (float) $profile->attendance_rate); // default cuando no hay datos
    }

    public function test_recalculate_stats_counts_falta_uno_results(): void
    {
        $field = $this->makeField(); // sport=football por default
        $user  = $this->makeUser();
        $profile = FaltaUnoSportProfile::factory()->create(['user_id' => $user->id, 'sport' => 'football']);

        // 2 wins, 1 draw, 1 loss en 4 partidos confirmed
        $game1 = FaltaUnoGame::factory()->create(['field_id' => $field->id]);
        $game2 = FaltaUnoGame::factory()->create(['field_id' => $field->id]);
        $game3 = FaltaUnoGame::factory()->create(['field_id' => $field->id]);
        $game4 = FaltaUnoGame::factory()->create(['field_id' => $field->id]);

        FaltaUnoParticipant::factory()->withResult('win')->create(['game_id' => $game1->id, 'user_id' => $user->id]);
        FaltaUnoParticipant::factory()->withResult('win')->create(['game_id' => $game2->id, 'user_id' => $user->id]);
        FaltaUnoParticipant::factory()->withResult('draw')->create(['game_id' => $game3->id, 'user_id' => $user->id]);
        FaltaUnoParticipant::factory()->withResult('loss')->create(['game_id' => $game4->id, 'user_id' => $user->id]);

        $profile->recalculateStats()->refresh();
        $this->assertSame(4, $profile->games_played);
        $this->assertSame(2, $profile->wins);
        $this->assertSame(1, $profile->draws);
        $this->assertSame(1, $profile->losses);
    }

    public function test_recalculate_stats_only_counts_games_in_matching_sport(): void
    {
        $footballField = $this->makeField();
        $tennisField   = $this->makeField(null, ['sport' => 'tennis']);
        $user = $this->makeUser();
        $profile = FaltaUnoSportProfile::factory()->create(['user_id' => $user->id, 'sport' => 'football']);

        $g1 = FaltaUnoGame::factory()->create(['field_id' => $footballField->id]);
        $g2 = FaltaUnoGame::factory()->create(['field_id' => $tennisField->id]);
        FaltaUnoParticipant::factory()->withResult('win')->create(['game_id' => $g1->id, 'user_id' => $user->id]);
        FaltaUnoParticipant::factory()->withResult('win')->create(['game_id' => $g2->id, 'user_id' => $user->id]);

        $profile->recalculateStats()->refresh();
        $this->assertSame(1, $profile->games_played); // sólo football
        $this->assertSame(1, $profile->wins);
    }

    public function test_recalculate_stats_average_rating_uses_below_match_above_scale(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $profile = FaltaUnoSportProfile::factory()->create(['user_id' => $user->id, 'sport' => 'football']);

        // 2 above (5) + 2 match (3) + 1 below (1) → avg = (5+5+3+3+1)/5 = 3.4
        $game = FaltaUnoGame::factory()->create(['field_id' => $field->id]);
        for ($i = 0; $i < 2; $i++) {
            FaltaUnoRating::factory()->above()->create([
                'game_id' => $game->id, 'rated_user_id' => $user->id, 'rater_user_id' => $this->makeUser()->id,
            ]);
        }
        for ($i = 0; $i < 2; $i++) {
            FaltaUnoRating::factory()->match()->create([
                'game_id' => $game->id, 'rated_user_id' => $user->id, 'rater_user_id' => $this->makeUser()->id,
            ]);
        }
        FaltaUnoRating::factory()->below()->create([
            'game_id' => $game->id, 'rated_user_id' => $user->id, 'rater_user_id' => $this->makeUser()->id,
        ]);

        $profile->recalculateStats()->refresh();
        $this->assertEquals(3.4, (float) $profile->average_rating);
    }

    public function test_attendance_rate_drops_with_no_shows_and_late_leaves(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $profile = FaltaUnoSportProfile::factory()->create(['user_id' => $user->id, 'sport' => 'football']);

        // 3 confirmed (presencia perfecta) + 1 late_leave + 1 no_show
        // attendance = confirmed_sin_late / total = 3 / 5 = 60%
        $games = collect(range(1, 5))->map(fn () => FaltaUnoGame::factory()->create(['field_id' => $field->id]));

        FaltaUnoParticipant::factory()->create(['game_id' => $games[0]->id, 'user_id' => $user->id]);
        FaltaUnoParticipant::factory()->create(['game_id' => $games[1]->id, 'user_id' => $user->id]);
        FaltaUnoParticipant::factory()->create(['game_id' => $games[2]->id, 'user_id' => $user->id]);
        FaltaUnoParticipant::factory()->lateLeave()->create(['game_id' => $games[3]->id, 'user_id' => $user->id]);
        FaltaUnoParticipant::factory()->noShow()->create(['game_id' => $games[4]->id, 'user_id' => $user->id]);

        $profile->recalculateStats()->refresh();
        $this->assertEquals(60.0, (float) $profile->attendance_rate);
        $this->assertSame(1, $profile->late_leaves_count);
    }

    public function test_average_rating_is_zero_when_no_ratings(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $profile = FaltaUnoSportProfile::factory()->create(['user_id' => $user->id, 'sport' => 'football']);

        // Sólo participar, sin ratings
        $game = FaltaUnoGame::factory()->create(['field_id' => $field->id]);
        FaltaUnoParticipant::factory()->withResult('win')->create(['game_id' => $game->id, 'user_id' => $user->id]);

        $profile->recalculateStats()->refresh();
        $this->assertEquals(0.0, (float) $profile->average_rating);
    }

    public function test_recalculate_category_returns_null_when_less_than_5_games(): void
    {
        $user = $this->makeUser();
        $profile = FaltaUnoSportProfile::factory()->create([
            'user_id' => $user->id, 'sport' => 'football', 'games_played' => 4, 'category' => 'intermedio',
        ]);

        $this->assertNull($profile->recalculateCategory());
    }

    public function test_recalculate_category_promotes_when_above_ratio_high(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $profile = FaltaUnoSportProfile::factory()->create([
            'user_id' => $user->id, 'sport' => 'football',
            'games_played' => 10, 'category' => 'intermedio',
        ]);

        $game = FaltaUnoGame::factory()->create(['field_id' => $field->id]);
        // 7 above, 3 match → 70% above (≥60% threshold)
        for ($i = 0; $i < 7; $i++) {
            FaltaUnoRating::factory()->above()->create([
                'game_id' => $game->id, 'rated_user_id' => $user->id, 'rater_user_id' => $this->makeUser()->id,
            ]);
        }
        for ($i = 0; $i < 3; $i++) {
            FaltaUnoRating::factory()->match()->create([
                'game_id' => $game->id, 'rated_user_id' => $user->id, 'rater_user_id' => $this->makeUser()->id,
            ]);
        }

        $result = $profile->recalculateCategory();
        $this->assertNotNull($result);
        // El método devuelve algo como ['old' => ..., 'new' => ..., 'direction' => 'up']
        $direction = $result['direction'] ?? null;
        $this->assertSame('up', $direction);
    }

    public function test_recalculate_category_demotes_when_below_ratio_high(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $profile = FaltaUnoSportProfile::factory()->create([
            'user_id' => $user->id, 'sport' => 'football',
            'games_played' => 10, 'category' => 'intermedio',
        ]);

        $game = FaltaUnoGame::factory()->create(['field_id' => $field->id]);
        for ($i = 0; $i < 8; $i++) {
            FaltaUnoRating::factory()->below()->create([
                'game_id' => $game->id, 'rated_user_id' => $user->id, 'rater_user_id' => $this->makeUser()->id,
            ]);
        }
        for ($i = 0; $i < 2; $i++) {
            FaltaUnoRating::factory()->match()->create([
                'game_id' => $game->id, 'rated_user_id' => $user->id, 'rater_user_id' => $this->makeUser()->id,
            ]);
        }

        $result = $profile->recalculateCategory();
        $this->assertNotNull($result);
        $this->assertSame('down', $result['direction'] ?? null);
    }

    public function test_recalculate_stats_does_not_count_cancelled_participants(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $profile = FaltaUnoSportProfile::factory()->create(['user_id' => $user->id, 'sport' => 'football']);

        $game1 = FaltaUnoGame::factory()->create(['field_id' => $field->id]);
        $game2 = FaltaUnoGame::factory()->create(['field_id' => $field->id]);
        FaltaUnoParticipant::factory()->withResult('win')->create(['game_id' => $game1->id, 'user_id' => $user->id]);
        // Cancelado: NO debe contar
        FaltaUnoParticipant::factory()->create([
            'game_id' => $game2->id, 'user_id' => $user->id, 'status' => 'cancelled',
        ]);

        $profile->recalculateStats()->refresh();
        $this->assertSame(1, $profile->games_played);
    }
}
