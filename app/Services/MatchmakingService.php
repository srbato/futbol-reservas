<?php

namespace App\Services;

use App\Models\FaltaUnoGame;
use App\Models\FaltaUnoParticipant;
use App\Models\FaltaUnoSportProfile;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Collection;

class MatchmakingService
{
    /**
     * Score weights for each matching criterion.
     */
    private const WEIGHT_SPORT    = 30;
    private const WEIGHT_CATEGORY = 25;
    private const WEIGHT_GENDER   = 20;
    private const WEIGHT_VENUE    = 15;
    private const WEIGHT_HOUR     = 10;

    /**
     * Get recommended Falta Uno games for a user.
     */
    public function getRecommendations(User $user, int $limit = 4): Collection
    {
        $sportProfiles = $user->faltaUnoSportProfiles;

        if ($sportProfiles->isEmpty()) {
            return collect();
        }

        // IDs of games where user is already a participant or initiator
        $participatingGameIds = FaltaUnoParticipant::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->pluck('game_id');

        // Fetch open games with future start_at
        $games = FaltaUnoGame::with([
                'field.venue',
                'field.faltaUnoSetting',
                'activeParticipants.user',
                'reservation',
            ])
            ->where('status', 'open')
            ->where(function ($q) {
                $q->whereHas('reservation', fn($rq) => $rq->where('status', 'PAID'))
                  ->orWhereNull('reservation_id');
            })
            ->where('start_at', '>', now())
            ->where('initiator_user_id', '!=', $user->id)
            ->whereNotIn('id', $participatingGameIds)
            ->orderBy('start_at')
            ->get();

        if ($games->isEmpty()) {
            return collect();
        }

        // Build user context for scoring
        $userContext = $this->buildUserContext($user, $sportProfiles);

        // Score each game
        $scored = $games->map(function (FaltaUnoGame $game) use ($userContext) {
            $score = $this->calculateScore($game, $userContext);
            return ['game' => $game, 'score' => $score];
        })
        ->filter(fn($item) => $item['score'] > 0) // exclude 0-score (e.g. gender mismatch)
        ->sortByDesc('score')
        ->take($limit);

        return $scored->pluck('game')->values();
    }

    /**
     * Build context from user's profiles and history for scoring.
     */
    private function buildUserContext(User $user, Collection $sportProfiles): array
    {
        // Sports and categories the user plays
        $sports = [];
        foreach ($sportProfiles as $profile) {
            $sports[$profile->sport] = [
                'category' => $profile->category,
                'gender'   => $profile->gender,
            ];
        }

        // Venue IDs where the user has played before (reservations + falta uno participations)
        $reservationVenueIds = Reservation::where('user_id', $user->id)
            ->whereIn('status', ['PAID', 'COMPLETED'])
            ->with('field')
            ->get()
            ->pluck('field.venue_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $participationVenueIds = FaltaUnoParticipant::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->with('game.field')
            ->get()
            ->pluck('game.field.venue_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // Also include venues from games initiated by user
        $initiatedVenueIds = FaltaUnoGame::where('initiator_user_id', $user->id)
            ->with('field')
            ->get()
            ->pluck('field.venue_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $knownVenueIds = array_unique(array_merge($reservationVenueIds, $participationVenueIds, $initiatedVenueIds));

        // Preferred hours: calculate from reservation history
        $preferredHours = $this->calculatePreferredHours($user);

        return [
            'sports'        => $sports,
            'knownVenueIds' => $knownVenueIds,
            'preferredHours' => $preferredHours,
        ];
    }

    /**
     * Calculate the hours the user typically plays (from reservations and falta uno games).
     * Returns an array of hours (0-23) sorted by frequency.
     */
    private function calculatePreferredHours(User $user): array
    {
        // Hours from reservations
        $reservationHours = Reservation::where('user_id', $user->id)
            ->whereIn('status', ['PAID', 'COMPLETED'])
            ->pluck('start_at')
            ->map(fn($dt) => $dt->hour);

        // Hours from falta uno participations
        $gameHours = FaltaUnoParticipant::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->with('game')
            ->get()
            ->pluck('game.start_at')
            ->filter()
            ->map(fn($dt) => $dt->hour);

        $allHours = $reservationHours->merge($gameHours);

        if ($allHours->isEmpty()) {
            return [];
        }

        // Count frequency and return top hours
        return $allHours->countBy()
            ->sortDesc()
            ->keys()
            ->take(5)
            ->toArray();
    }

    /**
     * Calculate a relevance score for a game given the user context.
     */
    private function calculateScore(FaltaUnoGame $game, array $userContext): int
    {
        $score = 0;
        $gameSport = $game->field->sport ?? null;

        // 1. Gender filter check (hard filter: if incompatible, return 0)
        if ($game->gender_filter && $game->gender_filter !== 'mixed') {
            $userGenderForSport = $userContext['sports'][$gameSport]['gender'] ?? null;
            if ($userGenderForSport && $userGenderForSport !== $game->gender_filter) {
                return 0; // hard exclusion
            }
        }
        $score += self::WEIGHT_GENDER;

        // 2. Sport match
        if ($gameSport && isset($userContext['sports'][$gameSport])) {
            $score += self::WEIGHT_SPORT;

            // 3. Category match (only relevant if sport matches)
            $userCategory = $userContext['sports'][$gameSport]['category'] ?? null;
            if ($userCategory) {
                if ($game->isInCategoryRange($userCategory)) {
                    $score += self::WEIGHT_CATEGORY;
                }
            } else {
                // No category restriction on game, full match
                if (!$game->category_min && !$game->category_max) {
                    $score += self::WEIGHT_CATEGORY;
                }
            }
        }

        // 4. Known venue bonus
        $venueId = $game->field->venue_id ?? null;
        if ($venueId && in_array($venueId, $userContext['knownVenueIds'])) {
            $score += self::WEIGHT_VENUE;
        }

        // 5. Preferred hour bonus
        if (!empty($userContext['preferredHours'])) {
            $gameHour = $game->start_at->hour;
            if (in_array($gameHour, $userContext['preferredHours'])) {
                $score += self::WEIGHT_HOUR;
            } elseif (in_array($gameHour - 1, $userContext['preferredHours']) || in_array($gameHour + 1, $userContext['preferredHours'])) {
                // Adjacent hour gets partial credit
                $score += (int) round(self::WEIGHT_HOUR * 0.5);
            }
        }

        return $score;
    }
}
