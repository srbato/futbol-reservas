<?php

namespace App\Http\Controllers;

use App\Models\FaltaUnoParticipant;
use App\Models\Reservation;
use App\Models\ReservationResult;
use App\Models\User;

class FaltaUnoProfilePublicController extends Controller
{
    public function show(User $user)
    {
        $profiles = $user->faltaUnoSportProfiles()->get();

        // Last 10 participated games with result
        $recentParticipations = FaltaUnoParticipant::with(['game.field.venue'])
            ->where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->whereNotNull('result')
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();

        $chartData = $recentParticipations->map(function ($p) {
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

        return view('falta-uno.sport-profile.public-show', compact('user', 'profiles', 'recentParticipations', 'chartData', 'conventionalHistory', 'conventionalStats'));
    }
}
