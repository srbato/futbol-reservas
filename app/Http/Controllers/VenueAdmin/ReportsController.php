<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $fieldId = $request->query('field_id');
        $from = $request->query('from')
            ? Carbon::parse($request->query('from'))->startOfDay()
            : Carbon::today()->subDays(6)->startOfDay();

        $to = $request->query('to')
            ? Carbon::parse($request->query('to'))->endOfDay()
            : Carbon::today()->endOfDay();

        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $monthStart = Carbon::now()->startOfMonth();

        $fields = Field::query()
            ->whereHas('venue', function ($q) use ($user) {
                $q->where('owner_user_id', $user->id);
            })
            ->with(['venue', 'schedules', 'exceptions'])
            ->orderBy('name')
            ->get();

        $filteredFields = $fieldId
            ? $fields->where('id', (int) $fieldId)->values()
            : $fields->values();

        $baseQuery = Reservation::query()
            ->whereHas('field.venue', function ($q) use ($user) {
                $q->where('owner_user_id', $user->id);
            })
            ->where('status', 'PAID')
            ->when($fieldId, fn ($q) => $q->where('field_id', $fieldId));

        $todayRevenue = (clone $baseQuery)
            ->whereDate('start_at', $today)
            ->sum('total_amount');

        $weekRevenue = (clone $baseQuery)
            ->where('start_at', '>=', $weekStart)
            ->sum('total_amount');

        $monthRevenue = (clone $baseQuery)
            ->where('start_at', '>=', $monthStart)
            ->sum('total_amount');

        $todayReservations = (clone $baseQuery)
            ->whereDate('start_at', $today)
            ->count();

        $weekReservations = (clone $baseQuery)
            ->where('start_at', '>=', $weekStart)
            ->count();

        $monthReservations = (clone $baseQuery)
            ->where('start_at', '>=', $monthStart)
            ->count();

        $labels = [];
        $reservationsPerDay = [];
        $revenuePerDay = [];

        $cursor = $from->copy()->startOfDay();

        while ($cursor->lte($to)) {
            $nextDay = $cursor->copy()->addDay();

            $labels[] = $cursor->format('d/m');

            $reservationsPerDay[] = (clone $baseQuery)
                ->where('start_at', '>=', $cursor)
                ->where('start_at', '<', $nextDay)
                ->count();

            $revenuePerDay[] = (clone $baseQuery)
                ->where('start_at', '>=', $cursor)
                ->where('start_at', '<', $nextDay)
                ->sum('total_amount');

            $cursor->addDay();
        }

        // Ocupación por cancha en el rango filtrado
        $paidReservationsInRange = (clone $baseQuery)
            ->whereBetween('start_at', [$from, $to])
            ->get(['field_id', 'start_at']);

        $fieldOccupancy = [];

        foreach ($filteredFields as $field) {
            $totalSlots = 0;
            $dayCursor = $from->copy()->startOfDay();

            while ($dayCursor->lte($to)) {
                $dateString = $dayCursor->toDateString();
                $dow = (int) $dayCursor->dayOfWeek;

                $exception = $field->exceptions
                    ->first(fn ($e) => Carbon::parse($e->date)->toDateString() === $dateString);

                $isClosed = $exception?->is_closed ?? false;

                $schedule = $field->schedules
                    ->firstWhere('day_of_week', $dow);

                $openTime = $exception?->open_time ?? $schedule?->open_time ?? null;
                $closeTime = $exception?->close_time ?? $schedule?->close_time ?? null;

                if (!$isClosed && $openTime && $closeTime) {
                    $open = Carbon::parse($dateString . ' ' . $openTime);
                    $close = Carbon::parse($dateString . ' ' . $closeTime);
                    $slotMinutes = (int) $field->slot_minutes;

                    for ($t = $open->copy(); $t->lt($close); $t->addMinutes($slotMinutes)) {
                        $slotEnd = $t->copy()->addMinutes($slotMinutes);
                        if ($slotEnd->gt($close)) {
                            break;
                        }
                        $totalSlots++;
                    }
                }

                $dayCursor->addDay();
            }

            $reservedSlots = $paidReservationsInRange
                ->where('field_id', $field->id)
                ->count();

            $occupancyPercent = $totalSlots > 0
                ? round(($reservedSlots / $totalSlots) * 100)
                : 0;

            $fieldRevenue = Reservation::query()
                ->where('field_id', $field->id)
                ->where('status', 'PAID')
                ->whereBetween('start_at', [$from, $to])
                ->sum('total_amount');

            $fieldOccupancy[] = [
                'field'             => $field,
                'reserved_slots'    => $reservedSlots,
                'total_slots'       => $totalSlots,
                'occupancy_percent' => $occupancyPercent,
                'revenue'           => $fieldRevenue,
            ];
        }

        return view('va.reports.index', compact(
            'todayRevenue',
            'weekRevenue',
            'monthRevenue',
            'todayReservations',
            'weekReservations',
            'monthReservations',
            'labels',
            'reservationsPerDay',
            'revenuePerDay',
            'fields',
            'fieldId',
            'from',
            'to',
            'fieldOccupancy'
        ));
    }

    public function export(Request $request)
    {
        $user = $request->user();

        $fieldId = $request->query('field_id');
        $from = $request->query('from')
            ? Carbon::parse($request->query('from'))->startOfDay()
            : Carbon::today()->subDays(6)->startOfDay();

        $to = $request->query('to')
            ? Carbon::parse($request->query('to'))->endOfDay()
            : Carbon::today()->endOfDay();

        $reservations = Reservation::query()
            ->whereHas('field.venue', function ($q) use ($user) {
                $q->where('owner_user_id', $user->id);
            })
            ->where('status', 'PAID')
            ->when($fieldId, fn ($q) => $q->where('field_id', $fieldId))
            ->whereBetween('start_at', [$from, $to])
            ->with(['field.venue', 'user'])
            ->orderBy('start_at')
            ->get();

        $filename = 'reservas_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate',
        ];

        $columns = ['Fecha', 'Hora', 'Complejo', 'Cancha', 'Usuario', 'Monto'];

        $callback = function () use ($reservations, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($reservations as $r) {
                fputcsv($file, [
                    $r->start_at->format('d/m/Y'),
                    $r->start_at->format('H:i'),
                    $r->field->venue->name,
                    $r->field->name,
                    $r->user->name ?? '',
                    $r->total_amount,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

