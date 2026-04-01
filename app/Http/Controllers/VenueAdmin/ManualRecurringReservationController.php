<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\Reservation;
use App\Models\ReservationBatch;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ManualRecurringReservationController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'field_id'       => ['required', 'integer', 'exists:fields,id'],
            'date'           => ['required', 'date', 'after_or_equal:today'],
            'time'           => ['required', 'date_format:H:i'],
            'frequency'      => ['required', 'in:weekly,biweekly'],
            'occurrences'    => ['required', 'integer', 'min:2', 'max:12'],
            'notes'          => ['nullable', 'string', 'max:255'],
            'client_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'amount_paid'    => ['nullable', 'numeric', 'min:0'],
        ]);

        $field = Field::with(['venue', 'schedules', 'exceptions'])
            ->findOrFail($data['field_id']);

        if ($user->role !== 'super_admin' && $field->venue->owner_user_id !== $user->id && !$user->isStaffOf($field->venue->id)) {
            abort(403);
        }

        if ($user->isVenueStaff()) {
            abort_if(!$user->hasStaffPermission('create_manual_reservations', $field->venue->id), 403);
        }

        if ($user->role !== 'super_admin') {
            $owner = $field->venue->owner;
            if (!$owner || !$owner->activeVenueAdminSubscription()->exists()) {
                return back()->with('error', 'Necesitás una suscripción activa para crear reservas manuales.');
            }
        }

        $assignedUserId = $user->id;
        if (!empty($data['client_user_id'])) {
            $clientUser = User::where('id', $data['client_user_id'])->where('is_active', true)->first();
            if ($clientUser) {
                $assignedUserId = $clientUser->id;
            }
        }

        $baseStart   = Carbon::parse($data['date'] . ' ' . $data['time'])->seconds(0);
        $occurrences = (int) $data['occurrences'];
        $weekInterval = $data['frequency'] === 'biweekly' ? 2 : 1;
        $amountPerTurn = isset($data['amount_paid']) ? round((float) $data['amount_paid'] / $occurrences, 2) : 0;
        $currency = $field->price?->currency ?? 'ARS';

        $result = DB::transaction(function () use ($field, $assignedUserId, $data, $baseStart, $occurrences, $weekInterval, $amountPerTurn, $currency) {
            // Lock the field row to prevent concurrent reservation creation
            Field::where('id', $field->id)->lockForUpdate()->first();

            $batch = ReservationBatch::create([
                'user_id'             => $assignedUserId,
                'field_id'            => $field->id,
                'subtotal'            => $data['amount_paid'] ?? 0,
                'discount_percentage' => 0,
                'discount_amount'     => 0,
                'total_amount'        => $data['amount_paid'] ?? 0,
                'currency'            => $currency,
                'status'              => 'PAID',
                'payment_provider'    => 'manual',
            ]);

            $created = 0;
            $failed  = 0;

            for ($i = 0; $i < $occurrences; $i++) {
                $start = $baseStart->copy()->addWeeks($i * $weekInterval);
                $end   = $start->copy()->addMinutes((int) $field->slot_minutes ?: 60);

                $overlap = Reservation::where('field_id', $field->id)
                    ->whereIn('status', ['PENDING_PAYMENT', 'PAID'])
                    ->where(function ($q) {
                        $q->where('status', '!=', 'PENDING_PAYMENT')
                          ->orWhere(function ($q2) {
                              $q2->where('status', 'PENDING_PAYMENT')
                                 ->whereNotNull('expires_at')
                                 ->where('expires_at', '>', now());
                          });
                    })
                    ->where('start_at', '<', $end)
                    ->where('end_at', '>', $start)
                    ->exists();

                if ($overlap) {
                    $failed++;
                    continue;
                }

                Reservation::create([
                    'batch_id'          => $batch->id,
                    'field_id'          => $field->id,
                    'user_id'           => $assignedUserId,
                    'start_at'          => $start,
                    'end_at'            => $end,
                    'status'            => 'PAID',
                    'total_amount'      => $amountPerTurn,
                    'currency'          => $currency,
                    'payment_provider'  => 'manual',
                    'verification_code' => Str::upper(Str::random(8)),
                    'notes'             => $data['notes'] ?: null,
                ]);

                $created++;
            }

            if ($created === 0) {
                $batch->delete();
                return ['error' => 'No se pudo crear ninguna reserva. Todos los horarios estaban ocupados.'];
            }

            return ['created' => $created, 'failed' => $failed, 'batch' => $batch];
        });

        if (isset($result['error'])) {
            return back()->with('error', $result['error']);
        }

        $created = $result['created'];
        $failed = $result['failed'];

        $msg = "Se crearon {$created} reservas recurrentes manualmente.";
        if ($failed > 0) {
            $msg .= " {$failed} turno(s) no se pudieron crear por conflicto de horario.";
        }

        return redirect()
            ->route('va.reservations.index', ['date' => $baseStart->toDateString()])
            ->with('success', $msg);
    }
}
