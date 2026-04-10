<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentTeam;
use Illuminate\Http\Request;

class TournamentTeamController extends Controller
{
    public function create(Tournament $tournament)
    {
        abort_if(!$tournament->canRegister(), 403, 'El torneo no acepta inscripciones.');

        // Verificar que el usuario no tenga ya un equipo
        $existingTeam = $tournament->teams()->where('captain_user_id', auth()->id())->first();
        if ($existingTeam) {
            return redirect()->route('torneos.teams.show', [$tournament, $existingTeam])
                ->with('info', 'Ya tenes un equipo inscripto.');
        }

        return view('torneos.teams.create', compact('tournament'));
    }

    public function store(Request $request, Tournament $tournament)
    {
        abort_if(!$tournament->canRegister(), 403, 'El torneo no acepta inscripciones.');

        $data = $request->validate([
            'name' => 'required|string|max:80',
            'logo' => 'nullable|image|max:1024',
            'players' => 'nullable|array|max:' . ($tournament->players_per_team * 2), // permitir suplentes
            'players.*.name' => 'required|string|max:80',
            'players.*.role' => 'nullable|string|in:jugador,arquero,capitan,suplente',
        ]);

        // Verificar nombre unico dentro del torneo
        $exists = $tournament->teams()->where('name', $data['name'])->exists();
        if ($exists) {
            return back()->withErrors(['name' => 'Ya existe un equipo con ese nombre en este torneo.'])->withInput();
        }

        // Verificar que no sea capitan de otro equipo en este torneo
        $existingTeam = $tournament->teams()->where('captain_user_id', auth()->id())->first();
        if ($existingTeam) {
            return redirect()->route('torneos.teams.show', [$tournament, $existingTeam])
                ->with('info', 'Ya tenes un equipo inscripto.');
        }

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('tournament-teams', 'public');
        }

        $team = TournamentTeam::create([
            'tournament_id' => $tournament->id,
            'name' => $data['name'],
            'logo_path' => $logoPath,
            'captain_user_id' => auth()->id(),
            'status' => TournamentTeam::STATUS_CONFIRMED,
        ]);

        // Agregar capitan como primer jugador
        $team->players()->create([
            'user_id' => auth()->id(),
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
            'role' => 'captain',
        ]);

        // Agregar jugadores adicionales
        if (!empty($data['players'])) {
            foreach ($data['players'] as $playerData) {
                if (empty($playerData['name'])) continue;

                $team->players()->create([
                    'name' => $playerData['name'],
                    'role' => $playerData['role'] ?? 'jugador',
                ]);
            }
        }

        // After team creation, if tournament requires payment
        if ($tournament->inscription_price && $tournament->inscription_price > 0) {
            $organizerTier = $tournament->organizer->organizerTier();
            if ($organizerTier === 'pro') {
                return redirect()->route('torneos.teams.checkout', [$tournament, $team])
                    ->with('success', 'Equipo inscripto. Completa el pago para confirmar.');
            }
        }

        return redirect()->route('torneos.show', $tournament)
            ->with('success', 'Equipo inscripto exitosamente.');
    }

    public function show(Tournament $tournament, TournamentTeam $team)
    {
        abort_if($team->tournament_id !== $tournament->id, 404);

        $team->load(['captain', 'players']);
        $isCaptain = auth()->check() && $team->isCaptain(auth()->user());
        $isOrganizer = auth()->check() && $tournament->isOrganizer(auth()->user());

        return view('torneos.teams.show', compact('tournament', 'team', 'isCaptain', 'isOrganizer'));
    }

    public function withdraw(Tournament $tournament, TournamentTeam $team)
    {
        abort_if($team->tournament_id !== $tournament->id, 404);
        abort_if(!$team->isCaptain(auth()->user()), 403, 'Solo el capitan puede retirar el equipo.');
        abort_if($tournament->hasStarted(), 422, 'No se puede retirar un equipo de un torneo en curso.');

        $team->update(['status' => TournamentTeam::STATUS_WITHDRAWN]);

        return redirect()->route('torneos.show', $tournament)
            ->with('success', 'Equipo retirado del torneo.');
    }
}
