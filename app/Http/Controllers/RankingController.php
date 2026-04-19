<?php

namespace App\Http\Controllers;

use App\Models\FaltaUnoParticipant;
use App\Models\FaltaUnoSportProfile;
use App\Models\ReservationResult;
use App\Services\BadgeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RankingController extends Controller
{
    public function index(Request $request)
    {
        $sportFilter    = $request->query('sport');
        $searchQuery    = trim($request->query('q', ''));
        $categoryFilter = $request->query('category');

        $sportLabels = [
            'football'   => 'Fútbol',
            'padel'      => 'Pádel',
            'tennis'     => 'Tenis',
            'basketball' => 'Básquet',
            'volleyball' => 'Vóley',
        ];

        // Base query: all sport profiles
        $query = FaltaUnoSportProfile::with('user');

        if ($sportFilter && array_key_exists($sportFilter, $sportLabels)) {
            $query->where('sport', $sportFilter);
        }

        if ($categoryFilter) {
            $query->where('category', $categoryFilter);
        }

        if ($searchQuery !== '') {
            $query->whereHas('user', function ($q) use ($searchQuery) {
                // Case-insensitive match (portable across Postgres/SQLite/MySQL)
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . mb_strtolower($searchQuery) . '%']);
            });
        }

        $profiles = $query->get();

        // Batch-load stats for all users involved
        $userIds = $profiles->pluck('user_id')->unique()->toArray();

        $allParticipations = FaltaUnoParticipant::with('game.field')
            ->whereIn('user_id', $userIds)
            ->where('status', 'confirmed')
            ->get()
            ->groupBy('user_id');

        $allConventionalResults = ReservationResult::with('reservation.field')
            ->whereIn('user_id', $userIds)
            ->whereNotNull('match_outcome')
            ->get()
            ->groupBy('user_id');

        // Batch-load all ratings received by these users (across all sports).
        // Usado para calcular el rating global (agregado) — mismo cálculo que en el perfil público.
        $allRatingsByUser = \App\Models\FaltaUnoRating::whereIn('rated_user_id', $userIds)
            ->get()
            ->groupBy('rated_user_id');

        $badgeService = new BadgeService();

        // Compute ranked profiles (public ranking — min 3 games)
        $rankedProfiles = $profiles->map(fn($profile) => $this->buildProfileStats(
            $profile,
            $allParticipations,
            $allConventionalResults,
            $allRatingsByUser,
            $badgeService
        ))
        ->filter(fn($item) => $item->games_played >= 3)
        ->sortByDesc('score')
        ->values();

        // ── Top 3 for podium (from filtered ranking)
        $top3 = $rankedProfiles->take(3);

        // ── Self card data: user's own stats
        $selfData = null;
        if (Auth::check()) {
            $selfData = $this->buildSelfCard(
                Auth::user(),
                $sportFilter && array_key_exists($sportFilter, $sportLabels) ? $sportFilter : null,
                $rankedProfiles,
                $badgeService
            );
        }

        // ── Available categories for filter dropdown (based on sport)
        $availableCategories = [];
        if ($sportFilter && array_key_exists($sportFilter, $sportLabels)) {
            $availableCategories = FaltaUnoSportProfile::getCategoriesForSport($sportFilter);
        }

        // ── Sport counts (for tabs)
        $sportCounts = FaltaUnoSportProfile::selectRaw('sport, COUNT(*) as total')
            ->groupBy('sport')
            ->pluck('total', 'sport')
            ->toArray();
        $totalProfiles = array_sum($sportCounts);

        // ── Pagination
        $page    = max(1, (int) $request->query('page', 1));
        $perPage = 20;
        $total   = $rankedProfiles->count();
        $items   = $rankedProfiles->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => route('ranking.index'), 'query' => $request->query()]
        );

        return view('ranking.index', [
            'players'             => $paginator,
            'top3'                => $top3,
            'selfData'            => $selfData,
            'sportFilter'         => $sportFilter,
            'sportLabels'         => $sportLabels,
            'searchQuery'         => $searchQuery,
            'categoryFilter'      => $categoryFilter,
            'availableCategories' => $availableCategories,
            'sportCounts'         => $sportCounts,
            'totalProfiles'       => $totalProfiles,
            'page'                => $page,
        ]);
    }

    /**
     * Build a standardized stats object for a sport profile.
     */
    private function buildProfileStats(
        FaltaUnoSportProfile $profile,
        $allParticipations,
        $allConventionalResults,
        $allRatingsByUser,
        BadgeService $badgeService
    ): object {
        $userId = $profile->user_id;
        $sport  = $profile->sport;

        $userParts  = $allParticipations->get($userId, collect());
        $sportParts = $userParts->filter(fn($p) => $p->game && $p->game->field && $p->game->field->sport === $sport);

        $fuGames = $sportParts->count();
        $fuWins  = $sportParts->where('result', 'win')->count();
        $fuDraws = $sportParts->where('result', 'draw')->count();
        $fuLoss  = $sportParts->where('result', 'loss')->count();

        $userConv  = $allConventionalResults->get($userId, collect());
        $convSport = $userConv->filter(fn($r) => $r->reservation && $r->reservation->field && $r->reservation->field->sport === $sport);

        $convGames = $convSport->count();
        $convWins  = $convSport->where('match_outcome', 'W')->count();
        $convDraws = $convSport->where('match_outcome', 'D')->count();
        $convLoss  = $convSport->where('match_outcome', 'L')->count();

        $totalGames = $fuGames + $convGames;
        $totalWins  = $fuWins + $convWins;
        $totalDraws = $fuDraws + $convDraws;
        $totalLoss  = $fuLoss + $convLoss;

        // Rating global: promedio de TODAS las reseñas recibidas en cualquier deporte.
        // Mismo cálculo que usa el perfil público. Fórmula: above=5, match=3, below=1.
        $userRatings = $allRatingsByUser->get($userId, collect());
        $totalRatings = $userRatings->count();
        $aggregatedRating = 0.0;
        if ($totalRatings > 0) {
            $scoreMap = ['above' => 5, 'match' => 3, 'below' => 1];
            $sum = $userRatings->sum(fn($r) => $scoreMap[$r->assessment] ?? 3);
            $aggregatedRating = round($sum / $totalRatings, 2);
        }

        $score = 0;
        if ($totalGames > 0) {
            $score = (($totalWins * 3 + $totalDraws * 1) / $totalGames) * 100;
            $score += ((float) $profile->attendance_rate / 100) * 10;
            // El bonus de score usa el rating global, no el per-sport.
            $score += ($aggregatedRating / 5) * 10;
        }

        $winRate = $totalGames > 0 ? round(($totalWins / $totalGames) * 100) : 0;

        return (object) [
            'profile'      => $profile,
            'user'         => $profile->user,
            'sport'        => $sport,
            'category'     => $profile->category,
            'games_played' => $totalGames,
            'wins'         => $totalWins,
            'draws'        => $totalDraws,
            'losses'       => $totalLoss,
            'win_rate'     => $winRate,
            'rating'       => $aggregatedRating,     // Rating global (todos los deportes)
            'attendance'   => (float) $profile->attendance_rate,
            'score'        => round($score, 2),
            'badges'       => $badgeService->getBadges($profile),
        ];
    }

    /**
     * Build the self-card data for the current user.
     *
     * Returns an object with:
     *  - 'state' => 'no_profile' | 'insufficient_games' | 'ranked'
     *  - 'sport' => string|null (the sport being displayed, null if "all sports" aggregated)
     *  - 'stats' => stats object (only for insufficient_games / ranked)
     *  - 'position' => int|null (only for ranked)
     *  - 'total' => int (total ranked players in current filter)
     *  - 'best_sport_label' => string|null (human label when showing best profile in "all sports" mode)
     */
    private function buildSelfCard($user, ?string $sportFilter, $rankedProfiles, BadgeService $badgeService): object
    {
        // Load user's profiles (optionally filtered by sport)
        $profilesQuery = FaltaUnoSportProfile::where('user_id', $user->id);
        if ($sportFilter) {
            $profilesQuery->where('sport', $sportFilter);
        }
        $userProfiles = $profilesQuery->get();

        // No profile at all for the selected sport (or overall)
        if ($userProfiles->isEmpty()) {
            return (object) [
                'state'            => 'no_profile',
                'sport'            => $sportFilter,
                'stats'            => null,
                'position'         => null,
                'total'            => $rankedProfiles->count(),
                'best_sport_label' => null,
            ];
        }

        // Rebuild stats with the same helper so it's consistent
        $allPartsIds   = $userProfiles->pluck('user_id')->toArray();
        $allParts      = FaltaUnoParticipant::with('game.field')
            ->whereIn('user_id', $allPartsIds)
            ->where('status', 'confirmed')
            ->get()
            ->groupBy('user_id');
        $allConv       = ReservationResult::with('reservation.field')
            ->whereIn('user_id', $allPartsIds)
            ->whereNotNull('match_outcome')
            ->get()
            ->groupBy('user_id');
        $allRatings = \App\Models\FaltaUnoRating::whereIn('rated_user_id', $allPartsIds)
            ->get()
            ->groupBy('rated_user_id');

        $statsByProfile = $userProfiles->map(fn($p) => $this->buildProfileStats($p, $allParts, $allConv, $allRatings, $badgeService));

        // Pick which one to show as "main"
        $mainStats = $statsByProfile->sortByDesc('score')->first();

        // Position lookup against the public ranking
        $position = null;
        if ($mainStats && $mainStats->games_played >= 3) {
            $position = $rankedProfiles->search(fn($p) => $p->profile->id === $mainStats->profile->id);
            $position = $position === false ? null : ($position + 1);
        }

        $bestSportLabel = null;
        if (!$sportFilter && $userProfiles->count() > 1 && $mainStats) {
            $sportNames = [
                'football' => 'Fútbol',
                'padel' => 'Pádel',
                'tennis' => 'Tenis',
                'basketball' => 'Básquet',
                'volleyball' => 'Vóley',
            ];
            $bestSportLabel = $sportNames[$mainStats->sport] ?? ucfirst($mainStats->sport);
        }

        $state = $mainStats && $mainStats->games_played >= 3 ? 'ranked' : 'insufficient_games';

        return (object) [
            'state'            => $state,
            'sport'            => $sportFilter ?: ($mainStats->sport ?? null),
            'stats'            => $mainStats,
            'position'         => $position,
            'total'            => $rankedProfiles->count(),
            'best_sport_label' => $bestSportLabel,
        ];
    }
}
