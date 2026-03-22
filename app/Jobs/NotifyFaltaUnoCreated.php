<?php

namespace App\Jobs;

use App\Models\FaltaUnoGame;
use App\Models\FaltaUnoSportProfile;
use App\Notifications\FaltaUnoNewGameNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyFaltaUnoCreated implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly FaltaUnoGame $game) {}

    public function handle(): void
    {
        $this->game->loadMissing('field');

        $sport = $this->game->field->sport;

        if (!$sport) {
            return;
        }

        $query = FaltaUnoSportProfile::with('user')
            ->where('sport', $sport)
            ->where('user_id', '!=', $this->game->initiator_user_id);

        // Gender filter
        if ($this->game->gender_filter !== 'mixed') {
            $query->where('gender', $this->game->gender_filter);
        }

        // Category range filter
        if ($this->game->category_min || $this->game->category_max) {
            $categories = FaltaUnoSportProfile::getCategoriesForSport($sport);
            $minSearch  = $this->game->category_min ? array_search($this->game->category_min, $categories) : false;
            $maxSearch  = $this->game->category_max ? array_search($this->game->category_max, $categories) : false;
            $minIdx     = $minSearch !== false ? $minSearch : 0;
            $maxIdx     = $maxSearch !== false ? $maxSearch : count($categories) - 1;
            $validCats  = array_slice($categories, $minIdx, $maxIdx - $minIdx + 1);
            $query->whereIn('category', $validCats);
        }

        $game = $this->game;

        $query->limit(50)->chunk(10, function ($profiles) use ($game) {
            foreach ($profiles as $profile) {
                if ($profile->user) {
                    $profile->user->notify(new FaltaUnoNewGameNotification($game));
                }
            }
        });
    }
}
