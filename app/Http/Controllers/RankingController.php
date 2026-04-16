<?php

namespace App\Http\Controllers;

use App\Models\FaltaUnoParticipant;
use App\Models\FaltaUnoSportProfile;
use App\Models\ReservationResult;
use App\Services\BadgeService;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function index(Request $request)
    {
        $sportFilter = $request->query('sport');

        $sportLabels = [
            'football'   => 'Futbol',
            'padel'      => 'Padel',
            'tennis'     => 'Tenis',
            'basketball' => 'Basquet',
            'volleyball' => 'Voley',
        ];

        // Get all sport profiles with their users
        $query = FaltaUnoSportProfile::with('user');

        if ($sportFilter && array_key_exists($sportFilter, $sportLabels)) {
            $query->where('sport', $sportFilter);
        }

        $profiles = $query->get();

        // Gather all unique user IDs
        $userIds = $profiles->pluck('user_id')->unique()->toArray();

        // Batch load all FaltaUno participations for these users
        $allParticipations = FaltaUnoParticipant::with('game.field')
            ->whereIn('user_id', $userIds)
            ->where('status', 'confirmed')
            ->get()
            ->groupBy('user_id');

        // Batch load all conventional results for these users
        $allConventionalResults = ReservationResult::with('reservation.field')
            ->whereIn('user_id', $userIds)
            ->whereNotNull('match_outcome')
            ->get()
            ->groupBy('user_id');

        $badgeService = new BadgeService();

        // Calculate real stats for each profile
        $rankedProfiles = $profiles->map(function ($profile) use ($allParticipations, $allConventionalResults, $badgeService) {
            $userId = $profile->user_id;
            $sport = $profile->sport;

            // FaltaUno stats
            $userParts = $allParticipations->get($userId, collect());
            $sportParts = $userParts->filter(fn($p) => $p->game && $p->game->field && $p->game->field->sport === $sport);

            $fuGames = $sportParts->count();
            $fuWins  = $sportParts->where('result', 'win')->count();
            $fuDraws = $sportParts->where('result', 'draw')->count();
            $fuLoss  = $sportParts->where('result', 'loss')->count();

            // Conventional stats
            $userConv = $allConventionalResults->get($userId, collect());
            $convSport = $userConv->filter(fn($r) => $r->reservation && $r->reservation->field && $r->reservation->field->sport === $sport);

            $convGames = $convSport->count();
            $convWins  = $convSport->where('match_outcome', 'W')->count();
            $convDraws = $convSport->where('match_outcome', 'D')->count();
            $convLoss  = $convSport->where('match_outcome', 'L')->count();

            $totalGames = $fuGames + $convGames;
            $totalWins  = $fuWins + $convWins;
            $totalDraws = $fuDraws + $convDraws;
            $totalLoss  = $fuLoss + $convLoss;

            // Calculate ranking score
            $score = 0;
            if ($totalGames > 0) {
                // Base: points per game * 100
                $score = (($totalWins * 3 + $totalDraws * 1) / $totalGames) * 100;

                // Bonus for attendance (max +10)
                $attendance = (float) $profile->attendance_rate;
                $score += ($attendance / 100) * 10;

                // Bonus for rating (max +10)
                $rating = (float) $profile->average_rating;
                $score += ($rating / 5) * 10;
            }

            $winRate = $totalGames > 0 ? round(($totalWins / $totalGames) * 100) : 0;

            $badges = $badgeService->getBadges($profile);

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
                'rating'       => (float) $profile->average_rating,
                'attendance'   => (float) $profile->attendance_rate,
                'score'        => round($score, 2),
                'badges'       => $badges,
            ];
        })
        ->filter(fn($item) => $item->games_played >= 3) // Minimum 3 games
        ->sortByDesc('score')
        ->values();

        // Manual pagination
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 20;
        $total = $rankedProfiles->count();
        $items = $rankedProfiles->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => route('ranking.index'), 'query' => $request->query()]
        );

        return view('ranking.index', [
            'players'     => $paginator,
            'sportFilter' => $sportFilter,
            'sportLabels' => $sportLabels,
            'page'        => $page,
        ]);
    }
}
