<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TournamentMatch extends Model
{
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_FINISHED = 'finished';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_WALKOVER = 'walkover';

    protected $fillable = [
        'tournament_id',
        'round',
        'round_name',
        'match_number',
        'home_team_id',
        'away_team_id',
        'venue_name_override',
        'scheduled_at',
        'status',
        'home_score',
        'away_score',
        'home_penalties',
        'away_penalties',
        'winner_team_id',
        'notes',
        'played_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'played_at'    => 'datetime',
    ];

    // ── Relaciones ──────────────────────────────────────────

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(TournamentTeam::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(TournamentTeam::class, 'away_team_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(TournamentTeam::class, 'winner_team_id');
    }

    // ── Métodos ─────────────────────────────────────────────

    public function isFinished(): bool
    {
        return $this->status === self::STATUS_FINISHED;
    }

    public function hasPenalties(): bool
    {
        return $this->home_penalties !== null && $this->away_penalties !== null;
    }

    public function scoreDisplay(): string
    {
        $score = ($this->home_score ?? '?') . ' - ' . ($this->away_score ?? '?');

        if ($this->hasPenalties()) {
            $score .= ' (' . $this->home_penalties . '-' . $this->away_penalties . ' pen)';
        }

        return $score;
    }
}
