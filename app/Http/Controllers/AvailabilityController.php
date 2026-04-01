<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function show(Request $request, Field $field)
    {
        $request->validate([
            'date' => ['required', 'date'],
        ]);

        $date = Carbon::parse($request->query('date'))->startOfDay();
        $dow = (int) $date->dayOfWeek; // 0=Dom ... 6=Sab

        $exception = $field->exceptions()->whereDate('date', $date)->first();
        if ($exception?->is_closed) {
            return response()->json([
                'date' => $date->toDateString(),
                'slots' => [],
            ]);
        }

        $schedule = $field->schedules()->where('day_of_week', $dow)->first();
        if (!$schedule) {
            return response()->json([
                'date' => $date->toDateString(),
                'slots' => [],
            ]);
        }

        $openTime = $exception?->open_time ?? $schedule->open_time;
        $closeTime = $exception?->close_time ?? $schedule->close_time;

        $open = Carbon::parse($date->toDateString() . ' ' . $openTime);
        $close = Carbon::parse($date->toDateString() . ' ' . $closeTime);

        $slotMinutes   = (int) ($field->slot_minutes ?: 60);
        $price         = (float) ($field->price?->price_per_slot ?? 0);
        $currency      = $field->price?->currency ?? 'ARS';

        $nightPrice     = $field->price?->night_price_per_slot ? (float) $field->price->night_price_per_slot : null;
        $nightStart     = $field->price?->night_start_time ?? null;
        $nightEnd       = $field->price?->night_end_time ?? null;

        $reservations = Reservation::query()
            ->where('field_id', $field->id)
            ->whereIn('status', ['PAID', 'PENDING_PAYMENT'])
            ->where(function ($q) {
                $q->where('status', '!=', 'PENDING_PAYMENT')
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
            $slotEnd = $t->copy()->addMinutes($slotMinutes);

            if ($slotEnd->gt($close)) {
                break;
            }

            $occupied = $reservations->first(fn ($r) =>
                $r->start_at < $slotEnd && $r->end_at > $slotStart
            );

            $blocked = $blocks->first(function ($b) use ($date, $slotStart, $slotEnd) {
                $blockStart = Carbon::parse($date->toDateString() . ' ' . $b->start_time);
                $blockEnd = Carbon::parse($date->toDateString() . ' ' . $b->end_time);

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

            // Precio base: nocturno si el slot cae dentro de la franja, normal si no
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
                // Si tiene fecha puntual y no coincide, no aplica
                if ($d->date && $d->date->toDateString() !== $date->toDateString()) {
                    return false;
                }

                // Si NO tiene fecha puntual, pero sí día de semana, debe coincidir
                if (!$d->date && !is_null($d->day_of_week) && (int) $d->day_of_week !== $dow) {
                    return false;
                }

                // Si tiene horario definido, debe cruzarse con el slot
                if ($d->start_time && $d->end_time) {
                    $discountStart = Carbon::parse($date->toDateString() . ' ' . $d->start_time);
                    $discountEnd = Carbon::parse($date->toDateString() . ' ' . $d->end_time);

                    return $discountStart < $slotEnd && $discountEnd > $slotStart;
                }

                // Si no tiene horario, aplica a todo el día
                return true;
            });

            if ($matchingDiscount) {
                $finalPrice = (float) $matchingDiscount->discount_price;
                $discountLabel = $matchingDiscount->label;
                $hasDiscount = true;
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
            ];
        }

        return response()->json([
            'date' => $date->toDateString(),
            'field_id' => $field->id,
            'slots' => $slots,
        ]);
    }

    /**
     * Check availability for all slots in a recurring reservation.
     */
    public function recurring(Request $request, Field $field)
    {
        $request->validate([
            'start_at'    => ['required', 'date'],
            'frequency'   => ['required', 'in:weekly,biweekly'],
            'occurrences' => ['required', 'integer', 'min:2', 'max:12'],
        ]);

        $baseStart   = Carbon::parse($request->query('start_at'))->seconds(0);
        $occurrences = (int) $request->query('occurrences');
        $weekInterval = $request->query('frequency') === 'biweekly' ? 2 : 1;
        $slotMinutes = (int) ($field->slot_minutes ?: 60);

        $field->loadMissing(['venue', 'schedules', 'exceptions', 'price']);

        $slots = [];

        for ($i = 0; $i < $occurrences; $i++) {
            $start = $baseStart->copy()->addWeeks($i * $weekInterval);
            $end   = $start->copy()->addMinutes($slotMinutes);
            $date  = $start->copy()->startOfDay();
            $dow   = (int) $start->dayOfWeek;

            $status = 'available';
            $reason = null;

            // Past
            if ($start->lessThan(now())) {
                $status = 'unavailable';
                $reason = 'Turno pasado';
                $slots[] = compact('start', 'status', 'reason') + ['start' => $start->toDateTimeString()];
                continue;
            }

            // Exception closed
            $exception = $field->exceptions()->whereDate('date', $date->toDateString())->first();
            if ($exception?->is_closed) {
                $status = 'unavailable';
                $reason = 'Cancha cerrada ese día';
                $slots[] = ['start' => $start->toDateTimeString(), 'status' => $status, 'reason' => $reason];
                continue;
            }

            // No schedule
            $schedule = $field->schedules->firstWhere('day_of_week', $dow);
            if (!$schedule && !$exception) {
                $status = 'unavailable';
                $reason = 'Sin horario ese día';
                $slots[] = ['start' => $start->toDateTimeString(), 'status' => $status, 'reason' => $reason];
                continue;
            }

            // Out of range
            $openTime  = $exception?->open_time ?? $schedule?->open_time;
            $closeTime = $exception?->close_time ?? $schedule?->close_time;
            if ($openTime && $closeTime) {
                $open  = Carbon::parse($date->toDateString() . ' ' . $openTime);
                $close = Carbon::parse($date->toDateString() . ' ' . $closeTime);
                if ($start->lt($open) || $end->gt($close)) {
                    $status = 'unavailable';
                    $reason = 'Fuera del horario de la cancha';
                    $slots[] = ['start' => $start->toDateTimeString(), 'status' => $status, 'reason' => $reason];
                    continue;
                }
            }

            // Blocked
            $blocked = $field->blocks()
                ->whereDate('date', $date->toDateString())
                ->get(['start_time', 'end_time', 'reason'])
                ->first(function ($block) use ($date, $start, $end) {
                    $blockStart = Carbon::parse($date->toDateString() . ' ' . $block->start_time);
                    $blockEnd   = Carbon::parse($date->toDateString() . ' ' . $block->end_time);
                    return $blockStart < $end && $blockEnd > $start;
                });

            if ($blocked) {
                $status = 'unavailable';
                $reason = 'Horario bloqueado';
                $slots[] = ['start' => $start->toDateTimeString(), 'status' => $status, 'reason' => $reason];
                continue;
            }

            // Overlap with existing reservation
            $overlap = Reservation::query()
                ->where('field_id', $field->id)
                ->whereIn('status', ['PENDING_PAYMENT', 'PAID'])
                ->where(function ($q) {
                    $q->where('status', '!=', 'PENDING_PAYMENT')
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
                $status = 'unavailable';
                $reason = 'Ya está reservado';
            }

            $slots[] = ['start' => $start->toDateTimeString(), 'status' => $status, 'reason' => $reason];
        }

        return response()->json([
            'field_id' => $field->id,
            'slots'    => $slots,
        ]);
    }
}