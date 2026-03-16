<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $today = now()->startOfDay();
        $tomorrow = $today->copy()->addDay();
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $startOfPrevWeek = now()->subWeek()->startOfWeek();
        $endOfPrevWeek = now()->subWeek()->endOfWeek();
        $startOfPrevMonth = now()->subMonth()->startOfMonth();
        $endOfPrevMonth = now()->subMonth()->endOfMonth();

        $membershipAlert = null;

        if ($user->role === 'venue_admin') {
            $activeSubscription = $user->activeVenueAdminSubscription()->first();

            if ($activeSubscription && $activeSubscription->expires_at) {
                $daysRemaining = (int) ceil(now()->diffInSeconds($activeSubscription->expires_at, false) / 86400);

                if ($daysRemaining <= 7) {
                    $membershipAlert = [
                        'expires_at' => $activeSubscription->expires_at,
                        'days_remaining' => $daysRemaining,
                        'tone' => $daysRemaining <= 1 ? 'danger' : 'warning',
                    ];
                }
            }
        }

        $venuesQuery = Venue::accessibleBy($user);

        $venues = $venuesQuery
            ->with(['fields.price', 'fields.schedules', 'fields.exceptions'])
            ->orderBy('name')
            ->get();

        $venueIds = $venues->pluck('id');
        $fields = $venues->flatMap(fn ($v) => $v->fields)->values();
        $fieldIds = $fields->pluck('id');

        $reservationsToday = Reservation::query()
            ->whereIn('field_id', $fieldIds)
            ->where('start_at', '>=', $today)
            ->where('start_at', '<', $tomorrow)
            ->with(['field.venue', 'user'])
            ->orderBy('start_at')
            ->get();

        $stats = [
            'venues_count' => $venues->count(),

            'fields_count' => Field::query()
                ->whereIn('venue_id', $venueIds)
                ->count(),

            'active_fields_count' => Field::query()
                ->whereIn('venue_id', $venueIds)
                ->where('is_active', true)
                ->count(),

            'reservations_today_count' => $reservationsToday->count(),

            'paid_today_count' => $reservationsToday
                ->where('status', 'PAID')
                ->count(),

            'pending_today_count' => $reservationsToday
                ->where('status', 'PENDING_PAYMENT')
                ->count(),

            'today_revenue' => $reservationsToday
                ->where('status', 'PAID')
                ->sum('total_amount'),
        ];

        $nextReservation = Reservation::query()
            ->whereIn('field_id', $fieldIds)
            ->where('start_at', '>=', now())
            ->whereIn('status', ['PENDING_PAYMENT', 'PAID'])
            ->with(['field.venue', 'user'])
            ->orderBy('start_at')
            ->first();

        $validRevenueStatuses = ['PAID'];
        $visibleStatuses = ['PAID', 'PENDING_PAYMENT'];

        $reservationsWeek = Reservation::query()
            ->whereIn('field_id', $fieldIds)
            ->whereBetween('start_at', [$startOfWeek, $endOfWeek])
            ->whereIn('status', $visibleStatuses)
            ->count();

        $revenueToday = (float) Reservation::query()
            ->whereIn('field_id', $fieldIds)
            ->whereBetween('start_at', [$today, $tomorrow])
            ->whereIn('status', $validRevenueStatuses)
            ->sum('total_amount');

        $revenueWeek = (float) Reservation::query()
            ->whereIn('field_id', $fieldIds)
            ->whereBetween('start_at', [$startOfWeek, $endOfWeek])
            ->whereIn('status', $validRevenueStatuses)
            ->sum('total_amount');

        $revenueMonth = (float) Reservation::query()
            ->whereIn('field_id', $fieldIds)
            ->whereBetween('start_at', [$startOfMonth, $endOfMonth])
            ->whereIn('status', $validRevenueStatuses)
            ->sum('total_amount');

        $reservationsWeekPrev = Reservation::query()
            ->whereIn('field_id', $fieldIds)
            ->whereBetween('start_at', [$startOfPrevWeek, $endOfPrevWeek])
            ->whereIn('status', $visibleStatuses)
            ->count();

        $revenueWeekPrev = (float) Reservation::query()
            ->whereIn('field_id', $fieldIds)
            ->whereBetween('start_at', [$startOfPrevWeek, $endOfPrevWeek])
            ->whereIn('status', $validRevenueStatuses)
            ->sum('total_amount');

        $reservationsMonthCurrent = Reservation::query()
            ->whereIn('field_id', $fieldIds)
            ->whereBetween('start_at', [$startOfMonth, $endOfMonth])
            ->whereIn('status', $visibleStatuses)
            ->count();

        $reservationsMonthPrev = Reservation::query()
            ->whereIn('field_id', $fieldIds)
            ->whereBetween('start_at', [$startOfPrevMonth, $endOfPrevMonth])
            ->whereIn('status', $visibleStatuses)
            ->count();

        $revenueMonthPrev = (float) Reservation::query()
            ->whereIn('field_id', $fieldIds)
            ->whereBetween('start_at', [$startOfPrevMonth, $endOfPrevMonth])
            ->whereIn('status', $validRevenueStatuses)
            ->sum('total_amount');

        $topField = Reservation::query()
            ->select('field_id', DB::raw('COUNT(*) as reservations_count'))
            ->whereIn('field_id', $fieldIds)
            ->whereBetween('start_at', [$startOfWeek, $endOfWeek])
            ->whereIn('status', $visibleStatuses)
            ->groupBy('field_id')
            ->orderByDesc('reservations_count')
            ->with('field.venue')
            ->first();

        $upcomingToday = Reservation::query()
            ->whereIn('field_id', $fieldIds)
            ->whereBetween('start_at', [$today, $tomorrow])
            ->whereIn('status', $visibleStatuses)
            ->with(['field.venue', 'user'])
            ->orderBy('start_at')
            ->take(10)
            ->get();

        $dow = (int) $today->dayOfWeek;
        $fieldOccupancy = [];

        foreach ($fields as $field) {
            $exception = $field->exceptions
                ->first(fn ($e) => Carbon::parse($e->date)->toDateString() === $today->toDateString());

            $isClosed = $exception?->is_closed ?? false;

            $schedule = $field->schedules
                ->firstWhere('day_of_week', $dow);

            $openTime = $exception?->open_time ?? $schedule?->open_time ?? null;
            $closeTime = $exception?->close_time ?? $schedule?->close_time ?? null;

            $totalSlots = 0;

            if (!$isClosed && $openTime && $closeTime) {
                $open = Carbon::parse($today->toDateString() . ' ' . $openTime);
                $close = Carbon::parse($today->toDateString() . ' ' . $closeTime);

                $slotMinutes = (int) $field->slot_minutes;

                for ($t = $open->copy(); $t->lt($close); $t->addMinutes($slotMinutes)) {
                    $slotEnd = $t->copy()->addMinutes($slotMinutes);

                    if ($slotEnd->gt($close)) {
                        break;
                    }

                    $totalSlots++;
                }
            }

            $reservedSlots = $reservationsToday
                ->where('field_id', $field->id)
                ->whereIn('status', $visibleStatuses)
                ->count();

            $occupancyPercent = $totalSlots > 0
                ? round(($reservedSlots / $totalSlots) * 100)
                : 0;

            $fieldOccupancy[] = [
                'field' => $field,
                'total_slots' => $totalSlots,
                'reserved_slots' => $reservedSlots,
                'occupancy_percent' => $occupancyPercent,
            ];
        }

        $superStats = null;
        $recentUsers = collect();
        $topVenue = null;

        if ($user->role === 'super_admin') {
            $superStats = [
                'users_count' => User::count(),
                'venue_admins_count' => User::where('role', 'venue_admin')->count(),
                'super_admins_count' => User::where('role', 'super_admin')->count(),
                'venues_count' => Venue::count(),
                'fields_count' => Field::count(),
                'reservations_today_count' => Reservation::query()
                    ->whereBetween('start_at', [$today, $tomorrow])
                    ->whereIn('status', $visibleStatuses)
                    ->count(),
                'revenue_today' => (float) Reservation::query()
                    ->whereBetween('start_at', [$today, $tomorrow])
                    ->whereIn('status', $validRevenueStatuses)
                    ->sum('total_amount'),
                'reservations_week_count' => Reservation::query()
                    ->whereBetween('start_at', [$startOfWeek, $endOfWeek])
                    ->whereIn('status', $visibleStatuses)
                    ->count(),
                'revenue_week' => (float) Reservation::query()
                    ->whereBetween('start_at', [$startOfWeek, $endOfWeek])
                    ->whereIn('status', $validRevenueStatuses)
                    ->sum('total_amount'),
            ];

            $recentUsers = User::query()
                ->latest()
                ->take(8)
                ->get();

            $topVenue = Reservation::query()
                ->join('fields', 'fields.id', '=', 'reservations.field_id')
                ->join('venues', 'venues.id', '=', 'fields.venue_id')
                ->select(
                    'venues.id as venue_id',
                    'venues.name as venue_name',
                    DB::raw('COUNT(reservations.id) as reservations_count')
                )
                ->whereBetween('reservations.start_at', [$startOfWeek, $endOfWeek])
                ->whereIn('reservations.status', $visibleStatuses)
                ->groupBy('venues.id', 'venues.name')
                ->orderByDesc('reservations_count')
                ->first();
        }

        return view('va.dashboard', compact(
            'venues',
            'stats',
            'reservationsToday',
            'nextReservation',
            'fieldOccupancy',
            'reservationsWeek',
            'revenueToday',
            'revenueWeek',
            'revenueMonth',
            'reservationsWeekPrev',
            'revenueWeekPrev',
            'reservationsMonthCurrent',
            'reservationsMonthPrev',
            'revenueMonthPrev',
            'topField',
            'upcomingToday',
            'superStats',
            'recentUsers',
            'topVenue',
            'membershipAlert'
        ));
    }
}