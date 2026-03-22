<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\ReservationBatch;
use Illuminate\Http\Request;

class MyReservationsController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // Single reservations (no batch)
        $reservations = Reservation::query()
            ->where('user_id', $userId)
            ->whereNull('batch_id')
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

        $misPartidos = \App\Models\FaltaUnoGame::with(['field.venue', 'activeParticipants'])
            ->where(function($q) use ($userId) {
                $q->where('initiator_user_id', $userId)
                  ->orWhereHas('participants', fn($q2) => $q2->where('user_id', $userId)->where('status', 'confirmed'));
            })
            ->whereIn('status', ['open', 'full', 'expired', 'cancelled'])
            ->orderByDesc('start_at')
            ->limit(20)
            ->get();

        return view('reservations.my', compact('reservations', 'batches', 'misPartidos'));
    }
}
