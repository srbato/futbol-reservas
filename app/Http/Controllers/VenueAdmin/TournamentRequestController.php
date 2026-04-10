<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\TournamentVenueRequest;
use App\Models\Venue;
use App\Services\TournamentVenueService;
use Illuminate\Http\Request;

class TournamentRequestController extends Controller
{
    public function index()
    {
        $venue = Venue::accessibleBy(auth()->user())->first();
        abort_if(!$venue, 404);

        $requests = TournamentVenueRequest::where('venue_id', $venue->id)
            ->with(['tournament.organizer', 'field.tournamentSetting', 'messages.user'])
            ->latest()
            ->paginate(20);

        return view('va.tournament-requests.index', compact('venue', 'requests'));
    }

    public function approve(Request $request, TournamentVenueRequest $tournamentRequest)
    {
        $venue = Venue::accessibleBy(auth()->user())->first();
        abort_if(!$venue || $tournamentRequest->venue_id !== $venue->id, 403);
        abort_if(!$tournamentRequest->isPending(), 422, 'Esta solicitud ya fue procesada.');

        $service = app(TournamentVenueService::class);
        $service->approveRequest($tournamentRequest, $request->input('response_message'));

        return back()->with('success', 'Solicitud aprobada. El torneo puede usar tu cancha.');
    }

    public function reject(Request $request, TournamentVenueRequest $tournamentRequest)
    {
        $venue = Venue::accessibleBy(auth()->user())->first();
        abort_if(!$venue || $tournamentRequest->venue_id !== $venue->id, 403);
        abort_if(!$tournamentRequest->isPending(), 422);

        $service = app(TournamentVenueService::class);
        $service->rejectRequest($tournamentRequest, $request->input('response_message'));

        return back()->with('success', 'Solicitud rechazada.');
    }
}
