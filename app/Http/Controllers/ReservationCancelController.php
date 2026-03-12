<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationCancelController extends Controller
{
    public function cancelByUser(Request $request, Reservation $reservation)
    {
        $user = $request->user();

        if ($reservation->user_id !== $user->id && $user->role !== 'super_admin') {
            abort(403);
        }

        if (in_array($reservation->status, ['CHECKED_IN', 'CANCELLED', 'EXPIRED'])) {
            return back()->with('error', 'Esta reserva no se puede cancelar.');
        }

        $reservation->update([
            'status' => 'CANCELLED',
            'expires_at' => null,
        ]);

        return back()->with('success', 'Reserva cancelada correctamente.');
    }
}