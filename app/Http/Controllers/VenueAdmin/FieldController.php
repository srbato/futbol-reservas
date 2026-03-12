<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\FieldPrice;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FieldController extends Controller
{
    public function create(Request $request, Venue $venue)
    {
        if ($venue->owner_user_id !== $request->user()->id && $request->user()->role !== 'super_admin') {
            abort(403);
        }

        return view('va.fields.create', compact('venue'));
    }

    public function store(Request $request, Venue $venue)
    {
        if ($venue->owner_user_id !== $request->user()->id && $request->user()->role !== 'super_admin') {
            abort(403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'sport' => ['required', 'string', 'max:30'],
            'format' => ['required', 'integer', 'min:3', 'max:11'],
            'slot_minutes' => ['required', 'integer', 'min:30', 'max:180'],
            'price_per_slot' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
        ]);

        $field = Field::create([
            'venue_id' => $venue->id,
            'name' => $data['name'],
            'sport' => $data['sport'],
            'format' => $data['format'],
            'slot_minutes' => $data['slot_minutes'],
            'is_indoor' => false,
            'is_active' => true,
        ]);

        FieldPrice::create([
            'field_id' => $field->id,
            'price_per_slot' => $data['price_per_slot'],
            'currency' => strtoupper($data['currency']),
        ]);

        return redirect()->route('va.schedule.edit', $field);
    }

    public function edit(Request $request, Field $field)
    {
        $field->load(['venue', 'price']);

        if ($field->venue->owner_user_id !== $request->user()->id && $request->user()->role !== 'super_admin') {
            abort(403);
        }

        return view('va.fields.edit', compact('field'));
    }

    public function update(Request $request, Field $field)
    {
        $field->load(['venue', 'price']);

        if ($field->venue->owner_user_id !== $request->user()->id && $request->user()->role !== 'super_admin') {
            abort(403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'sport' => ['required', 'string', 'max:30'],
            'format' => ['required', 'integer', 'min:3', 'max:11'],
            'slot_minutes' => ['required', 'integer', 'min:30', 'max:180'],
            'price_per_slot' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
        ]);

        $field->update([
            'name' => $data['name'],
            'sport' => $data['sport'],
            'format' => $data['format'],
            'slot_minutes' => $data['slot_minutes'],
        ]);

        if ($request->hasFile('cover_image')) {
            if ($field->cover_image_path) {
                Storage::disk('public')->delete($field->cover_image_path);
            }

            $path = $request->file('cover_image')->store('fields', 'public');
            $field->cover_image_path = $path;
        }

        $field->save();

        FieldPrice::updateOrCreate(
            ['field_id' => $field->id],
            [
                'price_per_slot' => $data['price_per_slot'],
                'currency' => strtoupper($data['currency']),
            ]
        );

        return redirect()->route('va.dashboard');
    }
}
