<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ManualReservationController extends Controller
{
    public function searchUsers(Request $request)
    {
        $q = trim($request->input('q', ''));

        if (mb_strlen($q) < 3) {
            return response()->json([]);
        }

        $users = User::where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('email', 'LIKE', '%' . $q . '%')
                      ->orWhere('name', 'LIKE', '%' . $q . '%');
            })
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->limit(10)
            ->get();

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'field_id'       => ['required', 'integer', 'exists:fields,id'],
            'date'           => ['required', 'date', 'after_or_equal:today'],
            'time'           => ['required', 'date_format:H:i'],
            'notes'          => ['nullable', 'string', 'max:255'],
            'client_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'amount_paid'    => ['nullable', 'numeric', 'min:0', 'max:10000000'],
        ]);

        $field = Field::with(['venue', 'schedules', 'exceptions'])
            ->findOrFail($data['field_id']);

        // Ensure field belongs to this admin
        if ($user->role !== 'super_admin' && $field->venue->owner_user_id !== $user->id && !$user->isStaffOf($field->venue->id)) {
            abort(403);
        }

        if ($user->isVenueStaff()) {
            abort_if(!$user->hasStaffPermission('create_manual_reservations', $field->venue->id), 403);
        }

        // Ensure the venue owner has an active subscription (staff inherits owner's subscription)
        if ($user->role !== 'super_admin') {
            $owner = $field->venue->owner;
            if (!$owner || !$owner->activeVenueAdminSubscription()->exists()) {
                return back()->with('error', 'Necesitás una suscripción activa para crear reservas manuales.');
            }
        }

        // Resolve which user_id to assign
        $assignedUserId = $user->id;
        if (!empty($data['client_user_id'])) {
            $clientUser = User::where('id', $data['client_user_id'])
                ->where('is_active', true)
                ->first();
            if ($clientUser) {
                $assignedUserId = $clientUser->id;
            }
        }

        $start = Carbon::parse($data['date'] . ' ' . $data['time'])->seconds(0);
        $slotMinutes = (int) ($field->slot_minutes ?: 60);
        if ($slotMinutes <= 0) $slotMinutes = 60;
        $end = $start->copy()->addMinutes($slotMinutes);

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
            'user_id'          => $assignedUserId,
            'start_at'         => $start,
            'end_at'           => $end,
            'status'           => 'PAID',
            'total_amount'     => $data['amount_paid'] ?? 0,
            'currency'         => $field->price?->currency ?? 'ARS',
            'payment_provider' => 'manual',
            'verification_code'=> Str::upper(Str::random(8)),
            'notes'            => ($data['notes'] ?? null) ?: null,
        ]);

        broadcast(new \App\Events\FieldAvailabilityChanged(
            $field->id,
            $start->toDateString()
        ));

        return redirect()->route('va.reservations.index', ['date' => $start->toDateString()])
            ->with('success', 'Reserva manual creada correctamente.');
    }
}
