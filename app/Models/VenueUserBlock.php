<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VenueUserBlock extends Model
{
    protected $fillable = [
        'venue_id',
        'user_id',
        'reason',
        'blocked_by',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function blockedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }

    /**
     * Check if a user is blocked in a specific venue.
     */
    public static function isBlocked(int $userId, int $venueId): bool
    {
        return static::where('user_id', $userId)
            ->where('venue_id', $venueId)
            ->exists();
    }
}
