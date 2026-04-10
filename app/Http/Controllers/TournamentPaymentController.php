<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentPayment;
use App\Models\TournamentTeam;
use App\Services\TournamentPaymentService;
use Illuminate\Http\Request;

class TournamentPaymentController extends Controller
{
    /**
     * Show checkout page BEFORE team creation (payment is a prerequisite)
     */
    public function checkout(Tournament $tournament)
    {
        abort_if(!$tournament->canRegister(), 403, 'El torneo no acepta inscripciones.');
        abort_if(!$tournament->inscription_price || $tournament->inscription_price <= 0, 422, 'Este torneo no tiene precio de inscripcion.');

        // Si ya tiene un equipo, redirigir al torneo
        $existingTeam = $tournament->teams()->where('captain_user_id', auth()->id())->first();
        if ($existingTeam) {
            return redirect()->route('torneos.teams.show', [$tournament, $existingTeam])
                ->with('info', 'Ya tenes un equipo inscripto.');
        }

        // Si ya pagó, redirigir al form de inscripción
        $payment = TournamentPayment::where('tournament_id', $tournament->id)
            ->where('user_id', auth()->id())
            ->where('status', 'approved')
            ->whereNull('team_id')
            ->first();

        if ($payment) {
            return redirect()->route('torneos.teams.create', $tournament)
                ->with('success', 'Pago confirmado. Completa la inscripcion de tu equipo.');
        }

        // Verificar que el organizador tenga MP conectado
        $organizerHasMp = !empty($tournament->organizer->mp_access_token);

        return view('torneos.teams.checkout', compact('tournament', 'organizerHasMp'));
    }

    /**
     * Initiate MercadoPago payment (pre-team)
     */
    public function pay(Tournament $tournament)
    {
        abort_if(!$tournament->canRegister(), 403);
        abort_if(!$tournament->inscription_price || $tournament->inscription_price <= 0, 422);

        // Crear registro de pago pendiente
        $payment = TournamentPayment::create([
            'tournament_id' => $tournament->id,
            'user_id' => auth()->id(),
            'amount' => $tournament->inscription_price,
            'status' => 'pending',
        ]);

        $service = app(TournamentPaymentService::class);
        $initPoint = $service->createCheckoutPreference($payment);

        if (!$initPoint) {
            $payment->update(['status' => 'failed']);
            return back()->with('error', 'No se pudo iniciar el pago. Intenta de nuevo.');
        }

        return redirect()->away($initPoint);
    }

    /**
     * Return from MP payment - check status and redirect accordingly
     */
    public function paymentReturn(Request $request, Tournament $tournament)
    {
        $status = $request->query('payment');

        if ($status === 'success') {
            // Verificar si el webhook ya confirmó el pago
            $payment = TournamentPayment::where('tournament_id', $tournament->id)
                ->where('user_id', auth()->id())
                ->where('status', 'approved')
                ->whereNull('team_id')
                ->first();

            if ($payment) {
                return redirect()->route('torneos.teams.create', $tournament)
                    ->with('success', 'Pago confirmado. Ahora podes anotar tu equipo.');
            }

            // El webhook puede tardar unos segundos, mostrar checkout con mensaje de espera
            return redirect()->route('torneos.teams.checkout', $tournament)
                ->with('info', 'Tu pago esta siendo procesado. En unos segundos se confirmara.');
        }

        if ($status === 'pending') {
            return redirect()->route('torneos.teams.checkout', $tournament)
                ->with('info', 'Tu pago esta pendiente de confirmacion.');
        }

        // failure
        return redirect()->route('torneos.teams.checkout', $tournament)
            ->with('error', 'El pago no se pudo completar. Intenta de nuevo.');
    }

    /**
     * Organizer confirms payment manually
     */
    public function confirmPayment(Tournament $tournament, TournamentTeam $team)
    {
        abort_if(!$tournament->isOrganizer(auth()->user()), 403);
        abort_if($team->tournament_id !== $tournament->id, 404);
        abort_if($team->payment_confirmed, 422, 'El pago ya fue confirmado.');

        $service = app(TournamentPaymentService::class);
        $service->confirmPaymentManually($team);

        return redirect()->route('torneos.manage', $tournament)
            ->with('success', "Pago del equipo {$team->name} confirmado.");
    }
}
