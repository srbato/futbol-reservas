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
