<?php

namespace App\Services;

use App\Events\FieldAvailabilityChanged;
use App\Mail\ReservationModifiedMail;
use App\Models\Field;
use App\Models\Reservation;
use App\Notifications\ReservationModifiedNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReservationModifyService
{
    public function __construct(private ReservationService $reservationService) {}

    /**
     * Aplica el cambio de horario de forma atómica.
     * Verifica disponibilidad con lockForUpdate antes de modificar.
     *
     * Lanza HttpException si el slot ya no está disponible.
     */
    public function applyChange(
        Reservation $reservation,
        Field $newField,
        Carbon $newStart,
        float $newPrice
    ): void {
        $newEnd     = $newStart->copy()->addMinutes((int) ($newField->slot_minutes ?: 60));
        $oldFieldId = $reservation->field_id;
        $oldDate    = $reservation->start_at->toDateString();

        DB::transaction(function () use ($reservation, $newField, $newStart, $newEnd, $newPrice) {
            // Lock para evitar race conditions
            DB::table('reservations')->where('id', $reservation->id)->lockForUpdate()->first();
            DB::table('fields')->where('id', $newField->id)->lockForUpdate()->first();

            // Verificar que el nuevo slot sigue disponible
            $overlap = Reservation::query()
                ->where('field_id', $newField->id)
                ->whereIn('status', ['PENDING_PAYMENT', 'PAID'])
                ->where('id', '!=', $reservation->id)
                ->where(function ($q) {
                    $q->where('status', '!=', 'PENDING_PAYMENT')
                      ->orWhere(function ($q2) {
                          $q2->where('status', 'PENDING_PAYMENT')
                             ->whereNotNull('expires_at')
                             ->where('expires_at', '>', now());
                      });
                })
                ->where(function ($q) use ($newStart, $newEnd) {
                    $q->where('start_at', '<', $newEnd)
                      ->where('end_at', '>', $newStart);
                })
                ->exists();

            if ($overlap) {
                abort(409, 'Ese horario fue reservado por otro usuario mientras confirmabas. Por favor elegí otro.');
            }

            // Verificar bloqueos
            $date    = $newStart->copy()->startOfDay();
            $blocked = $newField->blocks()
                ->whereDate('date', $date->toDateString())
                ->get(['start_time', 'end_time'])
                ->first(function ($block) use ($date, $newStart, $newEnd) {
                    $bs = Carbon::parse($date->toDateString() . ' ' . $block->start_time);
                    $be = Carbon::parse($date->toDateString() . ' ' . $block->end_time);
                    return $bs < $newEnd && $be > $newStart;
                });

            if ($blocked) {
                abort(422, 'Ese horario está bloqueado.');
            }

            // Aplicar el cambio
            $updatePayload = [
                'field_id'            => $newField->id,
                'start_at'            => $newStart,
                'end_at'              => $newEnd,
                'total_amount'        => $newPrice,
                'modified_at'         => now(),
                'modification_status' => 'COMPLETED',
            ];

            $reservation->update($updatePayload);

            $reservation->refresh();
        });

        // Broadcast FUERA de la transacción: un fallo de Reverb no puede revertir el cambio de horario
        try {
            broadcast(new FieldAvailabilityChanged($oldFieldId, $oldDate));
            broadcast(new FieldAvailabilityChanged($newField->id, $newStart->toDateString()));
        } catch (\Throwable $e) {
            Log::warning('applyChange: broadcast falló (no crítico)', [
                'error' => $e->getMessage(),
            ]);
        }

        // Notificación interna (campana + web push)
        try {
            $reservation->loadMissing('user');
            $reservation->user->notify(new ReservationModifiedNotification($reservation));
        } catch (\Throwable $e) {
            Log::warning('applyChange: notificación falló (no crítico)', [
                'error' => $e->getMessage(),
            ]);
        }

        // Email al venue owner
        try {
            $reservation->loadMissing('field.venue.owner');
            $venueOwner = $reservation->field->venue->owner ?? null;
            if ($venueOwner && $venueOwner->email) {
                Mail::to($venueOwner->email)->queue(new ReservationModifiedMail($reservation, 0, null));
            }
        } catch (\Throwable $e) {
            Log::warning('applyChange: email al venue owner falló (no crítico)', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
