<?php

namespace App\Http\Controllers;

use App\Models\VenueStaff;
use App\Models\VenueStaffInvitation;
use Illuminate\Http\Request;

class VenueStaffInvitationController extends Controller
{
    public function accept(string $token)
    {
        $invitation = VenueStaffInvitation::where('token', $token)
            ->with(['venue', 'user'])
            ->firstOrFail();

        if ($invitation->status === 'rejected') {
            return redirect()->route('venues.index')
                ->withErrors(['invitation' => 'Ya rechazaste esta invitación. Pedile al dueño que te envíe una nueva.']);
        }

        if ($invitation->status === 'accepted') {
            return redirect()->route('va.dashboard')
                ->with('success', 'Ya sos parte del equipo de ' . $invitation->venue->name . '.');
        }

        if ($invitation->status !== 'pending') {
            abort(404);
        }

        if ($invitation->user_id !== auth()->id()) {
            abort(403, 'Esta invitación no es para tu cuenta.');
        }

        if ($invitation->isExpired()) {
            $invitation->update(['status' => 'rejected']);
            return redirect()->route('va.dashboard')
                ->withErrors(['invitation' => 'Esta invitación ya venció.']);
        }

        VenueStaff::updateOrCreate(
            ['venue_id' => $invitation->venue_id, 'user_id' => $invitation->user_id],
            ['permissions' => $invitation->permissions]
        );
        $invitation->update(['status' => 'accepted']);

        return redirect()->route('va.dashboard')
            ->with('success', '¡Bienvenido! Ya sos parte del equipo de ' . $invitation->venue->name . '.');
    }

    public function reject(string $token)
    {
        $invitation = VenueStaffInvitation::where('token', $token)
            ->where('status', 'pending')
            ->with('venue')
            ->firstOrFail();

        if ($invitation->user_id !== auth()->id()) {
            abort(403, 'Esta invitación no es para tu cuenta.');
        }

        $invitation->update(['status' => 'rejected']);

        return redirect()->route('venues.index')
            ->with('success', 'Invitación rechazada.');
    }
}
