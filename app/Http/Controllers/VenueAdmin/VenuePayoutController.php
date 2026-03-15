<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\PlatformPayout;
use App\Models\Venue;
use Illuminate\Http\Request;

class VenuePayoutController extends Controller
{
    public function index(Request $request)
    {
        $user  = $request->user();
        $venue = Venue::where('owner_user_id', $user->id)->first();

        if (!$venue && $user->role === 'super_admin') {
            return redirect()->route('sa.payouts.index');
        }

        if (!$venue) {
            return redirect()->route('va.dashboard')
                ->with('error', 'No tenés un complejo asociado a tu cuenta.');
        }

        $pending = PlatformPayout::with(['reservation.field'])
            ->where('venue_id', $venue->id)
            ->where('status', 'pending')
            ->latest()
            ->get();

        $paid = PlatformPayout::with(['reservation.field'])
            ->where('venue_id', $venue->id)
            ->where('status', 'paid')
            ->latest('paid_at')
            ->get();

        $totalPending = $pending->sum('amount');
        $totalPaid    = $paid->sum('amount');

        return view('va.payouts.index', compact('venue', 'pending', 'paid', 'totalPending', 'totalPaid'));
    }
}
