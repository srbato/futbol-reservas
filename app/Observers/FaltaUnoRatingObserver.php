<?php

namespace App\Observers;

use App\Models\FaltaUnoRating;
use App\Models\FaltaUnoSportProfile;

class FaltaUnoRatingObserver
{
    /**
     * Recalcula stats del perfil afectado.
     * Resolver el sport desde el game.field.
     */
    protected function resync(FaltaUnoRating $rating): void
    {
        $rating->loadMissing('game.field');
        $sport = $rating->game?->field?->sport;
        if (!$sport) {
            return;
        }

        $profile = FaltaUnoSportProfile::where('user_id', $rating->rated_user_id)
            ->where('sport', $sport)
            ->first();

        if ($profile) {
            $profile->recalculateStats();
            $profile->recalculateCategory();
        }
    }

    public function created(FaltaUnoRating $rating): void
    {
        $this->resync($rating);
    }

    public function updated(FaltaUnoRating $rating): void
    {
        $this->resync($rating);
    }

    public function deleted(FaltaUnoRating $rating): void
    {
        $this->resync($rating);
    }

    public function restored(FaltaUnoRating $rating): void
    {
        $this->resync($rating);
    }

    public function forceDeleted(FaltaUnoRating $rating): void
    {
        $this->resync($rating);
    }
}
