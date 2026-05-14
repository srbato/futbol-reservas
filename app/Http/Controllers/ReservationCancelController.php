<?php

namespace App\Http\Controllers;

use App\Mail\FaltaUnoCancelledMail;
use App\Mail\ReservationCancelledMail;
use App\Models\FaltaUnoGame;
use App\Models\Reservation;
use App\Notifications\FaltaUnoCancelledNotification;
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

        if (in_array($reservation->status, ['CANCELLED', 'EXPIRED'])) {
            return back()->with('error', 'Esta reserva no se puede cancelar.');
        }

        // No permitir cancelar reservas vinculadas a una suscripcion recurrente activa
        if ($reservation->recurring_subscription_id) {
            $subscription = \App\Models\RecurringSubscription::find($reservation->recurring_subscription_id);
            if ($subscription && in_array($subscription->status, ['ACTIVE', 'PENDING_PAYMENT'])) {
                return back()->with('error', 'Esta reserva pertenece a una suscripcion recurrente activa. Para cancelarla, cancela primero la suscripcion desde "Mis Reservas".');
            }
        }

        // Check venue cancellation policy
        $reservation->loadMissing('field.venue');
        $cancellationHours = $reservation->field->venue->cancellation_hours;
        if ($cancellationHours !== null && $reservation->status === 'PAID') {
            $deadline = $reservation->start_at->copy()->subHours($cancellationHours);
            if (now()->isAfter($deadline)) {
                return back()->with('error',
                    "Este complejo solo permite cancelar con al menos {$cancellationHours} hora" .
                    ($cancellationHours === 1 ? '' : 's') . " de anticipación."
                );
            }
        }

        $refundResult = $refundService->refund($reservation);

        $reservation->update([
            'status'     => 'CANCELLED',
            'expires_at' => null,
        ]);

        // Avisar al grid público (real-time) que el slot quedó libre
        broadcast(new \App\Events\FieldAvailabilityChanged(
            $reservation->field_id,
            $reservation->start_at->toDateString()
        ));

        // Si la reserva pertenece a un partido Falta Uno, cancelarlo y notificar participantes
        $faltaUnoGame = FaltaUnoGame::where('reservation_id', $reservation->id)->first();
        if ($faltaUnoGame && in_array($faltaUnoGame->status, ['open', 'full'])) {
            $faltaUnoGame->load('activeParticipants.user', 'field.venue');

            foreach ($faltaUnoGame->activeParticipants as $participant) {
                if ($participant->user && $participant->user->email) {
                    Mail::to($participant->user->email)
                        ->send(new FaltaUnoCancelledMail($faltaUnoGame, $participant->user));
                    $participant->user->notify(new FaltaUnoCancelledNotification($faltaUnoGame));
                }
            }

            $faltaUnoGame->update([
                'status'       => 'cancelled',
                'cancelled_at' => now(),
            ]);
        }

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