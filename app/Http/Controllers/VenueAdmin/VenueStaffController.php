<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Mail\VenueStaffInvitationMail;
use App\Models\User;
use App\Models\Venue;
use App\Models\VenueStaff;
use App\Models\VenueStaffInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class VenueStaffController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        abort_if($user->isVenueStaff(), 403);

        $venues = Venue::where('owner_user_id', $user->id)
            ->with([
                'staff',
                'staffInvitations' => fn ($q) => $q->with('user')->whereIn('status', ['pending']),
            ])
            ->orderBy('name')
            ->get();

        return view('va.staff.index', compact('venues'));
    }

    public function invite(Request $request)
    {
        $request->validate([
            'venue_id' => ['required', 'integer'],
            'email'    => ['required', 'email'],
        ]);

        $owner = auth()->user();
        $venue = Venue::where('id', $request->venue_id)
            ->where('owner_user_id', $owner->id)
            ->firstOrFail();

        $invitee = User::where('email', $request->email)->first();

        if (!$invitee) {
            return back()->withErrors(['email' => 'No existe ningún usuario registrado con ese email.'])->withInput();
        }

        if ($invitee->id === $owner->id) {
            return back()->withErrors(['email' => 'No podés invitarte a vos mismo.'])->withInput();
        }

        if (in_array($invitee->role, ['venue_admin', 'super_admin'])) {
            return back()->withErrors(['email' => 'Este usuario ya es dueño de un complejo y no puede ser invitado como empleado.'])->withInput();
        }

        if ($venue->staff()->where('user_id', $invitee->id)->exists()) {
            return back()->withErrors(['email' => 'Este usuario ya es empleado de este complejo.'])->withInput();
        }

        // Remove any previous invitation for this user+venue before creating a new one
        VenueStaffInvitation::where('venue_id', $venue->id)
            ->where('user_id', $invitee->id)
            ->delete();

        $permissions = array_values(array_filter(
            $request->input('permissions', []),
            fn ($p) => is_string($p)
        ));

        $invitation = VenueStaffInvitation::create([
            'venue_id'    => $venue->id,
            'invited_by'  => $owner->id,
            'user_id'     => $invitee->id,
            'token'       => Str::random(64),
            'status'      => 'pending',
            'expires_at'  => now()->addHours(48),
            'permissions' => !empty($permissions) ? $permissions : null,
        ]);

        $invitation->load(['venue', 'user', 'invitedBy']);

        Mail::to($invitee->email)->send(new VenueStaffInvitationMail($invitation));

        return back()->with('success', "Invitación enviada a {$invitee->email}.");
    }

    public function remove(Request $request)
    {
        $request->validate([
            'venue_id' => ['required', 'integer'],
            'user_id'  => ['required', 'integer'],
        ]);

        $owner = auth()->user();
        $venue = Venue::where('id', $request->venue_id)
            ->where('owner_user_id', $owner->id)
            ->firstOrFail();

        $venue->staff()->detach($request->user_id);

        return back()->with('success', 'Empleado removido del complejo.');
    }

    public function updatePermissions(Request $request, VenueStaff $staff)
    {
        $owner = auth()->user();

        // Verificar que el staff pertenece a un venue del usuario logueado
        $venue = Venue::where('id', $staff->venue_id)
            ->where('owner_user_id', $owner->id)
            ->firstOrFail();

        $permissions = array_values(array_filter(
            $request->input('permissions', []),
            fn ($p) => is_string($p)
        ));

        $staff->update([
            'permissions' => !empty($permissions) ? $permissions : null,
        ]);

        return back()->with('success', 'Permisos actualizados correctamente.');
    }

    public function cancelInvitation(Request $request)
    {
        $request->validate([
            'invitation_id' => ['required', 'integer'],
        ]);

        $owner = auth()->user();

        $invitation = VenueStaffInvitation::whereHas('venue', fn ($q) => $q->where('owner_user_id', $owner->id))
            ->where('id', $request->invitation_id)
            ->where('status', 'pending')
            ->firstOrFail();

        $invitation->update(['status' => 'rejected']);

        return back()->with('success', 'Invitación cancelada.');
    }
}
