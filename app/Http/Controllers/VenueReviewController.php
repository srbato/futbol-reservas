<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Models\VenueReview;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;

class VenueReviewController extends Controller
{
    public function store(Request $request, Venue $venue)
    {
        $user = $request->user();

        // Verificar que el usuario haya tenido una reserva pagada o con check-in en este complejo
        $hasReservation = $venue->reservations()
            ->where('user_id', $user->id)
            ->whereIn('status', ['PAID'])
            ->where('start_at', '<', now())
            ->exists();

        if (!$hasReservation) {
            return redirect()
                ->route('venues.show', $venue)
                ->with('error', 'Solo podés dejar una reseña si reservaste en este complejo.');
        }

        // Verificar que no haya dejado ya una reseña
        $alreadyReviewed = VenueReview::where('venue_id', $venue->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyReviewed) {
            return redirect()
                ->route('venues.show', $venue)
                ->with('error', 'Ya dejaste una reseña para este complejo.');
        }

        $data = $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            VenueReview::create([
                'venue_id' => $venue->id,
                'user_id'  => $user->id,
                'rating'   => $data['rating'],
                'comment'  => $data['comment'] ?? null,
            ]);
        } catch (UniqueConstraintViolationException) {
            return redirect()
                ->route('venues.show', $venue)
                ->with('error', 'Ya dejaste una reseña para este complejo.');
        }

        return redirect()
            ->route('venues.show', $venue)
            ->with('success', 'Tu reseña fue publicada.');
    }
}
