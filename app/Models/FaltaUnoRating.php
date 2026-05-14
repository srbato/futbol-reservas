<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaltaUnoRating extends Model
{
    use HasFactory;
    protected $fillable = [
        'game_id',
        'rater_user_id',
        'rated_user_id',
        'assessment',
        'comment',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(FaltaUnoGame::class, 'game_id');
    }

    public function rater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rater_user_id');
    }

    public function rated(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rated_user_id');
    }
}
