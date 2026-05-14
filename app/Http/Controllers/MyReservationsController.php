<?php

namespace App\Http\Controllers;

use App\Models\FaltaUnoRating;
use App\Models\Reservation;
use App\Models\ReservationBatch;
use App\Models\RecurringSubscription;
use Illuminate\Http\Request;

class MyReservationsController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // Single reservations (no batch, no subscription, no falta uno)
        $reservations = Reservation::query()
            ->where('user_id', $userId)
            ->whereNull('batch_id')
            ->whereNull('recurring_subscription_id')
            ->whereDoesntHave('faltaUnoGame')
            ->with(['field.venue' => fn($q) => $q->select('id', 'name', 'address', 'lat', 'lng', 'owner_user_id', 'cancellation_hours', 'zone')])
            ->orderByDesc('start_at')
            ->get();

        // Recurring batches with their reservations
        // Only keep batches that have at least one non-dead reservation
        $batches = ReservationBatch::query()
            ->where('user_id', $userId)
            ->with(['field.venue', 'reservations' => fn($q) => $q->orderBy('start_at')])
            ->orderByDesc('id')
            ->get()
            ->filter(fn($b) => $b->reservations->whereNotIn('status', ['CANCELLED', 'EXPIRED'])->isNotEmpty())
            ->values();

        $misPartidos = \App\Models\FaltaUnoGame::with(['field.venue', 'activeParticipants', 'reservation', 'participants' => fn($q) => $q->where('user_id', $userId)])
            ->where(function($q) use ($userId) {
                $q->where('initiator_user_id', $userId)
                  ->orWhereHas('participants', fn($q2) => $q2->where('user_id', $userId)->where('status', 'confirmed'));
            })
            ->whereIn('status', ['open', 'full', 'finished', 'expired', 'cancelled'])
            ->orderByDesc('start_at')
            ->limit(20)
            ->get();

        $recurringSubscriptions = RecurringSubscription::where('user_id', $userId)
            ->with(['field.venue', 'reservations' => fn($q) => $q->orderBy('start_at')])
            ->orderByDesc('created_at')
            ->get();

        // Partidos pasados sin calificar (excluir cancelados y expirados)
        $ratedGameIds = FaltaUnoRating::where('rater_user_id', $userId)->pluck('game_id');
        $pendingRatingGames = $misPartidos
            ->filter(fn($g) => $g->start_at->isPast()
                && !in_array($g->status, ['cancelled', 'expired'])
                && !$ratedGameIds->contains($g->id))
            ->values();
        $pendingRatingsCount = $pendingRatingGames->count();

        // ── Stats para la cabecera ────────────────────────────────────────
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();

        $upcomingReservations = $reservations->filter(fn($r) =>
            in_array($r->status, ['PAID', 'PENDING_CASH']) && $r->start_at->isFuture()
        );
        $upcomingCount = $upcomingReservations->count();
        $nextReservation = $upcomingReservations->sortBy('start_at')->first();

        // Recurring subs activas
        $activeSubsCount = $recurringSubscriptions->where('status', 'ACTIVE')->count();
        $nextActiveSub   = $recurringSubscriptions->where('status', 'ACTIVE')->first();

        // Falta Uno este mes (jugados, finished, no cancelados)
        $thisMonthFu = $misPartidos->filter(fn($g) =>
            $g->start_at->isPast() &&
            $g->start_at->gte($startOfMonth) &&
            !in_array($g->status, ['cancelled', 'expired'])
        );
        $fuMonthCount = $thisMonthFu->count();
        $fuMonthWins  = 0; $fuMonthLosses = 0; $fuMonthDraws = 0;
        foreach ($thisMonthFu as $g) {
            $myParticipation = $g->participants->firstWhere('user_id', $userId);
            if ($myParticipation && $myParticipation->result) {
                if ($myParticipation->result === 'win')  $fuMonthWins++;
                if ($myParticipation->result === 'loss') $fuMonthLosses++;
                if ($myParticipation->result === 'draw') $fuMonthDraws++;
            }
        }

        // Total horas jugadas (todas las reservas pagadas + FU finished participados)
        $totalMinutes = 0;
        foreach (Reservation::where('user_id', $userId)->whereIn('status', ['PAID'])->get(['start_at','end_at']) as $r) {
            $totalMinutes += $r->start_at->diffInMinutes($r->end_at);
        }
        $totalHoursPlayed = round($totalMinutes / 60);
        $memberSince = $request->user()->created_at;

        // Ranking del usuario en su deporte principal (si tiene perfil)
        $mainSportProfile = $request->user()->faltaUnoSportProfiles()->orderByDesc('games_played')->first();
        $userRanking = $mainSportProfile?->category;

        // Métricas del nivel (Constancia, Puntualidad, Fair play)
        $levelMetrics = null;
        if ($mainSportProfile) {
            // Puntualidad = attendance_rate (% confirmed sin late_leave)
            $puntualidad = (float) $mainSportProfile->attendance_rate;
            // Fair play = average_rating × 20 (5 estrellas = 100%)
            $fairPlay = round(((float) $mainSportProfile->average_rating) * 20, 1);
            // Constancia = % de partidos completados de los anotados
            $totalParticipations = \App\Models\FaltaUnoParticipant::where('user_id', $userId)->count();
            $playedParticipations = \App\Models\FaltaUnoParticipant::where('user_id', $userId)
                ->where('status', 'confirmed')
                ->where('is_late_leave', false)
                ->whereHas('game', fn($q) => $q->where('start_at', '<', now())->whereNotIn('status', ['cancelled','expired']))
                ->count();
            $constancia = $totalParticipations > 0 ? round(($playedParticipations / $totalParticipations) * 100, 1) : 100.0;

            $levelMetrics = [
                'sport'       => $mainSportProfile->sport,
                'constancia'  => $constancia,
                'puntualidad' => $puntualidad,
                'fair_play'   => $fairPlay,
            ];
        }

        // Esta semana — reservas + FU del usuario agrupadas por día
        $weekStart = $now->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
        $weekDays = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $weekStart->copy()->addDays($i);
            $count = $reservations->filter(fn($r) => $r->start_at->isSameDay($day) && in_array($r->status, ['PAID','PENDING_CASH','PENDING_PAYMENT']))->count();
            $count += $misPartidos->filter(fn($g) => $g->start_at->isSameDay($day) && !in_array($g->status, ['cancelled','expired']))->count();
            $weekDays[] = [
                'date'     => $day,
                'isToday'  => $day->isToday(),
                'count'    => $count,
            ];
        }

        return view('reservations.my', compact(
            'reservations', 'batches', 'misPartidos', 'recurringSubscriptions',
            'pendingRatingsCount', 'pendingRatingGames',
            'upcomingCount', 'nextReservation',
            'activeSubsCount', 'nextActiveSub',
            'fuMonthCount', 'fuMonthWins', 'fuMonthLosses', 'fuMonthDraws',
            'totalHoursPlayed', 'memberSince', 'userRanking',
            'levelMetrics', 'weekDays'
        ));
    }
}
