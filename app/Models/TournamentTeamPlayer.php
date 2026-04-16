<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TournamentTeamPlayer extends Model
{
    protected $fillable = [
        'team_id',
        'user_id',
        'name',
        'email',
        'role',
        'jersey_number',
        'dni',
    ];

    // ── Relaciones ──────────────────────────────────────────

    public function team(): BelongsTo
    {
        return $this->belongsTo(TournamentTeam::class, 'team_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
