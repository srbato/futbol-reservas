<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenueStaffInvitation extends Model
{
    protected $fillable = ['venue_id', 'invited_by', 'user_id', 'token', 'status', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function invitedBy()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
