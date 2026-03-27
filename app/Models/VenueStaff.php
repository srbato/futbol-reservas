<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenueStaff extends Model
{
    protected $fillable = ['venue_id', 'user_id', 'permissions'];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
