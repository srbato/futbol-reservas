<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentTeam;
use Illuminate\Http\Request;

class TournamentPaymentController extends Controller
{
    /**
     * Show checkout page for team inscription (Pro organizers only)
     */
    public function checkout(Tournament $tournament, TournamentTeam $team)
    {
        abort_if($team->tournament_id !== $tournament->id, 404);
        abort_if(!$team->isCaptain(auth()->user()), 403, 'Solo el capitan puede pagar la inscripcion.');
        abort_if($team->payment_confirmed, 422, 'La inscripcion ya esta paga.');
        abort_if(!$tournament->inscription_price || $tournament->inscription_price <= 0, 422, 'Este torneo no tiene precio de inscripcion.');

        return view('torneos.teams.checkout', compact('tournament', 'team'));
    }

    /**
     * Initiate MercadoPago payment
     */
    public function pay(Tournament $tournament, TournamentTeam $team)
    {
        abort_if($team->tournament_id !== $tournament->id, 404);
        abort_if(!$team->isCaptain(auth()->user()), 403);
        abort_if($team->payment_confirmed, 422);

        $service = app(\App\Services\TournamentPaymentService::class);
        $initPoint = $service->createCheckoutPreference($team);

        if (!$initPoint) {
            return back()->with('error', 'No se pudo iniciar el pago. Intenta de nuevo.');
        }

        return redirect()->away($initPoint);
    }

    /**
     * Organizer confirms payment manually
     */
    public function confirmPayment(Tournament $tournament, TournamentTeam $team)
    {
        abort_if(!$tournament->isOrganizer(auth()->user()), 403);
        abort_if($team->tournament_id !== $tournament->id, 404);
        abort_if($team->payment_confirmed, 422, 'El pago ya fue confirmado.');

        $service = app(\App\Services\TournamentPaymentService::class);
        $service->confirmPaymentManually($team);

        return redirect()->route('torneos.manage', $tournament)
            ->with('success', "Pago del equipo {$team->name} confirmado.");
    }
}
