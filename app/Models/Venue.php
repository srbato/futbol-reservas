<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Field;
use App\Models\Reservation;


class Venue extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'owner_user_id',
        'name',
        'description',
        'cover_image_path',
        'gallery_paths',
        'address',
        'zone',
        'phone',
        'lat',
        'lng',
        'is_active',
        'cancellation_hours',
        'modification_hours',
        'recurring_payment_mode',
        'amenities',
        'accepts_cash_payment',
    ];

    protected $hidden = [
        'mp_access_token',
        'mp_refresh_token',
        'mp_user_id',
    ];

    protected $casts = [
        'mp_access_token'  => 'encrypted',
        'mp_refresh_token' => 'encrypted',
        'is_active'              => 'boolean',
        'accepts_cash_payment'   => 'boolean',
        'amenities'              => 'array',
        'gallery_paths'          => 'array',
        'cancellation_hours'  => 'integer',
        'modification_hours'  => 'integer',
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

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    /**
     * Scope: venues que el usuario puede administrar (dueño o staff).
     * Super admin no necesita este scope — tiene acceso global.
     */
    public function scopeAccessibleBy(Builder $query, User $user): Builder
    {
        if ($user->role === 'super_admin') {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->where('owner_user_id', $user->id)
              ->orWhereHas('staff', fn ($sq) => $sq->where('users.id', $user->id));
        });
    }

    public function staff()
    {
        return $this->belongsToMany(User::class, 'venue_staff')
            ->withPivot('id', 'permissions')
            ->withTimestamps();
    }

    public function staffInvitations()
    {
        return $this->hasMany(VenueStaffInvitation::class);
    }

    /**
     * Scope: solo venues cuyo dueño tiene suscripción activa (o es super_admin).
     */
    public function scopeWithActiveOwner(Builder $query): Builder
    {
        return $query->whereHas('owner', function ($q) {
            $q->where('role', 'super_admin')
              ->orWhereHas('venueAdminSubscriptions', function ($sq) {
                  $sq->whereIn('status', ['ACTIVE', 'TRIAL'])
                     ->whereNotNull('starts_at')
                     ->where('starts_at', '<=', now())
                     ->whereNotNull('expires_at')
                     ->where('expires_at', '>', now());
              });
        });
    }

    /**
     * Devuelve el slug del plan activo del owner (starter, pro, full).
     * Si es super_admin o no tiene plan, devuelve 'full' o 'starter' respectivamente.
     */
    public function getOwnerPlanSlugAttribute(): string
    {
        if ($this->relationLoaded('owner') && $this->owner) {
            if ($this->owner->role === 'super_admin') {
                return 'full';
            }

            $activeSub = $this->owner->relationLoaded('venueAdminSubscriptions')
                ? $this->owner->venueAdminSubscriptions
                    ->filter(function ($sub) {
                        return in_array($sub->status, ['ACTIVE', 'TRIAL'])
                            && $sub->starts_at && $sub->starts_at <= now()
                            && $sub->expires_at && $sub->expires_at > now();
                    })
                    ->sortByDesc('expires_at')
                    ->first()
                : $this->owner->activeVenueAdminSubscription()->first();

            return $activeSub?->plan_slug ?? 'starter';
        }

        return 'starter';
    }

    /**
     * Devuelve el sort_order numérico del plan del owner para ordenamiento.
     * Full=3 (primero), Pro=2 (segundo), Starter=1 (último).
     */
    public function getOwnerPlanSortOrderAttribute(): int
    {
        return match ($this->owner_plan_slug) {
            'full' => 3,
            'pro' => 2,
            default => 1,
        };
    }
}
