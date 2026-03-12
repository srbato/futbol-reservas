<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\FieldBlock;
use Illuminate\Http\Request;

class FieldBlockController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $fields = Field::query()
            ->whereHas('venue', function ($q) use ($user) {
                $q->where('owner_user_id', $user->id);
            })
            ->with('venue')
            ->orderBy('name')
            ->get();

        $blocks = FieldBlock::query()
            ->whereHas('field.venue', function ($q) use ($user) {
                $q->where('owner_user_id', $user->id);
            })
            ->with(['field.venue'])
            ->orderByDesc('date')
            ->orderBy('start_time')
            ->get();

        return view('va.blocks.index', compact('fields', 'blocks'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'field_id' => ['required', 'integer', 'exists:fields,id'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $field = Field::query()
            ->whereHas('venue', function ($q) use ($user) {
                $q->where('owner_user_id', $user->id);
            })
            ->findOrFail($data['field_id']);

        FieldBlock::create([
            'field_id' => $field->id,
            'date' => $data['date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'reason' => $data['reason'] ?? null,
        ]);

        return redirect()->route('va.blocks.index');
    }

    public function destroy(Request $request, FieldBlock $block)
    {
        $user = $request->user();

        $block->load('field.venue');

        if ($user->role !== 'super_admin' && $block->field->venue->owner_user_id !== $user->id) {
            abort(403);
        }

        $block->delete();

        return redirect()->route('va.blocks.index');
    }
}
