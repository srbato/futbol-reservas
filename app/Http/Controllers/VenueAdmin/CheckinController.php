<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
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
                $q->whereHas('field.venue', function ($q2) use ($user) {
                    $q2->where('owner_user_id', $user->id);
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
