<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaltaUnoSportProfile extends Model
{
    protected $fillable = [
        'user_id',
        'sport',
        'category',
        'gender',
        'games_played',
        'wins',
        'draws',
        'losses',
        'average_rating',
        'attendance_rate',
        'late_leaves_count',
    ];

    protected $casts = [
        'average_rating'   => 'decimal:2',
        'attendance_rate'  => 'decimal:2',
        'late_leaves_count' => 'integer',
    ];

    /**
     * Recalculate all stat columns (games_played, wins, draws, losses, average_rating)
     * from the source-of-truth tables (FaltaUnoParticipant, ReservationResult, FaltaUnoRating).
     *
     * Call this from anywhere that modifies a result or rating to keep the profile in sync.
     * Returns the profile instance for chaining.
     */
    public function recalculateStats(): self
    {
        // ── 1. Live count of games played + results (Falta Uno + conventional) ──
        $fuParts = \App\Models\FaltaUnoParticipant::with('game.field')
            ->where('user_id', $this->user_id)
            ->where('status', 'confirmed')
            ->get()
            ->filter(fn($p) => $p->game && $p->game->field && $p->game->field->sport === $this->sport);

        $convResults = \App\Models\ReservationResult::with('reservation.field')
            ->where('user_id', $this->user_id)
            ->whereNotNull('match_outcome')
            ->get()
            ->filter(fn($r) => $r->reservation && $r->reservation->field && $r->reservation->field->sport === $this->sport);

        $fuGames = $fuParts->count();
        $fuWins  = $fuParts->where('result', 'win')->count();
        $fuDraws = $fuParts->where('result', 'draw')->count();
        $fuLoss  = $fuParts->where('result', 'loss')->count();

        $convGames = $convResults->count();
        $convWins  = $convResults->where('match_outcome', 'W')->count();
        $convDraws = $convResults->where('match_outcome', 'D')->count();
        $convLoss  = $convResults->where('match_outcome', 'L')->count();

        $this->games_played = $fuGames + $convGames;
        $this->wins         = $fuWins + $convWins;
        $this->draws        = $fuDraws + $convDraws;
        $this->losses       = $fuLoss + $convLoss;

        // ── 2. average_rating from FaltaUnoRating ──
        $assessments = \App\Models\FaltaUnoRating::where('rated_user_id', $this->user_id)
            ->whereHas('game', fn($q) => $q->whereHas('field', fn($q2) => $q2->where('sport', $this->sport)))
            ->pluck('assessment');

        if ($assessments->isEmpty()) {
            $this->average_rating = 0.00;
        } else {
            $scoreMap = ['below' => 1, 'match' => 3, 'above' => 5];
            $avg = $assessments->avg(fn($a) => $scoreMap[$a] ?? 3);
            $this->average_rating = round((float) $avg, 2);
        }

        // ── 3. attendance_rate: confirmed / (confirmed + no_show + late_leaves) ──
        $allStatusParts = \App\Models\FaltaUnoParticipant::with('game.field')
            ->where('user_id', $this->user_id)
            ->whereIn('status', ['confirmed', 'no_show'])
            ->get()
            ->filter(fn($p) => $p->game && $p->game->field && $p->game->field->sport === $this->sport);

        $completed  = $allStatusParts->where('status', 'confirmed')->where('is_late_leave', false)->count();
        $lateLeaves = $allStatusParts->where('status', 'confirmed')->where('is_late_leave', true)->count();
        $noShows    = $allStatusParts->where('status', 'no_show')->count();
        $totalAttendance = $completed + $lateLeaves + $noShows;

        $this->late_leaves_count = $lateLeaves;
        $this->attendance_rate   = $totalAttendance > 0
            ? round(($completed / $totalAttendance) * 100, 2)
            : 100.00;

        $this->save();
        return $this;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getCategoriesForSport(string $sport): array
    {
        if ($sport === 'padel') {
            return ['primera', 'segunda', 'tercera', 'cuarta', 'quinta', 'sexta', 'septima', 'octava'];
        }

        return ['recreativo', 'intermedio', 'avanzado', 'competitivo'];
    }

    public static function getCategoryOrder(string $sport): array
    {
        return self::getCategoriesForSport($sport);
    }

    /**
     * Recalculate category based on last 10 ratings received by this user in this sport.
     * Returns ['old' => ..., 'new' => ..., 'direction' => 'up'|'down'] or null if no change.
     */
    /**
     * Sube/baja categoría según las últimas 10 evaluaciones recibidas.
     * Sube si ≥ 60% dicen 'above', baja si ≥ 60% dicen 'below'.
     * Requiere al menos 5 partidos jugados para activarse.
     */
    public function recalculateCategory(): ?array
    {
        if ($this->games_played < 5) {
            return null;
        }

        $assessments = FaltaUnoRating::where('rated_user_id', $this->user_id)
            ->whereHas('game', fn($q) => $q->whereHas('field', fn($q2) => $q2->where('sport', $this->sport)))
            ->latest()
            ->limit(10)
            ->pluck('assessment');

        if ($assessments->isEmpty()) {
            return null;
        }

        $total      = $assessments->count();
        $aboveRatio = $assessments->filter(fn($a) => $a === 'above')->count() / $total;
        $belowRatio = $assessments->filter(fn($a) => $a === 'below')->count() / $total;

        $order        = self::getCategoryOrder($this->sport);
        $currentIndex = array_search($this->category, $order);

        if ($currentIndex === false) {
            return null;
        }

        $newIndex = $currentIndex;

        if ($aboveRatio >= 0.6 && $currentIndex < count($order) - 1) {
            $newIndex = $currentIndex + 1;
        } elseif ($belowRatio >= 0.6 && $currentIndex > 0) {
            $newIndex = $currentIndex - 1;
        }

        if ($newIndex === $currentIndex) {
            return null;
        }

        $oldCategory    = $this->category;
        $newCategory    = $order[$newIndex];
        $direction      = $newIndex > $currentIndex ? 'up' : 'down';

        $this->category = $newCategory;
        $this->save();

        return [
            'old'       => $oldCategory,
            'new'       => $newCategory,
            'direction' => $direction,
        ];
    }
}
