<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TournamentSetting extends Model
{
    protected $fillable = [
        'field_id', 'tournament_enabled', 'tournament_price_per_match',
        'available_days', 'available_start_time', 'available_end_time',
        'contact_method', 'contact_value', 'auto_approve', 'notes',
    ];

    protected $casts = [
        'tournament_enabled' => 'boolean',
        'auto_approve' => 'boolean',
        'tournament_price_per_match' => 'decimal:2',
        'available_days' => 'array',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function availableDaysDisplay(): string
    {
        if (!$this->available_days) return 'Todos los dias';
        $dayNames = ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'];
        return collect($this->available_days)->map(fn($d) => $dayNames[$d] ?? '?')->join(', ');
    }
}
