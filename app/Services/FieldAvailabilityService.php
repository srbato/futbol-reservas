<?php

namespace App\Services;

use App\Models\Field;
use App\Models\Reservation;
use Carbon\Carbon;

/**
 * Calcula los slots disponibles de una cancha para una fecha dada.
 * Lógica extraída de AvailabilityController para poder reutilizarla
 * desde el grid de canchas del complejo (vista nueva ATC) sin duplicar código.
 *
 * Devuelve EXACTAMENTE el mismo shape que AvailabilityController para que
 * cualquier consumidor existente siga funcionando.
 */
class FieldAvailabilityService
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function computeSlots(Field $field, Carbon $date): array
    {
        $date = $date->copy()->startOfDay();
        $dow = (int) $date->dayOfWeek;

        $exception = $field->exceptions()->whereDate('date', $date)->first();
        if ($exception?->is_closed) {
            return [];
        }

        $schedule = $field->schedules()->where('day_of_week', $dow)->first();
        if (!$schedule) {
            return [];
        }

        $openTime  = $exception?->open_time  ?? $schedule->open_time;
        $closeTime = $exception?->close_time ?? $schedule->close_time;

        $open  = Carbon::parse($date->toDateString() . ' ' . $openTime);
        $close = Carbon::parse($date->toDateString() . ' ' . $closeTime);

        $slotMinutes = (int) ($field->slot_minutes ?: 60);
        $price       = (float) ($field->price?->price_per_slot ?? 0);
        $currency    = $field->price?->currency ?? 'ARS';

        $nightPrice = $field->price?->night_price_per_slot ? (float) $field->price->night_price_per_slot : null;
        $nightStart = $field->price?->night_start_time ?? null;
        $nightEnd   = $field->price?->night_end_time ?? null;

        $reservations = Reservation::query()
            ->where('field_id', $field->id)
            ->whereIn('status', ['PAID', 'PENDING_PAYMENT', 'PENDING_CASH'])
            ->where(function ($q) {
                $q->whereIn('status', ['PAID', 'PENDING_CASH'])
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'PENDING_PAYMENT')
                         ->whereNotNull('expires_at')
                         ->where('expires_at', '>', now());
                  });
            })
            ->whereDate('start_at', $date->toDateString())
            ->get(['start_at', 'end_at']);

        $blocks = $field->blocks()
            ->whereDate('date', $date->toDateString())
            ->get(['start_time', 'end_time', 'reason']);

        $discounts = $field->discounts()
            ->where('is_active', true)
            ->get();

        $slots = [];

        for ($t = $open->copy(); $t->lt($close); $t->addMinutes($slotMinutes)) {
            $slotStart = $t->copy();
            $slotEnd   = $t->copy()->addMinutes($slotMinutes);

            if ($slotEnd->gt($close)) {
                break;
            }

            $occupied = $reservations->first(fn ($r) =>
                $r->start_at < $slotEnd && $r->end_at > $slotStart
            );
            $occupiedUntil = $occupied ? $occupied->end_at->format('H:i') : null;

            $blocked = $blocks->first(function ($b) use ($date, $slotStart, $slotEnd) {
                $blockStart = Carbon::parse($date->toDateString() . ' ' . $b->start_time);
                $blockEnd   = Carbon::parse($date->toDateString() . ' ' . $b->end_time);
                return $blockStart < $slotEnd && $blockEnd > $slotStart;
            });

            $isPastSlot = $slotStart->lessThan(now());

            $status = 'AVAILABLE';
            $reason = null;

            if ($blocked) {
                $status = 'BLOCKED';
                $reason = $blocked->reason;
            } elseif ($occupied) {
                $status = 'UNAVAILABLE';
            } elseif ($isPastSlot) {
                $status = 'PAST';
            }

            $isNightSlot = false;
            if ($nightPrice !== null && $nightStart && $nightEnd) {
                $nightStartCarbon = Carbon::parse($date->toDateString() . ' ' . $nightStart);
                $nightEndCarbon   = Carbon::parse($date->toDateString() . ' ' . $nightEnd);
                $isNightSlot = $nightStartCarbon < $slotEnd && $nightEndCarbon > $slotStart;
            }

            $finalPrice    = $isNightSlot ? $nightPrice : $price;
            $originalPrice = $finalPrice;
            $discountLabel = null;
            $hasDiscount   = false;

            $matchingDiscount = $discounts->first(function ($d) use ($date, $dow, $slotStart, $slotEnd) {
                if ($d->date && $d->date->toDateString() !== $date->toDateString()) {
                    return false;
                }
                if (!$d->date && !is_null($d->day_of_week) && (int) $d->day_of_week !== $dow) {
                    return false;
                }
                if ($d->start_time && $d->end_time) {
                    $discountStart = Carbon::parse($date->toDateString() . ' ' . $d->start_time);
                    $discountEnd   = Carbon::parse($date->toDateString() . ' ' . $d->end_time);
                    return $discountStart < $slotEnd && $discountEnd > $slotStart;
                }
                return true;
            });

            if ($matchingDiscount) {
                $finalPrice    = (float) $matchingDiscount->discount_price;
                $discountLabel = $matchingDiscount->label;
                $hasDiscount   = true;
            }

            // entity_key permite al frontend agrupar slots consecutivos de la misma reserva/bloqueo
            // y renderizarlos como una sola tarjeta (grid-column: span N).
            $entityKey = null;
            if ($status === 'UNAVAILABLE' && $occupied) {
                $entityKey = 'r' . $occupied->getKey();
            } elseif ($status === 'BLOCKED' && $blocked) {
                $entityKey = 'b' . ($blocked->getKey() ?? ($blocked->start_time . '-' . $blocked->end_time));
            }

            $slots[] = [
                'start_at'       => $slotStart->format('H:i'),
                'end_at'         => $slotEnd->format('H:i'),
                'status'         => $status,
                'price'          => $finalPrice,
                'original_price' => $originalPrice,
                'currency'       => $currency,
                'reason'         => $reason,
                'has_discount'   => $hasDiscount,
                'discount_label' => $discountLabel,
                'is_night_price' => $isNightSlot,
                'occupied_until' => $status === 'UNAVAILABLE' ? $occupiedUntil : null,
                'entity_key'     => $entityKey,
            ];
        }

        return $slots;
    }
}
