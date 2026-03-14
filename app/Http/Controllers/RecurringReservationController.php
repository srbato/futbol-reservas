<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\ReservationBatch;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RecurringReservationController extends Controller
{
    public function store(Request $request, ReservationService $reservationService)
    {
        $data = $request->validate([
            'field_id'    => ['required', 'integer', 'exists:fields,id'],
            'start_at'    => ['required', 'date'],
            'frequency'   => ['required', 'in:weekly,biweekly'],
            'occurrences' => ['required', 'integer', 'min:2', 'max:12'],
        ]);

        $field = Field::query()
            ->with(['venue', 'price', 'schedules', 'exceptions', 'discounts', 'recurringDiscounts'])
            ->findOrFail($data['field_id']);

        $userId = $request->user()->id;
        $baseStart = Carbon::parse($data['start_at'])->seconds(0);
        $occurrences = (int) $data['occurrences'];
        $weekInterval = $data['frequency'] === 'biweekly' ? 2 : 1;
        $currency = $field->price?->currency ?? 'ARS';

        // Pick the best applicable recurring discount tier
        $applicableTier = $field->recurringDiscounts()
            ->where('is_active', true)
            ->where('min_occurrences', '<=', $occurrences)
            ->orderByDesc('min_occurrences')
            ->first();

        $discountPercentage = $applicableTier ? (float) $applicableTier->discount_percentage : 0;

        // Create the batch record first so we have a batch_id
        $batch = ReservationBatch::create([
            'user_id'             => $userId,
            'field_id'            => $field->id,
            'subtotal'            => 0,
            'discount_percentage' => $discountPercentage,
            'discount_amount'     => 0,
            'total_amount'        => 0,
            'currency'            => $currency,
            'status'              => 'PENDING_PAYMENT',
            'expires_at'          => now()->addMinutes(30),
        ]);

        $results = [];
        $subtotal = 0;

        for ($i = 0; $i < $occurrences; $i++) {
            $start = $baseStart->copy()->addWeeks($i * $weekInterval);

            try {
                $reservation = $reservationService->createSingle(
                    $field,
                    $start,
                    $userId,
                    expiresInMinutes: 30,
                    batchId: $batch->id
                );

                $subtotal += (float) $reservation->total_amount;

                $results[] = [
                    'date'           => $start->locale('es')->isoFormat('dddd D/MM/YYYY'),
                    'time'           => $start->format('H:i'),
                    'status'         => 'created',
                    'reservation_id' => $reservation->id,
                    'amount'         => (float) $reservation->total_amount,
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'date'   => $start->locale('es')->isoFormat('dddd D/MM/YYYY'),
                    'time'   => $start->format('H:i'),
                    'status' => 'failed',
                    'reason' => $e->getMessage(),
                ];
            }
        }

        $discountAmount = round($subtotal * $discountPercentage / 100, 2);
        $total = round($subtotal - $discountAmount, 2);

        $batch->update([
            'subtotal'        => $subtotal,
            'discount_amount' => $discountAmount,
            'total_amount'    => $total,
        ]);

        $created = collect($results)->where('status', 'created')->count();
        $failed  = collect($results)->where('status', 'failed')->count();

        // If zero reservations were created, delete the empty batch
        if ($created === 0) {
            $batch->delete();
            return response()->json([
                'results' => $results,
                'summary' => ['created' => 0, 'failed' => $failed],
                'batch'   => null,
            ], 422);
        }

        return response()->json([
            'results' => $results,
            'summary' => [
                'created' => $created,
                'failed'  => $failed,
            ],
            'batch' => [
                'id'                  => $batch->id,
                'subtotal'            => $subtotal,
                'discount_percentage' => $discountPercentage,
                'discount_amount'     => $discountAmount,
                'total_amount'        => $total,
                'currency'            => $currency,
                'checkout_url'        => route('batches.checkout', $batch),
            ],
        ], 201);
    }
}
