<?php

namespace App\Http\Controllers;

use App\Models\FaltaUnoGame;
use App\Models\FaltaUnoParticipant;
use Illuminate\Http\Request;

class FaltaUnoStatsController extends Controller
{
    public function create(FaltaUnoGame $game)
    {
        if (!$game->isFinished()) {
            return back()->with('error', 'Solo podés registrar stats cuando el partido ya terminó.');
        }

        $user        = auth()->user();
        $participant = $this->resolveParticipant($game, $user->id);

        if (!$participant) {
            abort(403, 'Solo los participantes pueden registrar sus stats.');
        }

        $game->loadMissing(['field.venue']);

        return view('falta-uno.stats', compact('game', 'participant'));
    }

    public function store(Request $request, FaltaUnoGame $game)
    {
        if (!$game->isFinished()) {
            return back()->with('error', 'Solo podés registrar stats cuando el partido ya terminó.');
        }

        $user        = auth()->user();
        $participant = $this->resolveParticipant($game, $user->id);

        if (!$participant) {
            abort(403, 'Solo los participantes pueden registrar sus stats.');
        }

        if ($participant->stats_submitted_at) {
            return back()->with('info', 'Ya enviaste tus estadísticas para este partido.');
        }

        $data = $request->validate([
            'goals'   => ['nullable', 'integer', 'min:0'],
            'assists' => ['nullable', 'integer', 'min:0'],
            'result'  => ['required', 'in:win,draw,loss'],
        ]);

        $participant->update([
            'goals'              => $data['goals'] ?? null,
            'assists'            => $data['assists'] ?? null,
            'result'             => $data['result'],
            'stats_submitted_at' => now(),
        ]);

        // Update the sport profile stats
        $game->loadMissing('field');
        $sport   = $game->field->sport;
        $profile = $user->sportProfileFor($sport);

        if ($profile) {
            $profile->games_played += 1;

            if ($data['result'] === 'win') {
                $profile->wins += 1;
            } elseif ($data['result'] === 'draw') {
                $profile->draws += 1;
            } else {
                $profile->losses += 1;
            }

            $profile->save();
        }

        return redirect()->route('profile.edit')->with('success', 'Estadísticas guardadas. ¡Gracias!');
    }

    /**
     * Devuelve el participante del juego para el usuario dado.
     * Si es el iniciador y no tiene registro, lo crea on-demand.
     */
    private function resolveParticipant(FaltaUnoGame $game, int $userId): ?FaltaUnoParticipant
    {
        $participant = FaltaUnoParticipant::where('game_id', $game->id)
            ->where('user_id', $userId)
            ->where('status', 'confirmed')
            ->first();

        if (!$participant && $game->initiator_user_id === $userId) {
            $participant = FaltaUnoParticipant::firstOrCreate(
                ['game_id' => $game->id, 'user_id' => $userId],
                ['status'  => 'confirmed'],
            );
        }

        return $participant;
    }
}
