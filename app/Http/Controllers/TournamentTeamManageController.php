<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentTeam;
use App\Models\TournamentMatch;

class TournamentTeamManageController extends Controller
{
    public function disqualify(Tournament $tournament, TournamentTeam $team)
    {
        abort_if(!$tournament->isOrganizer(auth()->user()), 403);
        abort_if($team->tournament_id !== $tournament->id, 404);
        abort_if($team->status === TournamentTeam::STATUS_DISQUALIFIED, 422, 'El equipo ya esta descalificado.');

        $team->update(['status' => TournamentTeam::STATUS_DISQUALIFIED]);

        // Si el torneo esta en curso, resolver matches pendientes como walkover
        if ($tournament->status === Tournament::STATUS_IN_PROGRESS) {
            $pendingMatches = TournamentMatch::where('tournament_id', $tournament->id)
                ->where('status', TournamentMatch::STATUS_SCHEDULED)
                ->where(function ($q) use ($team) {
                    $q->where('home_team_id', $team->id)
                      ->orWhere('away_team_id', $team->id);
                })
                ->get();

            foreach ($pendingMatches as $match) {
                $winnerId = $match->home_team_id === $team->id ? $match->away_team_id : $match->home_team_id;

                if ($winnerId) {
                    $match->update([
                        'status' => TournamentMatch::STATUS_WALKOVER,
                        'winner_team_id' => $winnerId,
                        'notes' => 'Walkover — equipo descalificado',
                        'played_at' => now(),
                    ]);

                    app(\App\Services\TournamentFixtureService::class)->advanceWinner($match);
                }
            }
        }

        return redirect()->route('torneos.manage', $tournament)
            ->with('success', 'Equipo descalificado.');
    }
}
