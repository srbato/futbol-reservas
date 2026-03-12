<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $date = $request->query('date')
            ? \Carbon\Carbon::parse($request->query('date'))
            : now();

        $status = $request->query('status');
        $fieldId = $request->query('field_id');

        $startDay = $date->copy()->startOfDay();
        $endDay = $date->copy()->addDay()->startOfDay();

        $reservations = Reservation::query()
            ->where('start_at', '>=', $startDay)
            ->where('start_at', '<', $endDay)
            ->whereHas('field.venue', function ($q) use ($user) {
                $q->where('owner_user_id', $user->id);
            })
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($fieldId, fn ($q) => $q->where('field_id', $fieldId))
            ->with(['user', 'field.venue'])
            ->orderBy('start_at')
            ->get();

        $fields = \App\Models\Field::query()
            ->whereHas('venue', function ($q) use ($user) {
                $q->where('owner_user_id', $user->id);
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('va.reservations.index', compact(
            'reservations',
            'date',
            'status',
            'fieldId',
            'fields'
        ));
    }

    public function agenda(Request $request)
    {
        $user = $request->user();

        $date = $request->query('date')
            ? \Carbon\Carbon::parse($request->query('date'))
            : now();

        $fieldId = $request->query('field_id');

        $startDay = $date->copy()->startOfDay();
        $endDay = $date->copy()->addDay()->startOfDay();

        $fields = \App\Models\Field::query()
            ->whereHas('venue', function ($q) use ($user) {
                $q->where('owner_user_id', $user->id);
            })
            ->with(['venue', 'price'])
            ->orderBy('name')
            ->get();

        if ($fieldId) {
            $fields = $fields->where('id', (int) $fieldId)->values();
        }

        $reservations = Reservation::query()
            ->where('start_at', '>=', $startDay)
            ->where('start_at', '<', $endDay)
            ->whereHas('field.venue', function ($q) use ($user) {
                $q->where('owner_user_id', $user->id);
            })
            ->with(['user', 'field'])
            ->get();

        // Agrupar reservas por cancha + hora de inicio
        $reservationMap = [];
        foreach ($reservations as $reservation) {
            $key = $reservation->field_id . '|' . $reservation->start_at->format('H:i');
            $reservationMap[$key] = $reservation;
        }

        return view('va.reservations.agenda', compact(
            'date',
            'fields',
            'reservations',
            'reservationMap',
            'fieldId'
        ));
    }

    public function cancel(Request $request, Reservation $reservation)
    {
        $user = $request->user();

        $reservation->load('field.venue');

        if ($user->role !== 'super_admin' && $reservation->field->venue->owner_user_id !== $user->id) {
            abort(403);
        }

        if (in_array($reservation->status, ['CHECKED_IN', 'CANCELLED', 'EXPIRED'])) {
            return back()->with('error', 'Esta reserva no se puede cancelar.');
        }

        $reservation->update([
            'status' => 'CANCELLED',
            'expires_at' => null,
        ]);

        return back()->with('success', 'Reserva cancelada por el administrador.');
    }
}