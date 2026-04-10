<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function index(Request $request)
    {
        $query = Tournament::whereIn('status', [
            Tournament::STATUS_OPEN_REGISTRATION,
            Tournament::STATUS_IN_PROGRESS,
            Tournament::STATUS_FINISHED,
        ])->with(['organizer', 'confirmedTeams']);

        if ($request->filled('sport')) {
            $query->where('sport', $request->sport);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $query->where('name', 'ilike', '%' . $request->q . '%');
        }

        $tournaments = $query->latest()->paginate(12);

        $myTournamentsCount = 0;
        if (auth()->check()) {
            $myTournamentsCount = Tournament::where('organizer_user_id', auth()->id())->count();
        }

        return view('torneos.index', [
            'tournaments' => $tournaments,
            'sport' => $request->input('sport'),
            'status' => $request->input('status'),
            'q' => $request->input('q'),
            'myTournamentsCount' => $myTournamentsCount,
        ]);
    }

    public function myTournaments()
    {
        $tournaments = Tournament::where('organizer_user_id', auth()->id())
            ->with(['confirmedTeams'])
            ->latest()
            ->get();

        return view('torneos.my-tournaments', compact('tournaments'));
    }

    public function show(Tournament $tournament)
    {
        // Permitir ver draft solo al organizador
        if ($tournament->status === Tournament::STATUS_DRAFT) {
            abort_if(!auth()->check() || !$tournament->isOrganizer(auth()->user()), 404);
        }

        // No mostrar cancelados al publico
        if ($tournament->status === Tournament::STATUS_CANCELLED) {
            abort_if(!auth()->check() || !$tournament->isOrganizer(auth()->user()), 404);
        }

        $tournament->load([
            'organizer',
            'confirmedTeams.captain',
            'confirmedTeams.players',
            'matches.homeTeam',
            'matches.awayTeam',
            'matches.winner',
        ]);

        $isOrganizer = auth()->check() && $tournament->isOrganizer(auth()->user());
        $canRegister = $tournament->canRegister();

        // Verificar si el usuario ya tiene equipo en este torneo
        $userTeam = null;
        if (auth()->check()) {
            $userTeam = $tournament->teams()
                ->where('captain_user_id', auth()->id())
                ->first();
        }

        // Organizar matches por rondas para el bracket
        $rounds = $tournament->matches->groupBy('round')->sortKeys();
        $totalRounds = $rounds->count();

        return view('torneos.show', compact(
            'tournament', 'isOrganizer', 'canRegister', 'userTeam', 'rounds', 'totalRounds'
        ));
    }
}
