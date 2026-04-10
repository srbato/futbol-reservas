<?php

namespace App\Http\Controllers;

use App\Events\TournamentChatMessageSent;
use App\Models\TournamentRequestMessage;
use App\Models\TournamentVenueRequest;
use App\Models\Venue;
use Illuminate\Http\Request;

class TournamentRequestChatController extends Controller
{
    public function storeOrganizer(Request $request, TournamentVenueRequest $tournamentRequest)
    {
        $tournament = $tournamentRequest->tournament;
        abort_if(!$tournament || !$tournament->isOrganizer(auth()->user()), 403);
        abort_if(!$tournamentRequest->isPending(), 422, 'Esta solicitud ya fue procesada.');

        $request->validate(['message' => 'required|string|max:1000']);

        $msg = TournamentRequestMessage::create([
            'venue_request_id' => $tournamentRequest->id,
            'user_id' => auth()->id(),
            'message' => $request->input('message'),
        ]);

        // Mark as read for organizer (they just sent a message, so they've seen everything)
        $tournamentRequest->update(['organizer_last_read_at' => now()]);

        broadcast(new TournamentChatMessageSent($msg));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'id' => $msg->id,
                'message' => $msg->message,
                'user_name' => auth()->user()->name,
                'created_at' => 'ahora',
                'is_mine' => true,
            ]);
        }

        return back()->with('success', 'Mensaje enviado.');
    }

    public function storeVenueAdmin(Request $request, TournamentVenueRequest $tournamentRequest)
    {
        $venue = Venue::accessibleBy(auth()->user())->first();
        abort_if(!$venue || $tournamentRequest->venue_id !== $venue->id, 403);
        abort_if(!$tournamentRequest->isPending(), 422, 'Esta solicitud ya fue procesada.');

        $request->validate(['message' => 'required|string|max:1000']);

        $msg = TournamentRequestMessage::create([
            'venue_request_id' => $tournamentRequest->id,
            'user_id' => auth()->id(),
            'message' => $request->input('message'),
        ]);

        // Mark as read for admin
        $tournamentRequest->update(['admin_last_read_at' => now()]);

        broadcast(new TournamentChatMessageSent($msg));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'id' => $msg->id,
                'message' => $msg->message,
                'user_name' => auth()->user()->name,
                'created_at' => 'ahora',
                'is_mine' => true,
            ]);
        }

        return back()->with('success', 'Mensaje enviado.');
    }

    /**
     * Mark chat as read (called when opening the chat modal).
     */
    public function markRead(Request $request, TournamentVenueRequest $tournamentRequest)
    {
        $isOrganizer = $tournamentRequest->tournament && $tournamentRequest->tournament->isOrganizer(auth()->user());
        $isAdmin = Venue::accessibleBy(auth()->user())->where('id', $tournamentRequest->venue_id)->exists();

        abort_if(!$isOrganizer && !$isAdmin, 403);

        if ($isOrganizer) {
            $tournamentRequest->update(['organizer_last_read_at' => now()]);
        } else {
            $tournamentRequest->update(['admin_last_read_at' => now()]);
        }

        return response()->json(['ok' => true]);
    }
}
