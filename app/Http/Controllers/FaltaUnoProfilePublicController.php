<?php

namespace App\Http\Controllers;

use App\Models\FaltaUnoParticipant;
use App\Models\FaltaUnoRating;
use App\Models\Reservation;
use App\Models\ReservationResult;
use App\Models\User;

class FaltaUnoProfilePublicController extends Controller
{
    public function show(User $user)
    {
        $profiles = $user->faltaUnoSportProfiles()->get();

        // Last 10 participated games (con resultado o no-show)
        $recentParticipations = FaltaUnoParticipant::with(['game.field.venue'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['confirmed', 'no_show'])
            ->where(function ($q) {
                $q->whereNotNull('result')->orWhere('status', 'no_show');
            })
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();

        $chartData = $recentParticipations->filter(fn($p) => $p->status !== 'no_show')->map(function ($p) {
            $resultScore = match($p->result) {
                'win'  => 3,
                'draw' => 1,
                'loss' => 0,
                default => null,
            };
            return [
                'date'   => \Carbon\Carbon::parse($p->game->start_at)->format('d/m'),
                'result' => $resultScore,
            ];
        })->reverse()->values();

        // Historial de reservas convencionales (últimas 10 con resultado cargado)
        $conventionalResults = ReservationResult::with(['reservation.field.venue'])
            ->where('user_id', $user->id)
            ->whereNotNull('match_outcome')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $conventionalHistory = $conventionalResults->map(function ($r) {
            return (object) [
                'reservation' => $r->reservation,
                'field'       => $r->reservation->field ?? null,
                'venue'       => $r->reservation->field->venue ?? null,
                'outcome'     => $r->match_outcome,
                'score'       => $r->match_result,
                'date'        => $r->reservation->start_at,
            ];
        });

        $conventionalStats = [
            'total'  => $conventionalResults->count(),
            'wins'   => $conventionalResults->where('match_outcome', 'W')->count(),
            'draws'  => $conventionalResults->where('match_outcome', 'D')->count(),
            'losses' => $conventionalResults->where('match_outcome', 'L')->count(),
        ];

        // Stats calculados desde participaciones reales (fuente única de verdad)
        $allParticipations = FaltaUnoParticipant::with('game.field')
            ->where('user_id', $user->id)
            ->whereIn('status', ['confirmed', 'no_show'])
            ->get();

        $realStats = [];
        foreach ($profiles as $profile) {
            $sportParts = $allParticipations->filter(fn($p) => $p->game->field->sport === $profile->sport);
            $confirmed = $sportParts->where('status', 'confirmed');

            $realStats[$profile->sport] = [
                'games_played' => $confirmed->count(),
                'wins'         => $confirmed->where('result', 'win')->count(),
                'draws'        => $confirmed->where('result', 'draw')->count(),
                'losses'       => $confirmed->where('result', 'loss')->count(),
                'no_shows'     => $sportParts->where('status', 'no_show')->count(),
            ];
        }

        // Datos de calificaciones recibidas por deporte
        $ratingsData = [];
        foreach ($profiles as $profile) {
            $ratings = FaltaUnoRating::where('rated_user_id', $user->id)
                ->whereHas('game', fn($q) => $q->whereHas('field', fn($q2) => $q2->where('sport', $profile->sport)))
                ->get();

            $totalRatings = $ratings->count();
            $aboveCount = $ratings->where('assessment', 'above')->count();
            $matchCount = $ratings->where('assessment', 'match')->count();
            $belowCount = $ratings->where('assessment', 'below')->count();

            // Últimos 5 comentarios
            $recentComments = FaltaUnoRating::where('rated_user_id', $user->id)
                ->whereHas('game', fn($q) => $q->whereHas('field', fn($q2) => $q2->where('sport', $profile->sport)))
                ->whereNotNull('comment')
                ->where('comment', '!=', '')
                ->with('rater:id,name,avatar_path')
                ->latest()
                ->limit(5)
                ->get();

            $ratingsData[$profile->sport] = [
                'total'    => $totalRatings,
                'above'    => $aboveCount,
                'match'    => $matchCount,
                'below'    => $belowCount,
                'comments' => $recentComments,
            ];
        }

        return view('falta-uno.sport-profile.public-show', compact('user', 'profiles', 'recentParticipations', 'chartData', 'conventionalHistory', 'conventionalStats', 'ratingsData', 'realStats'));
    }
}
