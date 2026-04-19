<?php

namespace App\Http\Controllers;

use App\Events\FaltaUnoCategoryChanged;
use App\Models\FaltaUnoGame;
use App\Models\FaltaUnoRating;
use App\Models\FaltaUnoSportProfile;
use App\Models\User;
use Illuminate\Http\Request;

class FaltaUnoRatingController extends Controller
{
    public function create(FaltaUnoGame $game)
    {
        // Redirigir al show del partido (la calificación ahora es inline)
        return redirect()->route('falta-uno.show', $game)->withFragment('calificar');
    }

    public function store(Request $request, FaltaUnoGame $game)
    {
        if (!$game->isFinished()) {
            return back()->with('error', 'Solo podés calificar cuando el partido ya terminó.');
        }

        $user = auth()->user();

        $isParticipant = $game->initiator_user_id === $user->id
            || $game->participants()->where('user_id', $user->id)->where('status', 'confirmed')->exists();

        if (!$isParticipant) {
            abort(403);
        }

        $alreadyRated = FaltaUnoRating::where('game_id', $game->id)
            ->where('rater_user_id', $user->id)
            ->exists();

        if ($alreadyRated) {
            return back()->with('info', 'Ya calificaste a los participantes de este partido.');
        }

        $data = $request->validate([
            'ratings'                => ['required', 'array'],
            'ratings.*.user_id'      => ['required', 'exists:users,id'],
            'ratings.*.assessment'   => ['required', 'in:below,match,above'],
            'ratings.*.comment'      => ['nullable', 'string', 'max:500'],
        ]);

        $game->loadMissing(['field', 'activeParticipants']);

        if (!$game->field) {
            return back()->with('error', 'La cancha de este partido ya no existe.');
        }

        $sport = $game->field->sport;

        // IDs validos para calificar: iniciador + participantes confirmados
        $validUserIds = $game->activeParticipants->pluck('user_id')
            ->push($game->initiator_user_id)
            ->unique()
            ->filter(fn($id) => $id !== $user->id)
            ->values();

        foreach ($data['ratings'] as $ratingData) {
            $ratedUserId = $ratingData['user_id'];

            if ($ratedUserId == $user->id) {
                continue;
            }

            // Validar que el usuario calificado sea participante o iniciador del partido
            if (!$validUserIds->contains($ratedUserId)) {
                continue;
            }

            FaltaUnoRating::create([
                'game_id'       => $game->id,
                'rater_user_id' => $user->id,
                'rated_user_id' => $ratedUserId,
                'assessment'    => $ratingData['assessment'],
                'comment'       => $ratingData['comment'] ?? null,
            ]);
            // El FaltaUnoRatingObserver se encarga de recalcular stats + category.

            // Si la category cambió, notificar al usuario
            $profile = FaltaUnoSportProfile::where('user_id', $ratedUserId)
                ->where('sport', $sport)
                ->first();

            if ($profile) {
                $change = $profile->recalculateCategory();
                if ($change) {
                    $ratedUser = User::find($ratedUserId);
                    if ($ratedUser) {
                        broadcast(new FaltaUnoCategoryChanged(
                            $ratedUser,
                            $sport,
                            $change['old'],
                            $change['new'],
                            $change['direction'],
                        ));
                    }
                }
            }
        }

        return redirect()->route('falta-uno.show', $game)
            ->with('success', 'Calificaciones enviadas. ¡Gracias por tu feedback!');
    }
}
