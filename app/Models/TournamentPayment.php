<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TournamentPayment extends Model
{
    protected $fillable = [
        'tournament_id',
        'user_id',
        'amount',
        'status',
        'mp_preference_id',
        'mp_payment_id',
        'team_id',
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(TournamentTeam::class, 'team_id');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
