<?php

namespace App\Services;

use App\Events\FieldAvailabilityChanged;
use App\Models\Field;
use App\Models\Reservation;
use App\Models\VenueUserBlock;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReservationService
{
    /**
     * Creates a single reservation. Throws HttpException on failure.
     */
    public function createSingle(Field $field, Carbon $start, int $userId, int $expiresInMinutes = 10, ?int $batchId = null, ?int $recurringSubscriptionId = null): Reservation
    {
        if (!$field->is_active) {
            abort(422, 'La cancha no está activa.');
        }

        if (!$field->venue || !$field->venue->is_active) {
            abort(422, 'El complejo no está activo.');
        }

        if (VenueUserBlock::isBlocked($userId, $field->venue->id)) {
            throw new \App\Exceptions\VenueBlockedException('No podés reservar en este complejo. Contactá al complejo para más información.');
        }

        $start = $start->copy()->seconds(0);
        $end = $start->copy()->addMinutes((int) $field->slot_minutes);

        if ($start->lessThan(now())) {
            abort(422, 'No podés reservar horarios pasados.');
        }

        $date = $start->copy()->startOfDay();
        $dow = (int) $start->dayOfWeek;

        $exception = $field->exceptions()
            ->whereDate('date', $date->toDateString())
            ->first();

        if ($exception?->is_closed) {
            abort(422, 'La cancha está cerrada en esa fecha.');
        }

        $schedule = $field->schedules()
            ->where('day_of_week', $dow)
            ->first();

        if (!$schedule && !$exception) {
            abort(422, 'La cancha no tiene horario configurado para ese día.');
        }

        $openTime = $exception?->open_time ?? $schedule?->open_time;
        $closeTime = $exception?->close_time ?? $schedule?->close_time;

        if (!$openTime || !$closeTime) {
            abort(422, 'No hay horario válido configurado para esa fecha.');
        }

        $open = Carbon::parse($date->toDateString() . ' ' . $openTime);
        $close = Carbon::parse($date->toDateString() . ' ' . $closeTime);

        if ($start->lt($open) || $end->gt($close)) {
            abort(422, 'El horario elegido está fuera del rango disponible.');
        }

        return DB::transaction(function () use ($field, $start, $end, $date, $userId, $expiresInMinutes, $batchId, $recurringSubscriptionId) {
            DB::table('fields')
                ->where('id', $field->id)
                ->lockForUpdate()
                ->first();

            $blocked = $field->blocks()
                ->whereDate('date', $date->toDateString())
                ->get(['start_time', 'end_time', 'reason'])
                ->first(function ($block) use ($date, $start, $end) {
                    $blockStart = Carbon::parse($date->toDateString() . ' ' . $block->start_time);
                    $blockEnd = Carbon::parse($date->toDateString() . ' ' . $block->end_time);
                    return $blockStart < $end && $blockEnd > $start;
                });

            if ($blocked) {
                abort(422, 'Ese horario está bloqueado actualmente.');
            }

            $overlap = Reservation::query()
                ->where('field_id', $field->id)
                ->whereIn('status', ['PENDING_PAYMENT', 'PENDING_CASH', 'PAID'])
                ->where(function ($q) {
                    $q->whereIn('status', ['PAID', 'PENDING_CASH'])
                      ->orWhere(function ($q2) {
                          $q2->where('status', 'PENDING_PAYMENT')
                             ->whereNotNull('expires_at')
                             ->where('expires_at', '>', now());
                      });
                })
                ->where(function ($q) use ($start, $end) {
                    $q->where('start_at', '<', $end)
                      ->where('end_at', '>', $start);
                })
                ->exists();

            if ($overlap) {
                abort(409, 'Ese horario ya está reservado.');
            }

            $finalPrice = $this->calculatePrice($field, $start);

            // Reservas de suscripción recurrente ya están pagas (la suscripción cubre el pago)
            $isPaidBySubscription = $recurringSubscriptionId !== null;

            $reservation = Reservation::create([
                'batch_id' => $batchId,
                'field_id' => $field->id,
                'user_id' => $userId,
                'start_at' => $start,
                'end_at' => $end,
                'status' => $isPaidBySubscription ? 'PAID' : 'PENDING_PAYMENT',
                'total_amount' => $finalPrice,
                'currency' => $field->price?->currency ?? 'ARS',
                'expires_at' => $isPaidBySubscription ? null : now()->addMinutes($expiresInMinutes ?? 10),
                'verification_code' => Str::upper(Str::random(8)),
                'recurring_subscription_id' => $recurringSubscriptionId,
            ]);

            broadcast(new FieldAvailabilityChanged($field->id, $start->toDateString()));

            return $reservation;
        });
    }

    /**
     * Calcula el precio de un slot para una cancha y horario dados.
     * Aplica precio nocturno y descuentos activos.
     */
    public function calculatePrice(Field $field, Carbon $start): float
    {
        $field->loadMissing(['price', 'discounts']);

        $date = $start->copy()->startOfDay();
        $end  = $start->copy()->addMinutes((int) ($field->slot_minutes ?: 60));
        $dow  = (int) $start->dayOfWeek;

        $basePrice  = (float) ($field->price?->price_per_slot ?? 0);
        $nightPrice = $field->price?->night_price_per_slot ? (float) $field->price->night_price_per_slot : null;
        $nightStart = $field->price?->night_start_time ?? null;
        $nightEnd   = $field->price?->night_end_time ?? null;

        if ($nightPrice !== null && $nightStart && $nightEnd) {
            $nightStartCarbon = Carbon::parse($date->toDateString() . ' ' . $nightStart);
            $nightEndCarbon   = Carbon::parse($date->toDateString() . ' ' . $nightEnd);
            if ($nightStartCarbon < $end && $nightEndCarbon > $start) {
                $basePrice = $nightPrice;
            }
        }

        $finalPrice = $basePrice;

        // Buscar TODOS los descuentos que matchean y elegir el MÁS BAJO (mejor para el usuario)
        $matchingDiscounts = $field->discounts()
            ->where('is_active', true)
            ->get()
            ->filter(function ($discount) use ($date, $dow, $start, $end) {
                if ($discount->date && $discount->date->toDateString() !== $date->toDateString()) {
                    return false;
                }
                if (!$discount->date && !is_null($discount->day_of_week) && (int) $discount->day_of_week !== $dow) {
                    return false;
                }
                if ($discount->start_time && $discount->end_time) {
                    $discountStart = Carbon::parse($date->toDateString() . ' ' . $discount->start_time);
                    $discountEnd   = Carbon::parse($date->toDateString() . ' ' . $discount->end_time);
                    return $discountStart < $end && $discountEnd > $start;
                }
                return true;
            });

        if ($matchingDiscounts->isNotEmpty()) {
            // Mejor descuento = el precio final más bajo
            $bestDiscount = $matchingDiscounts->sortBy(fn($d) => (float) $d->discount_price)->first();
            $discountedPrice = (float) $bestDiscount->discount_price;
            // Solo aplicar si realmente es menor al precio base (evitar "descuentos" que suben precio)
            if ($discountedPrice < $finalPrice) {
                $finalPrice = $discountedPrice;
            }
        }

        return $finalPrice;
    }
}
