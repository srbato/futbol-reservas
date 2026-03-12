<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class MyReservationsController extends Controller
{
    public function index(Request $request)
    {
        $reservations = Reservation::query()
            ->where('user_id', $request->user()->id)
            ->with(['field.venue'])
            ->orderByDesc('start_at')
            ->get();

        return view('reservations.my', compact('reservations'));
    }
}
