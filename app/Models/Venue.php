<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Models\Field;
use App\Models\Reservation;


class Venue extends Model
{
    protected $fillable = [
        'owner_user_id',
        'name',
        'description',
        'cover_image_path',
        'address',
        'zone',
        'lat',
        'lng',
        'is_active'
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class);
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorite_venues')
            ->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(VenueReview::class);
    }

    public function reservations(): HasManyThrough
    {
        return $this->hasManyThrough(
            Reservation::class,
            Field::class,
            'venue_id', // Foreign key en fields...
            'field_id', // Foreign key en reservations...
            'id',       // Local key en venues...
            'id'        // Local key en fields...
        );
    }
}
