<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Venue;
use App\Models\VenueUserBlock;
use Illuminate\Http\Request;

class VenueUserBlockController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $venues = Venue::query()
            ->accessibleBy($user)
            ->orderBy('name')
            ->get();

        $blocks = VenueUserBlock::query()
            ->whereIn('venue_id', $venues->pluck('id'))
            ->with(['venue', 'user', 'blockedByUser'])
            ->orderByDesc('created_at')
            ->get();

        return view('va.user-blocks.index', compact('venues', 'blocks'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => ['required', 'string', 'min:2'],
        ]);

        $q = $request->input('q');

        $users = User::query()
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
            })
            ->where('role', 'user')
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $authUser = $request->user();

        $data = $request->validate([
            'venue_id' => ['required', 'integer', 'exists:venues,id'],
            'user_id'  => ['required', 'integer', 'exists:users,id'],
            'reason'   => ['nullable', 'string', 'max:500'],
        ]);

        // Verify the admin has access to this venue
        $venue = Venue::query()
            ->accessibleBy($authUser)
            ->findOrFail($data['venue_id']);

        // Can't block yourself
        if ((int) $data['user_id'] === $authUser->id) {
            return back()->with('error', 'No podés bloquearte a vos mismo.');
        }

        // Can't block the venue owner
        if ((int) $data['user_id'] === $venue->owner_user_id) {
            return back()->with('error', 'No podés bloquear al dueño del complejo.');
        }

        // Check if already blocked
        $exists = VenueUserBlock::where('venue_id', $venue->id)
            ->where('user_id', $data['user_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Este usuario ya está bloqueado en este complejo.');
        }

        VenueUserBlock::create([
            'venue_id'   => $venue->id,
            'user_id'    => $data['user_id'],
            'reason'     => $data['reason'] ?? null,
            'blocked_by' => $authUser->id,
        ]);

        return redirect()->route('va.user-blocks.index')
            ->with('success', 'Usuario bloqueado correctamente.');
    }

    public function destroy(Request $request, VenueUserBlock $block)
    {
        $authUser = $request->user();

        $block->load('venue');

        // Verify the admin has access to this venue
        $venue = Venue::query()
            ->accessibleBy($authUser)
            ->find($block->venue_id);

        if (!$venue) {
            abort(403);
        }

        $block->delete();

        return redirect()->route('va.user-blocks.index')
            ->with('success', 'Usuario desbloqueado correctamente.');
    }
}
