<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\ReservationPlayer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReservationPlayerController extends Controller
{
    public function store(Request $request, Reservation $reservation): RedirectResponse
    {
        abort_if($reservation->user_id !== auth()->id(), 403);
        abort_if($reservation->status !== 'PAID', 422);

        $data = $request->validate([
            'search' => ['required', 'string', 'max:255'],
        ]);

        $search = trim($data['search']);

        $player = User::where('email', $search)
            ->orWhere('name', $search)
            ->first();

        if (!$player) {
            return back()->withErrors(['search' => 'No existe un usuario con ese nombre o email.']);
        }

        if ($player->id === auth()->id()) {
            return back()->withErrors(['search' => 'No podés agregarte a vos mismo.']);
        }

        if ($reservation->players()->where('user_id', $player->id)->exists()) {
            return back()->withErrors(['search' => "{$player->name} ya está en el partido."]);
        }

        ReservationPlayer::create([
            'reservation_id'   => $reservation->id,
            'user_id'          => $player->id,
            'added_by_user_id' => auth()->id(),
        ]);

        return back()->with('success', "{$player->name} fue agregado al partido.");
    }

    public function destroy(Reservation $reservation, User $player): RedirectResponse
    {
        abort_if($reservation->user_id !== auth()->id(), 403);

        $reservation->players()->where('user_id', $player->id)->delete();

        return back()->with('success', 'Jugador eliminado del partido.');
    }
}
