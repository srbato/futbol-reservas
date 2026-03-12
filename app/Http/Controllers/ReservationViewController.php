<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationViewController extends Controller
{
    public function show(Request $request, Reservation $reservation)
    {
        $user = $request->user();

        if ($reservation->user_id !== $user->id && $user->role !== 'super_admin') {
            abort(403, 'No tenés permiso para ver esta reserva.');
        }

        $reservation->load(['field.venue', 'user']);

        return view('reservations.show', compact('reservation'));
    }
}