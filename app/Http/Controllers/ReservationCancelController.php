<?php

namespace App\Http\Controllers;

use App\Mail\ReservationCancelledMail;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
            'status'     => 'CANCELLED',
            'expires_at' => null,
        ]);

        $reservation->loadMissing(['user', 'field.venue.owner']);

        // Mail al usuario
        Mail::to($reservation->user->email)
            ->send(new ReservationCancelledMail($reservation, 'user'));

        // Mail al dueño del complejo
        $venueOwner = $reservation->field->venue->owner;
        if ($venueOwner && $venueOwner->email) {
            Mail::to($venueOwner->email)
                ->send(new ReservationCancelledMail($reservation, 'admin'));
        }

        return back()->with('success', 'Reserva cancelada correctamente.');
    }
}