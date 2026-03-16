<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ManualReservationController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'field_id' => ['required', 'integer', 'exists:fields,id'],
            'date'     => ['required', 'date', 'after_or_equal:today'],
            'time'     => ['required', 'date_format:H:i'],
            'notes'    => ['nullable', 'string', 'max:255'],
        ]);

        $field = Field::with(['venue', 'schedules', 'exceptions'])
            ->findOrFail($data['field_id']);

        // Ensure field belongs to this admin
        if ($user->role !== 'super_admin' && $field->venue->owner_user_id !== $user->id && !$user->isStaffOf($field->venue->id)) {
            abort(403);
        }

        // Ensure admin has an active subscription
        if ($user->role !== 'super_admin' && !$user->activeVenueAdminSubscription()->exists()) {
            return back()->with('error', 'Necesitás una suscripción activa para crear reservas manuales.');
        }

        $start = Carbon::parse($data['date'] . ' ' . $data['time'])->seconds(0);
        $end   = $start->copy()->addMinutes((int) $field->slot_minutes ?: 60);

        // Check overlap with existing active reservations
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
            return back()->with('error', 'Ese horario ya está ocupado.');
        }

        Reservation::create([
            'field_id'         => $field->id,
            'user_id'          => $user->id,
            'start_at'         => $start,
            'end_at'           => $end,
            'status'           => 'PAID',
            'total_amount'     => 0,
            'currency'         => $field->price?->currency ?? 'ARS',
            'payment_provider' => 'manual',
            'verification_code'=> Str::upper(Str::random(8)),
            'notes'            => $data['notes'] ?: null,
        ]);

        return redirect()->route('va.reservations.index', ['date' => $start->toDateString()])
            ->with('success', 'Reserva manual creada correctamente.');
    }
}
