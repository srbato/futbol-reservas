<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class PaymentDevController extends Controller
{
    public function pay(Request $request, Reservation $reservation)
    {
        // si ya expiró o ya está pagada, no hacemos nada raro
        if ($reservation->status !== 'PENDING_PAYMENT') {
            return redirect()->route('reservations.checkout', $reservation);
        }

        if ($reservation->expires_at && $reservation->expires_at->isPast()) {
            $reservation->update(['status' => 'EXPIRED']);
            return redirect()->route('reservations.checkout', $reservation);
        }

        $reservation->update([
            'status' => 'PAID',
        ]);

        return redirect()->route('reservations.show', $reservation);
    }
}