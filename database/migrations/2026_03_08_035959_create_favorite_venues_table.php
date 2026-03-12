<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;

class FavoriteVenueController extends Controller
{
    public function store(Request $request, Venue $venue)
    {
        $request->user()->favoriteVenues()->syncWithoutDetaching([$venue->id]);

        return back()->with('success', 'Complejo agregado a favoritos.');
    }

    public function destroy(Request $request, Venue $venue)
    {
        $request->user()->favoriteVenues()->detach($venue->id);

        return back()->with('success', 'Complejo quitado de favoritos.');
    }
}
