<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class AdminReservationsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $reservations = Reservation::query()
            ->whereHas('field.venue', function ($q) use ($user) {
                $q->where('owner_user_id', $user->id);
            })
            ->with(['user','field.venue'])
            ->orderBy('start_at')
            ->get();

        return view('admin.reservations', compact('reservations'));
    }
}
