<?php

namespace App\Http\Controllers;

use App\Models\FaltaUnoSportProfile;
use App\Models\Reservation;
use App\Models\ReservationResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchHistoryController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        // Reservations where user is owner OR tagged player, only PAID, in the past
        $reservations = Reservation::with([
                'field.venue', 'players', 'user',
                'results' => fn($q) => $q->where('user_id', $user->id),
            ])
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('players', fn($q2) => $q2->where('user_id', $user->id));
            })
            ->whereIn('status', ['PAID'])
            ->orderByDesc('start_at')
            ->paginate(15);

        // Only past for stats (can't compute stats for future matches)
        $completed = Reservation::with([
                'field.venue',
                'results' => fn($q) => $q->where('user_id', $user->id),
            ])
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('players', fn($q2) => $q2->where('user_id', $user->id));
            })
            ->whereIn('status', ['PAID'])
            ->where('start_at', '<=', now())
            ->get();

        $totalGames = $completed->count();
        $totalSpent = $completed->where('user_id', $user->id)->sum('total_amount');
        $currency   = $completed->first()?->currency ?? 'ARS';

        $favoriteSport = $completed->isNotEmpty()
            ? $completed->groupBy(fn($r) => $r->field->sport)->map->count()->sortDesc()->keys()->first()
            : null;

        $favoriteVenue = $completed->isNotEmpty()
            ? $completed->groupBy(fn($r) => $r->field->venue->name)->map->count()->sortDesc()->keys()->first()
            : null;

        $statsBySport = $completed->isNotEmpty()
            ? $completed->groupBy(fn($r) => $r->field->sport)->map->count()->sortDesc()
            : collect();

        // Outcomes (W/D/L) by sport — each user's own result
        $outcomesBySport = ['_all' => ['W' => 0, 'D' => 0, 'L' => 0]];

        foreach ($completed as $r) {
            $sport   = $r->field->sport;
            $outcome = $r->results->first()?->match_outcome;

            if (!isset($outcomesBySport[$sport])) {
                $outcomesBySport[$sport] = ['W' => 0, 'D' => 0, 'L' => 0];
            }

            if (in_array($outcome, ['W', 'D', 'L'])) {
                $outcomesBySport['_all'][$outcome]++;
                $outcomesBySport[$sport][$outcome]++;
            }
        }

        $sportOptions = collect($statsBySport->keys())->mapWithKeys(
            fn($s) => [$s => self::sportLabel($s)]
        )->all();

        return view('match-history.index', compact(
            'reservations',
            'totalGames',
            'totalSpent',
            'currency',
            'favoriteSport',
            'favoriteVenue',
            'statsBySport',
            'outcomesBySport',
            'sportOptions',
        ));
    }

    public function updateResult(Request $request, Reservation $reservation): RedirectResponse
    {
        $user = auth()->user();

        $isOwner        = $reservation->user_id === $user->id;
        $isTaggedPlayer = $reservation->players()->where('user_id', $user->id)->exists();

        abort_if(!$isOwner && !$isTaggedPlayer, 403);
        abort_if($reservation->status !== 'PAID', 422);
        abort_if($reservation->start_at->isFuture(), 422);

        $data = $request->validate([
            'match_result'  => ['nullable', 'string', 'max:100'],
            'match_outcome' => ['nullable', 'in:W,D,L'],
        ]);

        // Check if a result already exists (to know previous outcome for profile adjustment)
        $existingResult = ReservationResult::where('reservation_id', $reservation->id)
            ->where('user_id', $user->id)
            ->first();

        $previousOutcome = $existingResult?->match_outcome;
        $newOutcome      = $data['match_outcome'] ?: null;

        // Each user saves their own result independently
        ReservationResult::updateOrCreate(
            ['reservation_id' => $reservation->id, 'user_id' => $user->id],
            [
                'match_result'  => $data['match_result']  ?: null,
                'match_outcome' => $newOutcome,
            ]
        );

        // Update FaltaUnoSportProfile with the result
        // Only sync if there's a new outcome OR if removing a previous one
        if ($newOutcome || $previousOutcome) {
            $this->syncSportProfile($user, $reservation, $previousOutcome, $newOutcome);
        }

        return back()->with('success', 'Resultado guardado.');
    }

    /**
     * Sync the user's FaltaUnoSportProfile when a reservation result is saved.
     * Handles both first-time creation and updates to existing results.
     */
    private function syncSportProfile($user, Reservation $reservation, ?string $previousOutcome, string $newOutcome): void
    {
        $sport = $reservation->field->sport;

        $profile = FaltaUnoSportProfile::firstOrCreate(
            ['user_id' => $user->id, 'sport' => $sport],
            [
                'category'         => FaltaUnoSportProfile::getCategoriesForSport($sport)[0],
                'games_played'     => 0,
                'wins'             => 0,
                'draws'            => 0,
                'losses'           => 0,
                'average_rating'   => 0,
                'attendance_rate'  => 100,
                'late_leaves_count' => 0,
            ]
        );

        $hadPrevious = !is_null($previousOutcome);
        $hasNew      = !is_null($newOutcome);

        if (!$hadPrevious && $hasNew) {
            // First time setting outcome: increment games_played + add W/D/L
            $profile->games_played += 1;
        } elseif ($hadPrevious && !$hasNew) {
            // Removing outcome: decrement games_played + subtract previous W/D/L
            $profile->games_played = max(0, $profile->games_played - 1);
        }
        // If both had previous and has new: games_played stays the same (just swapping W/D/L)

        // Subtract previous W/D/L if existed
        if ($hadPrevious) {
            match ($previousOutcome) {
                'W' => $profile->wins   = max(0, $profile->wins - 1),
                'D' => $profile->draws  = max(0, $profile->draws - 1),
                'L' => $profile->losses = max(0, $profile->losses - 1),
                default => null,
            };
        }

        // Add new W/D/L if exists
        if ($hasNew) {
            match ($newOutcome) {
                'W' => $profile->wins   += 1,
                'D' => $profile->draws  += 1,
                'L' => $profile->losses += 1,
                default => null,
            };
        }

        $profile->save();
    }

    public static function sportLabel(string $sport): string
    {
        return match ($sport) {
            'football'   => 'Fútbol',
            'tennis'     => 'Tenis',
            'padel'      => 'Pádel',
            'paddle'     => 'Pádel',
            'basketball' => 'Básquet',
            'volleyball' => 'Vóley',
            default      => ucfirst($sport),
        };
    }
}
