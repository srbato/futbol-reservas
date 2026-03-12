<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function show(Request $request, Reservation $reservation)
    {
        $user = $request->user();

        if ($reservation->user_id !== $user->id && $user->role !== 'super_admin') {
            abort(403, 'No tenés permiso para ver este checkout.');
        }

        $reservation->load(['field.venue']);

        if ($reservation->status === 'PAID') {
            return redirect()
                ->route('reservations.success', $reservation)
                ->with('success', 'Esta reserva ya fue pagada.');
        }

        if ($reservation->expires_at && $reservation->expires_at->isPast()) {
            return redirect()
                ->route('reservations.failure', $reservation)
                ->with('error', 'La reserva venció y ya no puede pagarse.');
        }

        return view('checkout.show', compact('reservation'));
    }
}
