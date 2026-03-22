<?php

namespace App\Http\Controllers;

use App\Models\FaltaUnoParticipant;
use App\Models\Reservation;
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

        // Historial de reservas convencionales (últimas 10 pagadas)
        $conventionalHistory = Reservation::query()
            ->where('user_id', $user->id)
            ->where('status', 'PAID')
            ->whereNotNull('result')
            ->with(['field.venue'])
            ->orderByDesc('start_at')
            ->limit(10)
            ->get();

        $conventionalStats = [
            'total' => $conventionalHistory->count(),
            'wins'  => $conventionalHistory->where('result', 'win')->count(),
            'draws' => $conventionalHistory->where('result', 'draw')->count(),
            'losses'=> $conventionalHistory->where('result', 'loss')->count(),
        ];

        return view('falta-uno.sport-profile.public-show', compact('user', 'profiles', 'recentParticipations', 'chartData', 'conventionalHistory', 'conventionalStats'));
    }
}
