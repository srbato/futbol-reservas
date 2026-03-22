<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaltaUnoParticipant extends Model
{
    protected $fillable = [
        'game_id',
        'user_id',
        'status',
        'goals',
        'assists',
        'result',
        'stats_submitted_at',
    ];

    protected $casts = [
        'stats_submitted_at' => 'datetime',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(FaltaUnoGame::class, 'game_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
