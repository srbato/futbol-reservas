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
        if (!$game->isFinished()) {
            return back()->with('error', 'Solo podés calificar cuando el partido ya terminó.');
        }

        $user = auth()->user();

        $isParticipant = $game->initiator_user_id === $user->id
            || $game->participants()->where('user_id', $user->id)->where('status', 'confirmed')->exists();

        if (!$isParticipant) {
            abort(403, 'Solo los participantes pueden calificar.');
        }

        $alreadyRated = FaltaUnoRating::where('game_id', $game->id)
            ->where('rater_user_id', $user->id)
            ->exists();

        if ($alreadyRated) {
            return back()->with('info', 'Ya calificaste a los participantes de este partido.');
        }

        // Collect all participants except the current user
        $participantUserIds = $game->activeParticipants()->pluck('user_id');
        $otherIds = collect([$game->initiator_user_id])
            ->merge($participantUserIds)
            ->unique()
            ->filter(fn($id) => $id !== $user->id)
            ->values();

        $otherUsers = User::whereIn('id', $otherIds)->get();

        $game->loadMissing(['field.venue']);

        return view('falta-uno.rate', compact('game', 'otherUsers'));
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

        $game->loadMissing('field');
        $sport = $game->field->sport;

        foreach ($data['ratings'] as $ratingData) {
            $ratedUserId = $ratingData['user_id'];

            if ($ratedUserId == $user->id) {
                continue;
            }

            FaltaUnoRating::create([
                'game_id'       => $game->id,
                'rater_user_id' => $user->id,
                'rated_user_id' => $ratedUserId,
                'assessment'    => $ratingData['assessment'],
                'comment'       => $ratingData['comment'] ?? null,
            ]);

            // Recalculate average_rating: below=1, match=3, above=5
            $profile = FaltaUnoSportProfile::where('user_id', $ratedUserId)
                ->where('sport', $sport)
                ->first();

            if ($profile) {
                $assessments = FaltaUnoRating::where('rated_user_id', $ratedUserId)
                    ->whereHas('game', fn($q) => $q->whereHas('field', fn($q2) => $q2->where('sport', $sport)))
                    ->pluck('assessment');

                $scoreMap = ['below' => 1, 'match' => 3, 'above' => 5];
                $avg = $assessments->avg(fn($a) => $scoreMap[$a] ?? 3);
                $profile->average_rating = round($avg, 2);
                $profile->save();

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

        return redirect()->route('falta-uno.index')
            ->with('success', 'Calificaciones enviadas. ¡Gracias por tu feedback!');
    }
}
