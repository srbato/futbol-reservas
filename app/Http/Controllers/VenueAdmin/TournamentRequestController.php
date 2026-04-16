<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\FieldBlock;
use App\Models\Reservation;
use App\Models\TournamentScheduleRequest;
use App\Models\TournamentVenueRequest;
use App\Models\Venue;
use App\Services\TournamentVenueService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TournamentRequestController extends Controller
{
    public function index()
    {
        $venue = Venue::accessibleBy(auth()->user())->first();
        abort_if(!$venue, 404);

        $requests = TournamentVenueRequest::where('venue_id', $venue->id)
            ->with(['tournament.organizer', 'field.tournamentSetting', 'messages.user', 'scheduleRequest'])
            ->latest()
            ->paginate(20);

        // Build conflicts for each pending schedule request
        $scheduleConflicts = [];
        foreach ($requests as $req) {
            $schedule = $req->scheduleRequest;
            if (!$schedule || !$schedule->isPending()) continue;

            $conflicts = [];
            foreach ($schedule->slots as $slot) {
                $date = $slot['date'];
                $start = $slot['start_time'];
                $end = $slot['end_time'];

                // Check FieldBlocks
                $blocks = FieldBlock::where('field_id', $schedule->field_id)
                    ->where('date', $date)
                    ->where('start_time', '<', $end)
                    ->where('end_time', '>', $start)
                    ->get();

                foreach ($blocks as $b) {
                    $conflicts[] = [
                        'date' => Carbon::parse($b->date)->format('d/m/Y'),
                        'time' => $b->start_time . '–' . $b->end_time,
                        'reason' => $b->reason ?? 'Bloqueado',
                    ];
                }

                // Check Reservations
                $dayStart = Carbon::parse("{$date} {$start}");
                $dayEnd = Carbon::parse("{$date} {$end}");

                $reservations = Reservation::where('field_id', $schedule->field_id)
                    ->where('start_at', '<', $dayEnd)
                    ->where('end_at', '>', $dayStart)
                    ->whereNotIn('status', ['cancelled'])
                    ->get();

                foreach ($reservations as $r) {
                    $conflicts[] = [
                        'date' => $r->start_at->format('d/m/Y'),
                        'time' => $r->start_at->format('H:i') . '–' . $r->end_at->format('H:i'),
                        'reason' => 'Reserva',
                    ];
                }
            }

            $scheduleConflicts[$req->id] = $conflicts;
        }

        return view('va.tournament-requests.index', compact('venue', 'requests', 'scheduleConflicts'));
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

    public function approveSchedule(Request $request, TournamentVenueRequest $tournamentRequest)
    {
        $venue = Venue::accessibleBy(auth()->user())->first();
        abort_if(!$venue || $tournamentRequest->venue_id !== $venue->id, 403);

        $schedule = $tournamentRequest->scheduleRequest;
        abort_if(!$schedule, 404, 'No hay solicitud de horarios.');
        abort_if(!$schedule->isPending(), 422, 'Esta solicitud ya fue procesada.');

        $schedule->update([
            'status' => TournamentScheduleRequest::STATUS_APPROVED,
            'responded_by' => auth()->id(),
            'responded_at' => now(),
        ]);

        // Create FieldBlocks so the slots appear as occupied in the venue calendar
        foreach ($schedule->slots as $slot) {
            FieldBlock::create([
                'field_id' => $schedule->field_id,
                'date' => $slot['date'],
                'start_time' => $slot['start_time'],
                'end_time' => $slot['end_time'],
                'reason' => 'Torneo: ' . ($tournamentRequest->tournament->name ?? 'Torneo'),
            ]);
        }

        return back()->with('success', 'Horarios aprobados. Las canchas quedaron bloqueadas en esos horarios.');
    }

    public function rejectSchedule(Request $request, TournamentVenueRequest $tournamentRequest)
    {
        $venue = Venue::accessibleBy(auth()->user())->first();
        abort_if(!$venue || $tournamentRequest->venue_id !== $venue->id, 403);

        $schedule = $tournamentRequest->scheduleRequest;
        abort_if(!$schedule, 404, 'No hay solicitud de horarios.');
        abort_if(!$schedule->isPending(), 422, 'Esta solicitud ya fue procesada.');

        $schedule->update([
            'status' => TournamentScheduleRequest::STATUS_REJECTED,
            'response_message' => $request->input('response_message'),
            'responded_by' => auth()->id(),
            'responded_at' => now(),
        ]);

        return back()->with('success', 'Horarios rechazados. El organizador podra enviar una nueva solicitud.');
    }
}
