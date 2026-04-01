<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class FaltaUnoPenalty extends Model
{
    protected $fillable = [
        'user_id',
        'sport',
        'type',
        'reason',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Penalidades activas (no expiradas) */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('expires_at', '>', now());
    }

    /** Solo cooldowns */
    public function scopeCooldowns(Builder $query): Builder
    {
        return $query->where('type', 'cooldown');
    }

    /** Solo bans */
    public function scopeBans(Builder $query): Builder
    {
        return $query->where('type', 'ban');
    }
}
