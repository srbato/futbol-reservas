<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function edit(Request $request, Field $field)
    {
        $field->load(['venue', 'schedules']);

        if ($request->user()->role !== 'super_admin' && $field->venue->owner_user_id !== $request->user()->id && !$request->user()->isStaffOf($field->venue->id)) {
            abort(403);
        }

        $existing = $field->schedules()->get()->keyBy('day_of_week');

        return view('va.schedule.edit', compact('field', 'existing'));
    }

    public function update(Request $request, Field $field)
    {
        $field->load('venue');

        if ($request->user()->role !== 'super_admin' && $field->venue->owner_user_id !== $request->user()->id && !$request->user()->isStaffOf($field->venue->id)) {
            abort(403);
        }

        $data = $request->validate([
            'days' => ['required', 'array'],
            'days.*.is_closed' => ['nullable'],
            'days.*.open_time' => ['nullable', 'date_format:H:i'],
            'days.*.close_time' => ['nullable', 'date_format:H:i'],
        ]);

        foreach ($data['days'] as $dow => $row) {
            $dow = (int) $dow;
            if ($dow < 0 || $dow > 6) {
                continue;
            }

            $isClosed = isset($row['is_closed']);
            $open = $row['open_time'] ?? null;
            $close = $row['close_time'] ?? null;

            if ($isClosed || !$open || !$close) {
                $field->schedules()->where('day_of_week', (int) $dow)->delete();
                continue;
            }

            $field->schedules()->updateOrCreate(
                ['day_of_week' => (int) $dow],
                [
                    'open_time' => $open,
                    'close_time' => $close,
                ]
            );
        }

        return redirect()->route('va.dashboard')
            ->with('success', 'Horarios actualizados.');
    }
}