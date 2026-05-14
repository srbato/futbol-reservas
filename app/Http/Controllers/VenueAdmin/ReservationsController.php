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
            ->with(['venue', 'price', 'schedules', 'exceptions'])
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
                    ->whereIn('status', ['PAID', 'PENDING_CASH', 'PENDING_PAYMENT'])
                    ->where(function ($q) {
                        $q->whereIn('status', ['PAID', 'PENDING_CASH'])
                          ->orWhere(function ($q2) {
                              $q2->where('status', 'PENDING_PAYMENT')
                                 ->whereNotNull('expires_at')
                                 ->where('expires_at', '>', now());
                          });
                    })
                    ->with(['user', 'field'])
                    ->get()
                : collect();

            // Bloqueos del field para la semana
            $weekBlocks = $selectedField
                ? \App\Models\FieldBlock::query()
                    ->where('field_id', $selectedField->id)
                    ->whereBetween('date', [$weekStart->toDateString(), $weekStart->copy()->addDays(6)->toDateString()])
                    ->get()
                : collect();

            // Mapa "ancho" para week view: "YYYY-MM-DD|HH:MM" => ['type','data','is_start','span']
            $weekCellMap = [];
            $sm = $selectedField ? ($selectedField->slot_minutes ?: 60) : 60;
            foreach ($reservations as $r) {
                $cur = $r->start_at->copy();
                $end = $r->end_at;
                $count = 0;
                $totalSpan = (int) ceil($cur->diffInMinutes($end) / max($sm, 1));
                while ($cur < $end) {
                    $key = $cur->format('Y-m-d|H:i');
                    $weekCellMap[$key] = [
                        'type' => 'reservation', 'data' => $r,
                        'is_start' => $count === 0, 'span' => $totalSpan,
                    ];
                    $cur->addMinutes($sm);
                    $count++;
                }
            }
            foreach ($weekBlocks as $b) {
                $blockStart = Carbon::parse(($b->date instanceof Carbon ? $b->date->toDateString() : (string) $b->date) . ' ' . $b->start_time);
                $blockEnd   = Carbon::parse(($b->date instanceof Carbon ? $b->date->toDateString() : (string) $b->date) . ' ' . $b->end_time);
                $cur = $blockStart->copy();
                $count = 0;
                $totalSpan = (int) ceil($cur->diffInMinutes($blockEnd) / max($sm, 1));
                while ($cur < $blockEnd) {
                    $key = $cur->format('Y-m-d|H:i');
                    if (!isset($weekCellMap[$key])) {
                        $weekCellMap[$key] = [
                            'type' => 'block', 'data' => $b,
                            'is_start' => $count === 0, 'span' => $totalSpan,
                        ];
                    }
                    $cur->addMinutes($sm);
                    $count++;
                }
            }

            // Mapa legacy (vista vieja): "YYYY-MM-DD|HH:MM" => reservation
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

            // Vars vacías que el día usa pero la semana no necesita
            $blocks = collect(); $cellMap = []; $fieldOpenClose = [];

            return view('va.reservations.agenda', compact(
                'date', 'fields', 'reservations', 'reservationMap', 'fieldId',
                'slots', 'weekDays', 'weekStart', 'selectedField', 'activeSlotsPerDay', 'view',
                'blocks', 'cellMap', 'fieldOpenClose', 'weekCellMap', 'weekBlocks'
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
            ->whereIn('status', ['PAID', 'PENDING_CASH', 'PENDING_PAYMENT'])
            // Filtrar PENDING_PAYMENT ya expirados (no se muestran como ocupados)
            ->where(function ($q) {
                $q->whereIn('status', ['PAID', 'PENDING_CASH'])
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'PENDING_PAYMENT')
                         ->whereNotNull('expires_at')
                         ->where('expires_at', '>', now());
                  });
            })
            ->with(['user', 'field'])
            ->get();

        // Mapa por start_at exacto: "field_id|HH:MM" => reservation (vista vieja)
        $reservationMap = [];
        foreach ($reservations as $reservation) {
            $key = $reservation->field_id . '|' . $reservation->start_at->format('H:i');
            $reservationMap[$key] = $reservation;
        }

        // ── Bloques del día (para nuevo grid ATC admin) ───────────────────────
        $fieldIds = $fields->pluck('id')->all();
        $blocks = \App\Models\FieldBlock::query()
            ->whereIn('field_id', $fieldIds)
            ->whereDate('date', $date->toDateString())
            ->get();

        // Generar slots desde los horarios reales de cada cancha (+ excepciones)
        $slotSet = [];
        $fieldOpenClose = []; // field_id => ['open' => Carbon, 'close' => Carbon] para hoy

        foreach ($fields as $field) {
            $exception = $field->exceptions->first(fn ($e) => $e->date->toDateString() === $date->toDateString());
            if ($exception?->is_closed) {
                continue;
            }
            $openTime  = $exception?->open_time  ?? optional($field->schedules->firstWhere('day_of_week', $dayOfWeek))->open_time;
            $closeTime = $exception?->close_time ?? optional($field->schedules->firstWhere('day_of_week', $dayOfWeek))->close_time;
            if (!$openTime || !$closeTime) continue;

            $slotMinutes = $field->slot_minutes ?: 60;
            $current     = Carbon::parse($date->toDateString() . ' ' . $openTime);
            $close       = Carbon::parse($date->toDateString() . ' ' . $closeTime);
            $fieldOpenClose[$field->id] = ['open' => $current->copy(), 'close' => $close->copy()];

            while ($current < $close) {
                $slotSet[$current->format('H:i')] = true;
                $current->addMinutes($slotMinutes);
            }
        }

        // Forzar slots de reservas existentes (defensivo, por si una reserva quedó fuera del schedule)
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

        // ── Mapa "ancho" para el grid admin: cubre TODOS los slots que ocupa cada reserva ──
        // Una reserva 21:00→22:30 ocupa 3 slots de 30min (21:00, 21:30, 22:00).
        // Marcamos cada slot ocupado, y separamos "isStart" para mostrar la card sólo en el primer slot.
        $cellMap = []; // "field_id|HH:MM" => ['type' => 'reservation|block', 'data' => ..., 'is_start' => bool, 'span' => N]
        foreach ($reservations as $res) {
            $field = $fields->firstWhere('id', $res->field_id);
            if (!$field) continue;
            $slotMinutes = $field->slot_minutes ?: 60;
            $cur = $res->start_at->copy();
            $end = $res->end_at;
            $count = 0;
            $totalSpan = (int) ceil($cur->diffInMinutes($end) / max($slotMinutes, 1));
            while ($cur < $end) {
                $key = $res->field_id . '|' . $cur->format('H:i');
                $cellMap[$key] = [
                    'type'     => 'reservation',
                    'data'     => $res,
                    'is_start' => $count === 0,
                    'span'     => $totalSpan,
                ];
                $cur->addMinutes($slotMinutes);
                $count++;
            }
        }
        foreach ($blocks as $block) {
            $field = $fields->firstWhere('id', $block->field_id);
            if (!$field) continue;
            $slotMinutes = $field->slot_minutes ?: 60;
            $blockStart = Carbon::parse($date->toDateString() . ' ' . $block->start_time);
            $blockEnd   = Carbon::parse($date->toDateString() . ' ' . $block->end_time);
            $cur = $blockStart->copy();
            $count = 0;
            $totalSpan = (int) ceil($cur->diffInMinutes($blockEnd) / max($slotMinutes, 1));
            while ($cur < $blockEnd) {
                $key = $block->field_id . '|' . $cur->format('H:i');
                if (!isset($cellMap[$key])) { // reserva pisa al bloqueo
                    $cellMap[$key] = [
                        'type'     => 'block',
                        'data'     => $block,
                        'is_start' => $count === 0,
                        'span'     => $totalSpan,
                    ];
                }
                $cur->addMinutes($slotMinutes);
                $count++;
            }
        }

        $weekDays          = null;
        $weekStart         = null;
        $selectedField     = null;
        $activeSlotsPerDay = [];

        $weekCellMap = []; $weekBlocks = collect();

        return view('va.reservations.agenda', compact(
            'date', 'fields', 'reservations', 'reservationMap', 'fieldId',
            'slots', 'weekDays', 'weekStart', 'selectedField', 'activeSlotsPerDay', 'view',
            'blocks', 'cellMap', 'fieldOpenClose', 'weekCellMap', 'weekBlocks'
        ));
    }

    public function confirmCash(Request $request, Reservation $reservation)
    {
        $user = $request->user();

        $reservation->load('field.venue');

        if ($user->role !== 'super_admin' && $reservation->field->venue->owner_user_id !== $user->id && !$user->isStaffOf($reservation->field->venue->id)) {
            abort(403);
        }

        if ($user->isVenueStaff()) {
            abort_if(!$user->hasStaffPermission('view_reservations', $reservation->field->venue->id), 403);
        }

        if ($reservation->status !== 'PENDING_CASH') {
            return back()->with('error', 'Esta reserva no tiene pago en efectivo pendiente.');
        }

        $reservation->update([
            'status'         => 'PAID',
            'payment_status' => 'approved',
            'notes'          => trim(($reservation->notes ? $reservation->notes . ' | ' : '') . 'Pago en efectivo confirmado por ' . $user->name . ' el ' . now()->format('d/m/Y H:i')),
        ]);

        return back()->with('success', 'Pago en efectivo confirmado. La reserva ahora está pagada.');
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

        broadcast(new \App\Events\FieldAvailabilityChanged(
            $reservation->field_id,
            $reservation->start_at->toDateString()
        ));

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