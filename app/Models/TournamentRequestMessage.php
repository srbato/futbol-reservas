<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TournamentRequestMessage extends Model
{
    protected $fillable = [
        'venue_request_id',
        'user_id',
        'message',
    ];

    public function venueRequest(): BelongsTo
    {
        return $this->belongsTo(TournamentVenueRequest::class, 'venue_request_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
