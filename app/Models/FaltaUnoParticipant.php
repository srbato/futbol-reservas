<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaltaUnoParticipant extends Model
{
    use HasFactory;
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_NO_SHOW = 'no_show';
    const VALID_STATUSES = ['confirmed', 'cancelled', 'no_show'];

    protected $fillable = [
        'game_id',
        'user_id',
        'status',
        'is_late_leave',
        'was_kicked',
        'left_at',
        'no_show_at',
        'goals',
        'assists',
        'result',
        'stats_submitted_at',
    ];

    protected $casts = [
        'is_late_leave'     => 'boolean',
        'was_kicked'        => 'boolean',
        'left_at'           => 'datetime',
        'no_show_at'        => 'datetime',
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
