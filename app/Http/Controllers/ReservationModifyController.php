<?php

namespace App\Http\Controllers;

use App\Mail\ReservationModifiedMail;
use App\Models\Field;
use App\Models\Reservation;
use App\Services\MercadoPagoPartialRefundService;
use App\Services\ReservationModifyPaymentService;
use App\Services\ReservationModifyService;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReservationModifyController extends Controller
{
    /**
     * Paso 1 — Selector de nuevo horario.
     */
    public function showSlotPicker(Request $request, Reservation $reservation)
    {
        $this->authorizeModify($request, $reservation);

        $reservation->loadMissing('field.venue');
        $venue  = $reservation->field->venue;
        $fields = Field::with(['schedules', 'price', 'blocks'])
            ->where('venue_id', $venue->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedFieldId = (int) $request->query('field_id', $reservation->field_id);
        $selectedField   = $fields->firstWhere('id', $selectedFieldId) ?? $fields->first();

        $selectedDate = $request->query('date')
            ? Carbon::parse($request->query('date'))
            : $reservation->start_at->copy();

        // No permitir fechas pasadas
        if ($selectedDate->isPast() && !$selectedDate->isToday()) {
            $selectedDate = now();
        }

        $slots = $this->buildSlots($selectedField, $selectedDate, $reservation);

        return view('reservations.modify.slot-picker', compact(
            'reservation', 'venue', 'fields', 'selectedField', 'selectedDate', 'slots'
        ));
    }

    /**
     * Paso 2 — Preview del cambio (calcula delta de precio).
     */
    public function previewChange(Request $request, Reservation $reservation)
    {
        $this->authorizeModify($request, $reservation);

        $request->validate([
            'field_id' => 'required|integer|exists:fields,id',
            'start_at' => 'required|date',
        ]);

        $newField = Field::with(['venue', 'price', 'discounts'])
            ->findOrFail($request->field_id);

        $reservation->loadMissing('field.venue');

        // Verificar que la cancha pertenece al mismo complejo
        if ($newField->venue_id !== $reservation->field->venue_id) {
            return back()->with('error', 'La cancha no pertenece al mismo complejo.');
        }

        $newStart = Carbon::parse($request->start_at);

        if ($newStart->isPast()) {
            return back()->with('error', 'No podés elegir un horario pasado.');
        }

        $newEnd = $newStart->copy()->addMinutes((int) ($newField->slot_minutes ?: 60));

        // Verificar disponibilidad (sin bloquear aún)
        $overlap = Reservation::query()
            ->where('field_id', $newField->id)
            ->where('id', '!=', $reservation->id)
            ->where(function ($q) {
                $q->where('status', 'PAID')
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'PENDING_PAYMENT')
                         ->whereNotNull('expires_at')
                         ->where('expires_at', '>', now());
                  });
            })
            ->where(function ($q) use ($newStart, $newEnd) {
                $q->where('start_at', '<', $newEnd)->where('end_at', '>', $newStart);
            })
            ->exists();

        if ($overlap) {
            return back()->with('error', 'Ese horario ya está ocupado. Elegí otro.');
        }

        // Verificar bloques de cancha
        $blocked = $newField->blocks()
            ->whereDate('date', $newStart->toDateString())
            ->where(function ($q) use ($newStart, $newEnd) {
                $q->whereRaw("(date || ' ' || start_time) < ?", [$newEnd->toDateTimeString()])
                  ->whereRaw("(date || ' ' || end_time) > ?", [$newStart->toDateTimeString()]);
            })
            ->exists();

        if ($blocked) {
            return back()->with('error', 'Ese horario está bloqueado por el complejo. Elegí otro.');
        }

        /** @var ReservationService $reservationService */
        $reservationService = app(ReservationService::class);
        $newPrice           = $reservationService->calculatePrice($newField, $newStart);
        $diff               = $newPrice - (float) $reservation->total_amount;

        // Guardar selección en sesión (válida 10 minutos)
        session([
            'reservation_modify' => [
                'reservation_id' => $reservation->id,
                'field_id'       => $newField->id,
                'start_at'       => $newStart->toDateTimeString(),
                'new_price'      => $newPrice,
                'expires_at'     => now()->addMinutes(10)->toDateTimeString(),
            ],
        ]);

        return view('reservations.modify.preview', compact(
            'reservation', 'newField', 'newStart', 'newPrice', 'diff'
        ));
    }

    /**
     * Paso 3 — Confirmar el cambio (Fase 1: igual precio o más barato).
     */
    public function confirm(Request $request, Reservation $reservation)
    {
        $this->authorizeModify($request, $reservation);

        $sessionData = session('reservation_modify');

        if (!$sessionData || $sessionData['reservation_id'] !== $reservation->id) {
            return redirect()->route('reservations.modify.show', $reservation)
                ->with('error', 'La sesión expiró. Por favor volvé a elegir el horario.');
        }

        if (now()->isAfter(Carbon::parse($sessionData['expires_at']))) {
            session()->forget('reservation_modify');
            return redirect()->route('reservations.modify.show', $reservation)
                ->with('error', 'La selección del horario expiró (10 minutos). Por favor volvé a elegir.');
        }

        $newField = Field::with(['venue', 'price', 'discounts', 'blocks'])->findOrFail($sessionData['field_id']);
        $newStart = Carbon::parse($sessionData['start_at']);
        $newPrice = (float) $sessionData['new_price'];
        $diff     = $newPrice - (float) $reservation->total_amount;

        // Fase 2: nuevo horario más caro → cobrar la diferencia via MercadoPago
        if ($diff > 0) {
            session()->forget('reservation_modify');

            try {
                $initPoint = app(ReservationModifyPaymentService::class)
                    ->createPreference($reservation, $newField, $newStart, $diff);
            } catch (\RuntimeException $e) {
                return redirect()->route('reservations.modify.show', $reservation)
                    ->with('error', 'No se pudo iniciar el pago con MercadoPago. Intentá nuevamente.');
            }

            return redirect()->away($initPoint);
        }

        // Aplicar el cambio de forma atómica
        /** @var ReservationModifyService $modifyService */
        $modifyService = app(ReservationModifyService::class);
        $modifyService->applyChange($reservation, $newField, $newStart, $newPrice);

        // Reembolso parcial si el nuevo slot es más barato
        $refundResult = null;
        if ($diff < 0) {
            /** @var MercadoPagoPartialRefundService $partialRefund */
            $partialRefund = app(MercadoPagoPartialRefundService::class);
            $refundResult  = $partialRefund->refundPartial($reservation->fresh(), abs($diff));
        }

        // Email de confirmación
        $fresh = $reservation->fresh()->loadMissing(['user', 'field.venue']);
        Mail::to($fresh->user->email)->queue(new ReservationModifiedMail($fresh, abs($diff), $refundResult));

        session()->forget('reservation_modify');

        $message = 'Tu reserva fue modificada correctamente.';
        if ($diff < 0) {
            $message .= $refundResult === true
                ? ' El reembolso de $' . number_format(abs($diff), 0, ',', '.') . ' fue procesado.'
                : ' No pudimos procesar el reembolso automáticamente — contactá soporte.';
        }

        return redirect()->route('reservations.show', $reservation)->with('success', $message);
    }

    // ─── Retornos de pago MercadoPago (Fase 2) ──────────────────────────────

    /**
     * MercadoPago redirige aquí tras un pago aprobado.
     * Verifica el pago, aplica el cambio y limpia los datos pendientes.
     */
    public function modifySuccess(Request $request, Reservation $reservation)
    {
        abort_if($reservation->user_id !== auth()->id(), 403);

        $paymentId = $request->query('payment_id') ?: $request->query('collection_id');

        if (!$paymentId) {
            return redirect()->route('reservations.modify.payment.pending', $reservation);
        }

        // Caso: el webhook ya procesó el cambio exitosamente
        if ($reservation->modification_status === 'COMPLETED') {
            return redirect()->route('reservations.show', $reservation)
                ->with('success', 'Tu reserva ya fue modificada correctamente.');
        }

        // Caso: el webhook procesó el pago pero el slot ya estaba tomado → reembolsó y limpió
        if (!$reservation->modify_mp_preference_id) {
            return redirect()->route('reservations.show', $reservation)
                ->with('error', 'El horario fue reservado por otra persona mientras procesabas el pago. La diferencia será reembolsada automáticamente.');
        }

        // Resolver el access_token correcto para verificar el pago
        $reservation->loadMissing('field.venue');

        $newField = Field::with(['venue', 'price', 'discounts', 'blocks'])
            ->find($reservation->modify_field_id);

        if (!$newField) {
            return redirect()->route('reservations.show', $reservation)
                ->with('error', 'No se encontraron los datos del cambio pendiente.');
        }

        $newField->loadMissing('venue');
        $accessToken = $newField->venue->mp_access_token
            ?? config('services.mercadopago.access_token');

        // Verificar estado del pago en la API de MP
        $paymentResponse = Http::withOptions(['verify' => app()->isProduction()])
            ->withToken($accessToken)
            ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

        if (!$paymentResponse->successful()) {
            Log::error('modifySuccess: no se pudo consultar el pago', [
                'reservation_id' => $reservation->id,
                'http_status'    => $paymentResponse->status(),
            ]);
            return redirect()->route('reservations.modify.payment.pending', $reservation);
        }

        $paymentData   = $paymentResponse->json();
        $paymentStatus = $paymentData['status'] ?? null;

        if ($paymentStatus !== 'approved') {
            return redirect()->route('reservations.modify.payment.pending', $reservation);
        }

        $newStart = Carbon::parse($reservation->modify_start_at);
        $newPrice = (float) $reservation->total_amount + (float) $reservation->modify_diff_amount;

        /** @var ReservationModifyService $modifyService */
        $modifyService = app(ReservationModifyService::class);

        try {
            $modifyService->applyChange($reservation, $newField, $newStart, $newPrice);
        } catch (\Throwable $e) {
            // Slot tomado mientras se procesaba el pago: reembolsar
            Log::error('modifySuccess: slot tomado al aplicar cambio, reembolsando', [
                'reservation_id' => $reservation->id,
                'error'          => $e->getMessage(),
            ]);

            // Reembolsar usando directamente la API (no podemos usar refundPartial porque
            // el payment_id del diferencial no es el payment_external_id de la reserva)
            Http::withOptions(['verify' => app()->isProduction()])
                ->withToken($accessToken)
                ->post("https://api.mercadopago.com/v1/payments/{$paymentId}/refunds", [
                    'amount' => round((float) $reservation->modify_diff_amount, 2),
                ]);

            $reservation->update([
                'modify_mp_preference_id' => null,
                'modify_diff_amount'      => null,
                'modify_field_id'         => null,
                'modify_start_at'         => null,
            ]);

            return redirect()->route('reservations.show', $reservation)
                ->with('error', 'El horario fue reservado por otra persona mientras procesabas el pago. Se reembolsará la diferencia automáticamente.');
        }

        // Limpiar datos de modificación pendiente
        $reservation->update([
            'modify_mp_preference_id' => null,
            'modify_diff_amount'      => null,
            'modify_field_id'         => null,
            'modify_start_at'         => null,
        ]);

        // El email de confirmación lo envía el webhook (handleModifyPayment) para evitar duplicados.

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Tu reserva fue modificada correctamente. El pago de la diferencia fue procesado.');
    }

    /**
     * MercadoPago redirige aquí cuando el pago está pendiente de acreditación.
     * Limpia los datos de modificación pendiente para evitar datos huérfanos.
     */
    public function modifyPending(Reservation $reservation)
    {
        abort_if($reservation->user_id !== auth()->id(), 403);

        $reservation->update([
            'modify_mp_preference_id' => null,
            'modify_diff_amount'      => null,
            'modify_field_id'         => null,
            'modify_start_at'         => null,
        ]);

        $reservation->loadMissing(['field.venue']);

        return view('reservations.modify.payment-pending', compact('reservation'));
    }

    /**
     * MercadoPago redirige aquí cuando el pago falló o fue rechazado.
     * Limpia los datos de modificación para que el usuario pueda reintentar.
     */
    public function modifyFailure(Reservation $reservation)
    {
        abort_if($reservation->user_id !== auth()->id(), 403);

        $reservation->update([
            'modify_mp_preference_id' => null,
            'modify_diff_amount'      => null,
            'modify_field_id'         => null,
            'modify_start_at'         => null,
        ]);

        $reservation->loadMissing(['field.venue']);

        return view('reservations.modify.payment-failure', compact('reservation'));
    }

    // ─── Helpers ────────────────────────────────────────────────────────────

    private function authorizeModify(Request $request, Reservation $reservation): void
    {
        $user = $request->user();

        if ($reservation->user_id !== $user->id) {
            abort(403);
        }

        if ($reservation->status !== 'PAID') {
            abort(422, 'Solo podés modificar reservas pagadas.');
        }

        if ($reservation->batch_id !== null) {
            abort(422, 'Las reservas recurrentes no se pueden modificar individualmente.');
        }

        if ($reservation->recurring_subscription_id !== null) {
            abort(422, 'Las reservas de suscripciones recurrentes no se pueden modificar.');
        }

        if (\App\Models\FaltaUnoGame::where('reservation_id', $reservation->id)->exists()) {
            abort(422, 'Las reservas vinculadas a un partido Falta Uno no se pueden modificar.');
        }

        if ($reservation->start_at->isPast()) {
            abort(422, 'No podés modificar una reserva pasada.');
        }

        $reservation->loadMissing('field.venue');
        $modificationHours = $reservation->field->venue->modification_hours;

        if ($modificationHours === null) {
            abort(422, 'Este complejo no permite modificar reservas.');
        }

        $deadline = $reservation->start_at->copy()->subHours($modificationHours);
        if (now()->isAfter($deadline)) {
            $h = $modificationHours;
            abort(422, "Este complejo solo permite modificar con al menos {$h} hora" . ($h === 1 ? '' : 's') . " de anticipación.");
        }
    }

    private function buildSlots(Field $field, Carbon $date, Reservation $excludeReservation): array
    {
        /** @var ReservationService $reservationService */
        $reservationService = app(ReservationService::class);

        $dow      = (int) $date->dayOfWeek;
        $schedule = $field->schedules->firstWhere('day_of_week', $dow);

        if (!$schedule) {
            return [];
        }

        $slotMinutes = (int) ($field->slot_minutes ?: 60);
        $dayStart    = Carbon::parse($date->toDateString() . ' ' . $schedule->open_time);
        $dayEnd      = Carbon::parse($date->toDateString() . ' ' . $schedule->close_time);

        // Cargar reservas del día en batch (evita N+1)
        $existingReservations = Reservation::query()
            ->where('field_id', $field->id)
            ->whereIn('status', ['PENDING_PAYMENT', 'PAID'])
            ->where('id', '!=', $excludeReservation->id)
            ->where('start_at', '>=', $dayStart)
            ->where('start_at', '<', $dayEnd)
            ->get(['start_at', 'end_at', 'status', 'expires_at']);

        $blocks = $field->blocks()->whereDate('date', $date->toDateString())->get(['start_time', 'end_time']);

        $slots   = [];
        $current = $dayStart->copy();

        while ($current < $dayEnd) {
            $slotEnd = $current->copy()->addMinutes($slotMinutes);
            if ($slotEnd->gt($dayEnd)) {
                break;
            }

            if ($current->isPast()) {
                $current->addMinutes($slotMinutes);
                continue;
            }

            $occupied = $existingReservations->first(function ($r) use ($current, $slotEnd) {
                if ($r->status === 'PENDING_PAYMENT' && $r->expires_at?->isPast()) {
                    return false;
                }
                return $r->start_at < $slotEnd && $r->end_at > $current;
            });

            $blocked = $blocks->first(function ($b) use ($date, $current, $slotEnd) {
                $bs = Carbon::parse($date->toDateString() . ' ' . $b->start_time);
                $be = Carbon::parse($date->toDateString() . ' ' . $b->end_time);
                return $bs < $slotEnd && $be > $current;
            });

            $isCurrent = $field->id === $excludeReservation->field_id
                && $current->toDateTimeString() === $excludeReservation->start_at->format('Y-m-d H:i:s');

            $slots[] = [
                'time'       => $current->format('H:i'),
                'start_at'   => $current->toDateTimeString(),
                'available'  => !$occupied && !$blocked,
                'is_current' => $isCurrent,
                'price'      => (!$occupied && !$blocked && !$isCurrent)
                    ? $reservationService->calculatePrice($field, $current->copy())
                    : null,
            ];

            $current->addMinutes($slotMinutes);
        }

        return $slots;
    }
}
