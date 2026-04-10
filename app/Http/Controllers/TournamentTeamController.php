<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentPayment;
use App\Models\TournamentTeam;
use Illuminate\Http\Request;

class TournamentTeamController extends Controller
{
    public function create(Tournament $tournament)
    {
        abort_if(!$tournament->canRegister(), 403, 'El torneo no acepta inscripciones.');

        $existingTeam = $tournament->teams()->where('captain_user_id', auth()->id())->first();
        if ($existingTeam) {
            return redirect()->route('torneos.teams.show', [$tournament, $existingTeam])
                ->with('info', 'Ya tenes un equipo inscripto.');
        }

        // Si el torneo tiene precio, verificar que el usuario haya pagado
        if ($tournament->inscription_price && $tournament->inscription_price > 0) {
            $payment = TournamentPayment::where('tournament_id', $tournament->id)
                ->where('user_id', auth()->id())
                ->where('status', 'approved')
                ->whereNull('team_id')
                ->first();

            if (!$payment) {
                return redirect()->route('torneos.teams.checkout', $tournament)
                    ->with('info', 'Primero debes abonar la inscripcion para anotar tu equipo.');
            }
        }

        return view('torneos.teams.create', compact('tournament'));
    }

    public function store(Request $request, Tournament $tournament)
    {
        abort_if(!$tournament->canRegister(), 403, 'El torneo no acepta inscripciones.');

        $data = $request->validate([
            'name' => 'required|string|max:80',
            'logo' => 'nullable|image|max:1024',
            'players' => 'nullable|array|max:' . ($tournament->players_per_team * 2),
            'players.*.name' => 'required|string|max:80',
            'players.*.role' => 'nullable|string|in:jugador,arquero,capitan,suplente',
        ]);

        $exists = $tournament->teams()->where('name', $data['name'])->exists();
        if ($exists) {
            return back()->withErrors(['name' => 'Ya existe un equipo con ese nombre en este torneo.'])->withInput();
        }

        $existingTeam = $tournament->teams()->where('captain_user_id', auth()->id())->first();
        if ($existingTeam) {
            return redirect()->route('torneos.teams.show', [$tournament, $existingTeam])
                ->with('info', 'Ya tenes un equipo inscripto.');
        }

        // Verificar pago si el torneo tiene precio
        $payment = null;
        if ($tournament->inscription_price && $tournament->inscription_price > 0) {
            $payment = TournamentPayment::where('tournament_id', $tournament->id)
                ->where('user_id', auth()->id())
                ->where('status', 'approved')
                ->whereNull('team_id')
                ->first();

            if (!$payment) {
                return redirect()->route('torneos.teams.checkout', $tournament)
                    ->with('error', 'Debes abonar la inscripcion antes de anotar tu equipo.');
            }
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
            'payment_confirmed' => $payment ? true : ($tournament->inscription_price <= 0),
            'payment_confirmed_at' => $payment ? now() : null,
            'payment_method' => $payment ? 'mercadopago' : null,
            'payment_external_id' => $payment ? $payment->mp_payment_id : null,
        ]);

        // Vincular el pago al equipo
        if ($payment) {
            $payment->update(['team_id' => $team->id]);
        }

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
