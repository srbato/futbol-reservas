<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\TournamentSetting;
use App\Models\Venue;
use Illuminate\Http\Request;

class TournamentSettingController extends Controller
{
    public function index()
    {
        $venue = Venue::accessibleBy(auth()->user())->with('fields.tournamentSetting')->first();
        abort_if(!$venue, 404);

        return view('va.tournament-settings.index', compact('venue'));
    }

    public function update(Request $request, Field $field)
    {
        // Verify field belongs to user's venue
        $venue = Venue::accessibleBy(auth()->user())->first();
        abort_if(!$venue || $field->venue_id !== $venue->id, 403);

        $data = $request->validate([
            'tournament_enabled' => 'required|boolean',
            'tournament_price_per_match' => 'nullable|numeric|min:0',
            'available_days' => 'nullable|array',
            'available_days.*' => 'integer|min:0|max:6',
            'available_start_time' => 'nullable|date_format:H:i',
            'available_end_time' => 'nullable|date_format:H:i',
            'contact_method' => 'required|in:whatsapp,phone,email,internal',
            'contact_value' => 'nullable|string|max:150',
            'auto_approve' => 'required|boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        TournamentSetting::updateOrCreate(
            ['field_id' => $field->id],
            $data
        );

        return back()->with('success', "Configuracion de torneos actualizada para {$field->name}.");
    }
}
