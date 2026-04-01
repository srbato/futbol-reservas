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
        'age_group',
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
