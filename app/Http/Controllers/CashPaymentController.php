<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class CashPaymentController extends Controller
{
    /**
     * El usuario elige pagar en efectivo en el complejo.
     * Cambia la reserva de PENDING_PAYMENT a PENDING_CASH sin expiración.
     */
    public function store(Request $request, Reservation $reservation)
    {
        $user = $request->user();

        if ($reservation->user_id !== $user->id && $user->role !== 'super_admin') {
            abort(403, 'No tenés permiso para modificar esta reserva.');
        }

        if ($reservation->status !== 'PENDING_PAYMENT') {
            abort(422, 'La reserva no está pendiente de pago.');
        }

        if ($reservation->expires_at && $reservation->expires_at->isPast()) {
            abort(422, 'La reserva ya expiró.');
        }

        $reservation->loadMissing('field.venue');

        if (!$reservation->field->venue->accepts_cash_payment) {
            abort(422, 'Este complejo no acepta pago en efectivo.');
        }

        $reservation->update([
            'status'           => 'PENDING_CASH',
            'payment_provider' => 'cash',
            'payment_status'   => 'pending',
            'expires_at'       => null, // No expira — el jugador paga al llegar
        ]);

        return redirect()
            ->route('reservations.show', $reservation)
            ->with('success', 'Reserva confirmada. Recordá pagar en efectivo al llegar al complejo.');
    }
}
