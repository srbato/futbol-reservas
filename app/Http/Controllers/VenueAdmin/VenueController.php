<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VenueController extends Controller
{
    public function create(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'super_admin' && Venue::where('owner_user_id', $user->id)->exists()) {
            return redirect()->route('va.dashboard')
                ->with('error', 'Ya tenés un complejo creado. Solo podés administrar un complejo por cuenta.');
        }

        return view('va.venues.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
            'address' => ['nullable', 'string', 'max:200'],
            'zone' => ['nullable', 'string', 'max:120'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $user = $request->user();
        if ($user->role !== 'super_admin' && Venue::where('owner_user_id', $user->id)->exists()) {
            return redirect()->route('va.dashboard')
                ->with('error', 'Ya tenés un complejo creado. Solo podés administrar un complejo por cuenta.');
        }

        $venue = new Venue();
        $venue->owner_user_id = $user->id;
        $venue->name = $data['name'];
        $venue->description = $data['description'] ?? null;
        $venue->address = $data['address'] ?? null;
        $venue->zone = $data['zone'] ?? null;
        $venue->lat = $data['lat'] ?? null;
        $venue->lng = $data['lng'] ?? null;
        $venue->is_active = true;

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('venues', 'public');
            $venue->cover_image_path = $path;
        }

        $venue->save();

        return redirect()->route('va.dashboard');
    }

    public function edit(Request $request, Venue $venue)
    {
        if ($venue->owner_user_id !== $request->user()->id && $request->user()->role !== 'super_admin') {
            abort(403);
        }

        return view('va.venues.edit', compact('venue'));
    }

    public function update(Request $request, Venue $venue)
    {
        if ($venue->owner_user_id !== $request->user()->id && $request->user()->role !== 'super_admin') {
            abort(403);
        }

       $data = $request->validate([
            'name'        => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
            'address'     => ['nullable', 'string', 'max:200'],
            'zone'        => ['nullable', 'string', 'max:120'],
            'lat'         => ['nullable', 'numeric', 'between:-90,90'],
            'lng'         => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $venue->name        = $data['name'];
        $venue->description = $data['description'] ?? null;
        $venue->address     = $data['address'] ?? null;
        $venue->zone        = $data['zone'] ?? null;
        $venue->lat         = $data['lat'] ?? null;
        $venue->lng         = $data['lng'] ?? null;

        if ($request->hasFile('cover_image')) {
            if ($venue->cover_image_path) {
                Storage::disk('public')->delete($venue->cover_image_path);
            }

            $path = $request->file('cover_image')->store('venues', 'public');
            $venue->cover_image_path = $path;
        }

        $venue->save();

        return redirect()->route('va.dashboard');
    }
}
