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

        if ($user->isVenueStaff()) {
            abort_if(!$user->hasStaffPermission('view_reservations', $user->activeStaffVenueId()), 403);
        }

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

        if ($user->isVenueStaff()) {
            abort_if(!$user->hasStaffPermission('view_agenda', $user->activeStaffVenueId()), 403);
        }

        $view    = $request->query('view') === 'week' ? 'week' : 'day';
        $fieldId = $request->query('field_id');

        $date = $request->query('date')
            ? Carbon::parse($request->query('date'))
            : now();

        $allFields = \App\Models\Field::query()
            ->whereHas('venue', fn ($q) => $q->accessibleBy($user))
            ->with(['venue', 'price', 'schedules'])
            ->orderBy('name')
            ->get();

        // ── Vista semanal ──────────────────────────────────────────────────────
        if ($view === 'week') {
            $weekStart = $date->copy()->startOfWeek(Carbon::MONDAY);
            $weekEnd   = $weekStart->copy()->addDays(7);

            $selectedField = $fieldId
                ? $allFields->firstWhere('id', (int) $fieldId)
                : $allFields->first();

            if ($selectedField) {
                $fieldId = $selectedField->id;
            }

            $reservations = $selectedField
                ? Reservation::query()
                    ->where('start_at', '>=', $weekStart)
                    ->where('start_at', '<', $weekEnd)
                    ->where('field_id', $selectedField->id)
                    ->where('status', 'PAID')
                    ->with(['user', 'field'])
                    ->get()
                : collect();

            // Mapa: "YYYY-MM-DD|HH:MM" => reservation
            $reservationMap = [];
            foreach ($reservations as $reservation) {
                $key = $reservation->start_at->format('Y-m-d|H:i');
                $reservationMap[$key] = $reservation;
            }

            // Armar los 7 días y los slots activos por día
            $weekDays          = [];
            $activeSlotsPerDay = [];
            $slotSet           = [];

            for ($i = 0; $i < 7; $i++) {
                $day     = $weekStart->copy()->addDays($i);
                $dateKey = $day->format('Y-m-d');
                $weekDays[]                    = $day;
                $activeSlotsPerDay[$dateKey]   = [];

                if ($selectedField) {
                    $dow      = $day->dayOfWeek;
                    $schedule = $selectedField->schedules->firstWhere('day_of_week', $dow);
                    if ($schedule) {
                        $slotMinutes = $selectedField->slot_minutes ?: 60;
                        $current     = Carbon::parse($schedule->open_time);
                        $close       = Carbon::parse($schedule->close_time);
                        while ($current < $close) {
                            $t = $current->format('H:i');
                            $slotSet[$t]                     = true;
                            $activeSlotsPerDay[$dateKey][]   = $t;
                            $current->addMinutes($slotMinutes);
                        }
                    }
                }
            }

            // Incluir horarios reales de reservas existentes
            foreach ($reservations as $res) {
                $t       = $res->start_at->format('H:i');
                $dateKey = $res->start_at->format('Y-m-d');
                $slotSet[$t] = true;
                if (!in_array($t, $activeSlotsPerDay[$dateKey] ?? [])) {
                    $activeSlotsPerDay[$dateKey][] = $t;
                }
            }

            ksort($slotSet);
            $slots = array_keys($slotSet);

            if (empty($slots)) {
                for ($h = 8; $h <= 23; $h++) {
                    $slots[] = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
                }
            }

            $fields = $allFields;

            return view('va.reservations.agenda', compact(
                'date', 'fields', 'reservations', 'reservationMap', 'fieldId',
                'slots', 'weekDays', 'weekStart', 'selectedField', 'activeSlotsPerDay', 'view'
            ));
        }

        // ── Vista diaria ───────────────────────────────────────────────────────
        $fields = $allFields;
        if ($fieldId) {
            $fields = $fields->where('id', (int) $fieldId)->values();
        }

        $dayOfWeek = $date->dayOfWeek;
        $startDay  = $date->copy()->startOfDay();
        $endDay    = $date->copy()->addDay()->startOfDay();

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
            $current     = Carbon::parse($schedule->open_time);
            $close       = Carbon::parse($schedule->close_time);
            while ($current < $close) {
                $slotSet[$current->format('H:i')] = true;
                $current->addMinutes($slotMinutes);
            }
        }

        foreach ($reservations as $res) {
            $slotSet[$res->start_at->format('H:i')] = true;
        }

        ksort($slotSet);
        $slots = array_keys($slotSet);

        if (empty($slots)) {
            for ($h = 8; $h <= 23; $h++) {
                $slots[] = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
            }
        }

        $weekDays          = null;
        $weekStart         = null;
        $selectedField     = null;
        $activeSlotsPerDay = [];

        return view('va.reservations.agenda', compact(
            'date', 'fields', 'reservations', 'reservationMap', 'fieldId',
            'slots', 'weekDays', 'weekStart', 'selectedField', 'activeSlotsPerDay', 'view'
        ));
    }

    public function cancel(Request $request, Reservation $reservation, MercadoPagoRefundService $refundService)
    {
        $user = $request->user();

        $reservation->load('field.venue');

        if ($user->role !== 'super_admin' && $reservation->field->venue->owner_user_id !== $user->id && !$user->isStaffOf($reservation->field->venue->id)) {
            abort(403);
        }

        if ($user->isVenueStaff()) {
            abort_if(!$user->hasStaffPermission('cancel_reservations', $reservation->field->venue->id), 403);
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