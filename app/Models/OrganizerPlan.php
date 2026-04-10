<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizerPlan extends Model
{
    protected $fillable = [
        'slug', 'name', 'monthly_price', 'max_tournaments', 'max_teams_per_tournament',
        'available_formats', 'has_stats', 'has_notifications', 'has_custom_branding',
        'has_mp_payments', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'available_formats' => 'array',
        'has_stats' => 'boolean',
        'has_notifications' => 'boolean',
        'has_custom_branding' => 'boolean',
        'has_mp_payments' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function isUnlimited(): bool
    {
        return $this->max_tournaments === -1;
    }
}
