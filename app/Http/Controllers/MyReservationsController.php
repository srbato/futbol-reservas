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
            ->with(['field.venue' => fn($q) => $q->select('id', 'name', 'owner_user_id', 'cancellation_hours')])
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

        return view('reservations.my', compact('reservations', 'batches', 'misPartidos', 'recurringSubscriptions', 'pendingRatingsCount', 'pendingRatingGames'));
    }
}
