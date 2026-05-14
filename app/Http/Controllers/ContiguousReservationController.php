<?php

namespace App\Http\Controllers;

use App\Exceptions\VenueBlockedException;
use App\Models\Field;
use App\Models\ReservationBatch;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Crea N reservas consecutivas en la misma cancha (drag-to-select del grid ATC).
 *
 * - 1 slot  → crea 1 reserva, devuelve checkout_url de esa reserva
 * - N slots → crea un batch con N reservas consecutivas, devuelve checkout_url del batch
 *
 * Reusa ReservationService::createSingle (misma lógica de validación/locking que
 * el flujo actual). Si alguna de las reservas falla, hace rollback completo
 * (cancela las que sí se crearon) para no dejar huecos.
 */
class ContiguousReservationController extends Controller
{
    public function store(Request $request, ReservationService $service)
    {
        $data = $request->validate([
            'field_id' => ['required', 'integer', 'exists:fields,id'],
            'start_at' => ['required', 'date'],
            'slots'    => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        $field = Field::query()
            ->with(['venue', 'price', 'schedules', 'exceptions', 'discounts'])
            ->findOrFail($data['field_id']);

        $userId      = $request->user()->id;
        $start       = Carbon::parse($data['start_at'])->seconds(0);
        $slotCount   = (int) $data['slots'];
        $slotMinutes = (int) ($field->slot_minutes ?: 60);
        $currency    = $field->price?->currency ?? 'ARS';

        // ── Caso simple: 1 slot → reserva normal, mismo flujo que ReservationController
        if ($slotCount === 1) {
            try {
                $reservation = $service->createSingle($field, $start, $userId);
            } catch (VenueBlockedException $e) {
                return response()->json(['error' => $e->getMessage()], 403);
            }

            return response()->json([
                'type'         => 'single',
                'reservation'  => $reservation,
                'checkout_url' => route('reservations.checkout', $reservation),
            ], 201);
        }

        // ── Caso multi-slot: batch con N reservas consecutivas
        // Creamos el batch primero
        $batch = ReservationBatch::create([
            'user_id'             => $userId,
            'field_id'            => $field->id,
            'subtotal'            => 0,
            'discount_percentage' => 0,
            'discount_amount'     => 0,
            'total_amount'        => 0,
            'currency'            => $currency,
            'status'              => 'PENDING_PAYMENT',
            'expires_at'          => now()->addMinutes(15),
        ]);

        $createdReservations = [];
        $subtotal = 0;
        $failureReason = null;

        for ($i = 0; $i < $slotCount; $i++) {
            $slotStart = $start->copy()->addMinutes($i * $slotMinutes);
            try {
                $reservation = $service->createSingle(
                    $field,
                    $slotStart,
                    $userId,
                    expiresInMinutes: 15,
                    batchId: $batch->id
                );
                $createdReservations[] = $reservation;
                $subtotal += (float) $reservation->total_amount;
            } catch (VenueBlockedException $e) {
                $failureReason = $e->getMessage();
                break;
            } catch (\Throwable $e) {
                $failureReason = $e->getMessage();
                break;
            }
        }

        // Si falló alguna, rollback: borramos las que se crearon y el batch
        if ($failureReason !== null) {
            foreach ($createdReservations as $r) {
                $r->delete();
            }
            $batch->delete();
            return response()->json([
                'error' => 'No se pudieron reservar todos los turnos: ' . $failureReason,
            ], 409);
        }

        $batch->update([
            'subtotal'     => $subtotal,
            'total_amount' => $subtotal,
        ]);

        return response()->json([
            'type'         => 'batch',
            'batch_id'     => $batch->id,
            'subtotal'     => $subtotal,
            'total_amount' => $subtotal,
            'currency'     => $currency,
            'checkout_url' => route('batches.checkout', $batch),
        ], 201);
    }
}
