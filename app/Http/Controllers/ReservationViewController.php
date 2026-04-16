<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationViewController extends Controller
{
    public function show(Request $request, Reservation $reservation)
    {
        $user = $request->user();

        $reservation->load(['field.venue', 'field.faltaUnoSetting', 'user', 'players.user', 'faltaUnoGame']);

        $isOwner      = $reservation->user_id === $user->id;
        $isSuperAdmin = $user->role === 'super_admin';
        $isVenueAdmin = $user->role === 'venue_admin'
            && $reservation->field->venue->owner_user_id === $user->id;
        $isTaggedPlayer = $reservation->players->contains('user_id', $user->id);
        $isVenueStaff = $user->isStaffOf($reservation->field->venue->id);

        if (!$isOwner && !$isSuperAdmin && !$isVenueAdmin && !$isTaggedPlayer && !$isVenueStaff) {
            abort(403, 'No tenés permiso para ver esta reserva.');
        }

        return view('reservations.show', compact('reservation', 'isOwner', 'isTaggedPlayer'));
    }
}
