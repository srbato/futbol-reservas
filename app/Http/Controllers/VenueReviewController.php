<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Models\VenueReview;
use Illuminate\Http\Request;

class VenueReviewController extends Controller
{
    public function store(Request $request, Venue $venue)
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        VenueReview::create([
            'venue_id' => $venue->id,
            'user_id' => $request->user()->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        return redirect()
            ->route('venues.show', $venue)
            ->with('success', 'Tu reseña fue publicada.');
    }
}
