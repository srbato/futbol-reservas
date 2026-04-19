<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class PaymentDevController extends Controller
{
    public function pay(Request $request, Reservation $reservation)
    {
        // Only available in local/development environment.
        // Triple check: isLocal, not production, and URL contains localhost/ngrok/trycloudflare
        // para blindarse ante cualquier misconfiguración de APP_ENV en producción.
        abort_if(!app()->isLocal() || app()->isProduction(), 404);

        $appUrl = (string) config('app.url');
        $isSafeUrl = str_contains($appUrl, 'localhost')
            || str_contains($appUrl, '127.0.0.1')
            || str_contains($appUrl, '.ngrok')
            || str_contains($appUrl, '.trycloudflare')
            || str_contains($appUrl, '.test');
        abort_if(!$isSafeUrl, 404);

        // Ownership check
        if ($reservation->user_id !== $request->user()->id && $request->user()->role !== 'super_admin') {
            abort(403);
        }

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