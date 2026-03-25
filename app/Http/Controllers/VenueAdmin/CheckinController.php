<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Venue;
use Illuminate\Http\Request;

class CheckinController extends Controller
{
    public function index()
    {
        return view('va.checkin');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required','string']
        ]);

        $user = $request->user();

        $reservation = Reservation::where('verification_code', $data['code'])
            ->when($user->role !== 'super_admin', function ($q) use ($user) {
                $accessibleVenueIds = Venue::accessibleBy($user)->pluck('id');
                $q->whereHas('field', function ($q2) use ($accessibleVenueIds) {
                    $q2->whereIn('venue_id', $accessibleVenueIds);
                });
            })
            ->first();

        if (!$reservation) {
            return back()->with('error', 'Código inválido o no pertenece a tus canchas.');
        }

        if ($reservation->status === 'CHECKED_IN') {
            return back()->with('error', 'Esta reserva ya fue validada.');
        }

        if ($reservation->status !== 'PAID') {
            return back()->with('error', 'La reserva no está pagada.');
        }

        $reservation->status = 'CHECKED_IN';
        $reservation->save();

        return back()->with('success', 'Check-in realizado correctamente.');
    }
}
