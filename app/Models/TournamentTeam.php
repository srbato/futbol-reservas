<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TournamentTeam extends Model
{
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_DISQUALIFIED = 'disqualified';
    const STATUS_WITHDRAWN = 'withdrawn';

    protected $fillable = [
        'tournament_id',
        'name',
        'logo_path',
        'captain_user_id',
        'status',
        'seed',
        'payment_confirmed',
        'payment_confirmed_at',
        'payment_method',
        'payment_external_id',
        'mp_preference_id',
    ];

    protected $hidden = [
        'payment_external_id',
        'mp_preference_id',
    ];

    protected $casts = [
        'payment_confirmed' => 'boolean',
        'payment_confirmed_at' => 'datetime',
    ];

    // ── Relaciones ──────────────────────────────────────────

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function captain(): BelongsTo
    {
        return $this->belongsTo(User::class, 'captain_user_id');
    }

    public function players(): HasMany
    {
        return $this->hasMany(TournamentTeamPlayer::class, 'team_id');
    }

    public function homeMatches(): HasMany
    {
        return $this->hasMany(TournamentMatch::class, 'home_team_id');
    }

    public function awayMatches(): HasMany
    {
        return $this->hasMany(TournamentMatch::class, 'away_team_id');
    }

    // ── Métodos ─────────────────────────────────────────────

    public function isCaptain(User $user): bool
    {
        return $this->captain_user_id === $user->id;
    }
}
