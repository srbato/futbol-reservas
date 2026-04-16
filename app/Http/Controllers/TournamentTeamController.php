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

        $minPlayers = $tournament->players_per_team ?? 1;
        $data = $request->validate([
            'team_name' => 'required|string|max:80',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024',
            'players' => 'required|array|min:' . $minPlayers,
            'players.*.name' => 'required|string|max:80',
            'players.*.dni' => 'required|string|max:20',
            'players.*.jersey_number' => 'required|integer|min:0|max:99',
            'players.*.role' => 'nullable|string',
        ]);

        $exists = $tournament->teams()->where('name', $data['team_name'])->exists();
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
            'name' => $data['team_name'],
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

        // Agregar todos los jugadores (el primero es el capitan)
        if (!empty($data['players'])) {
            foreach ($data['players'] as $index => $playerData) {
                if (empty($playerData['name'])) continue;

                $isCaptain = ($index == 0 || ($playerData['role'] ?? '') === 'capitan');

                $team->players()->create([
                    'user_id' => $isCaptain ? auth()->id() : null,
                    'name' => $playerData['name'],
                    'email' => $isCaptain ? auth()->user()->email : null,
                    'dni' => $playerData['dni'] ?? null,
                    'jersey_number' => $playerData['jersey_number'] ?? null,
                    'role' => $isCaptain ? 'captain' : ($playerData['role'] ?? 'jugador'),
                ]);
            }
        }

        // Notificar al organizador si tiene plan con notificaciones
        $orgPlan = \App\Models\OrganizerPlan::where('slug', $tournament->organizer->organizerTier())->where('is_active', true)->first();
        if ($orgPlan && $orgPlan->has_notifications) {
            $tournament->organizer->notify(new \App\Notifications\TournamentTeamRegisteredNotification($tournament, $team));
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
