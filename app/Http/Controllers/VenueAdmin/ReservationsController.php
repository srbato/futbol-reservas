<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Mail\ReservationCancelledMail;
use App\Models\Reservation;
use App\Services\MercadoPagoRefundService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
            ->whereHas('field.venue', fn ($q) => $q->accessibleBy($user))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($fieldId, fn ($q) => $q->where('field_id', $fieldId))
            ->with(['user', 'field.venue'])
            ->orderBy('start_at')
            ->get();

        $fields = \App\Models\Field::query()
            ->whereHas('venue', fn ($q) => $q->accessibleBy($user))
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
            ? Carbon::parse($request->query('date'))
            : now();

        $fieldId = $request->query('field_id');
        $dayOfWeek = $date->dayOfWeek;

        $startDay = $date->copy()->startOfDay();
        $endDay   = $date->copy()->addDay()->startOfDay();

        $fields = \App\Models\Field::query()
            ->whereHas('venue', fn ($q) => $q->accessibleBy($user))
            ->with(['venue', 'price', 'schedules'])
            ->orderBy('name')
            ->get();

        if ($fieldId) {
            $fields = $fields->where('id', (int) $fieldId)->values();
        }

        $reservations = Reservation::query()
            ->where('start_at', '>=', $startDay)
            ->where('start_at', '<', $endDay)
            ->whereHas('field.venue', fn ($q) => $q->accessibleBy($user))
            ->where('status', 'PAID')
            ->with(['user', 'field'])
            ->get();

        // Mapa: "field_id|HH:MM" => reservation
        $reservationMap = [];
        foreach ($reservations as $reservation) {
            $key = $reservation->field_id . '|' . $reservation->start_at->format('H:i');
            $reservationMap[$key] = $reservation;
        }

        // Generar slots desde los horarios reales de cada cancha
        $slotSet = [];
        foreach ($fields as $field) {
            $schedule = $field->schedules->firstWhere('day_of_week', $dayOfWeek);
            if (!$schedule) {
                continue;
            }
            $slotMinutes = $field->slot_minutes ?: 60;
            $current = Carbon::parse($schedule->open_time);
            $close   = Carbon::parse($schedule->close_time);
            while ($current < $close) {
                $slotSet[$current->format('H:i')] = true;
                $current->addMinutes($slotMinutes);
            }
        }

        // Incluir también los start_at reales (cubre reservas fuera del horario habitual)
        foreach ($reservations as $res) {
            $slotSet[$res->start_at->format('H:i')] = true;
        }

        ksort($slotSet);
        $slots = array_keys($slotSet);

        // Fallback si no hay horarios configurados
        if (empty($slots)) {
            for ($h = 8; $h <= 23; $h++) {
                $slots[] = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
            }
        }

        return view('va.reservations.agenda', compact(
            'date',
            'fields',
            'reservations',
            'reservationMap',
            'fieldId',
            'slots'
        ));
    }

    public function cancel(Request $request, Reservation $reservation, MercadoPagoRefundService $refundService)
    {
        $user = $request->user();

        $reservation->load('field.venue');

        if ($user->role !== 'super_admin' && $reservation->field->venue->owner_user_id !== $user->id && !$user->isStaffOf($reservation->field->venue->id)) {
            abort(403);
        }

        if (in_array($reservation->status, ['CANCELLED', 'EXPIRED'])) {
            return back()->with('error', 'Esta reserva no se puede cancelar.');
        }

        $refundResult = $refundService->refund($reservation);

        $reservation->update([
            'status'     => 'CANCELLED',
            'expires_at' => null,
        ]);

        $reservation->loadMissing(['user', 'field.venue']);

        if ($reservation->user?->email) {
            Mail::to($reservation->user->email)
                ->send(new ReservationCancelledMail($reservation, 'user'));
        }

        $message = match ($refundResult) {
            true  => 'Reserva cancelada. El reembolso fue procesado correctamente.',
            false => 'Reserva cancelada. No se pudo procesar el reembolso automáticamente — revisá los logs.',
            null  => 'Reserva cancelada por el administrador.',
        };

        return back()->with('success', $message);
    }
}