<?php

namespace App\Services;

use App\Models\Field;
use App\Models\Tournament;
use App\Models\TournamentVenueRequest;
use Illuminate\Database\Eloquent\Collection;

class TournamentVenueService
{
    /**
     * Send a venue request from an organizer for a specific field.
     */
    public function sendVenueRequest(Tournament $tournament, Field $field, array $data): TournamentVenueRequest
    {
        $request = TournamentVenueRequest::create([
            'tournament_id' => $tournament->id,
            'venue_id' => $field->venue_id,
            'field_id' => $field->id,
            'requested_by' => auth()->id(),
            'proposed_dates' => $data['proposed_dates'] ?? null,
            'message' => $data['message'] ?? null,
            'status' => TournamentVenueRequest::STATUS_PENDING,
        ]);

        // Check for auto-approve
        $setting = $field->tournamentSetting;
        if ($setting && $setting->auto_approve) {
            return $this->approveRequest($request);
        }

        // Recalculate tournament approval status
        $tournament->recalculateApprovalStatus();

        return $request;
    }

    /**
     * Send venue requests for multiple fields at once.
     * Returns array of requests grouped by status.
     */
    public function sendMultipleVenueRequests(Tournament $tournament, array $fieldIds, string $message = null): array
    {
        $requests = [];

        foreach ($fieldIds as $fieldId) {
            $field = Field::with('venue', 'tournamentSetting')->findOrFail($fieldId);

            // Skip if request already exists for this field
            $existing = TournamentVenueRequest::where('tournament_id', $tournament->id)
                ->where('field_id', $fieldId)
                ->first();

            if ($existing) {
                $requests[] = $existing;
                continue;
            }

            $requests[] = $this->sendVenueRequest($tournament, $field, [
                'message' => $message ?? 'Solicitud al crear torneo.',
            ]);
        }

        // Recalculate once after all requests
        $tournament->refresh();
        $tournament->recalculateApprovalStatus();

        return $requests;
    }

    /**
     * Venue admin approves a request.
     */
    public function approveRequest(TournamentVenueRequest $request, ?string $message = null): TournamentVenueRequest
    {
        $request->update([
            'status' => TournamentVenueRequest::STATUS_APPROVED,
            'response_message' => $message,
            'responded_at' => now(),
            'responded_by' => auth()->id(),
        ]);

        // Recalculate tournament approval status
        $request->tournament->recalculateApprovalStatus();

        return $request;
    }

    /**
     * Venue admin rejects a request.
     */
    public function rejectRequest(TournamentVenueRequest $request, ?string $reason = null): TournamentVenueRequest
    {
        $request->update([
            'status' => TournamentVenueRequest::STATUS_REJECTED,
            'response_message' => $reason,
            'responded_at' => now(),
            'responded_by' => auth()->id(),
        ]);

        // Recalculate tournament approval status
        $request->tournament->recalculateApprovalStatus();

        return $request;
    }

    /**
     * Get fields that accept tournaments for a given sport.
     */
    public function getAvailableFields(string $sport): Collection
    {
        return Field::whereHas('tournamentSetting', function ($q) {
            $q->where('tournament_enabled', true);
        })
        ->where('sport', $sport)
        ->where('is_active', true)
        ->with(['venue', 'tournamentSetting'])
        ->get();
    }
}
