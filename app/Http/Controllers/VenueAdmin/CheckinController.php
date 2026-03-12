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

        $reservation = Reservation::where('verification_code', $data['code'])->first();

        if (!$reservation) {
            return back()->with('error','Código inválido');
        }

        if ($reservation->status !== 'PAID') {
            return back()->with('error','La reserva no está pagada');
        }

        $reservation->status = 'CHECKED_IN';
        $reservation->save();

        return back()->with('success','Check-in realizado correctamente');
    }
}
