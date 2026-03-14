<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Field extends Model
{
    protected $fillable = [
        'venue_id','name','sport','format','slot_minutes','is_indoor','is_active','cover_image_path'
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function price(): HasOne
    {
        return $this->hasOne(FieldPrice::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(FieldSchedule::class);
    }

    public function exceptions(): HasMany
    {
        return $this->hasMany(FieldException::class);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(FieldBlock::class);
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(FieldDiscount::class);
    }

    public function recurringDiscounts(): HasMany
    {
        return $this->hasMany(FieldRecurringDiscount::class);
    }

}