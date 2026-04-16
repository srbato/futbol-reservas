<?php

namespace App\Http\Controllers;

use App\Exceptions\VenueBlockedException;
use App\Models\Field;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function store(Request $request, ReservationService $reservationService)
    {
        $data = $request->validate([
            'field_id' => ['required', 'integer', 'exists:fields,id'],
            'start_at' => ['required', 'date'],
        ]);

        $field = Field::query()
            ->with(['venue', 'price', 'schedules', 'exceptions', 'discounts'])
            ->findOrFail($data['field_id']);

        try {
            $reservation = $reservationService->createSingle(
                $field,
                Carbon::parse($data['start_at']),
                $request->user()->id
            );
        } catch (VenueBlockedException $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }

        return response()->json($reservation, 201);
    }
}
