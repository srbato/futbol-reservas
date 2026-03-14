<?php

namespace App\Http\Controllers;

use App\Mail\ReservationCancelledMail;
use App\Models\Reservation;
use App\Services\MercadoPagoRefundService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ReservationCancelController extends Controller
{
    public function cancelByUser(Request $request, Reservation $reservation, MercadoPagoRefundService $refundService)
    {
        $user = $request->user();

        if ($reservation->user_id !== $user->id && $user->role !== 'super_admin') {
            abort(403);
        }

        if (in_array($reservation->status, ['CHECKED_IN', 'CANCELLED', 'EXPIRED'])) {
            return back()->with('error', 'Esta reserva no se puede cancelar.');
        }

        $refundResult = $refundService->refund($reservation);

        $reservation->update([
            'status'     => 'CANCELLED',
            'expires_at' => null,
        ]);

        $reservation->loadMissing(['user', 'field.venue.owner']);

        Mail::to($reservation->user->email)
            ->send(new ReservationCancelledMail($reservation, 'user'));

        $venueOwner = $reservation->field->venue->owner;
        if ($venueOwner && $venueOwner->email) {
            Mail::to($venueOwner->email)
                ->send(new ReservationCancelledMail($reservation, 'admin'));
        }

        $message = match ($refundResult) {
            true  => 'Reserva cancelada. El reembolso fue procesado correctamente.',
            false => 'Reserva cancelada. No pudimos procesar el reembolso automáticamente — contactá soporte.',
            null  => 'Reserva cancelada correctamente.',
        };

        return back()->with('success', $message);
    }
}