<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'avatar_path',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function favoriteVenues()
    {
        return $this->belongsToMany(Venue::class, 'favorite_venues')
            ->withTimestamps();
    }

    public function venueReviews()
    {
        return $this->hasMany(VenueReview::class);
    }

    public function venueAdminSubscriptions(): HasMany
    {
        return $this->hasMany(VenueAdminSubscription::class);
    }

    public function activeVenueAdminSubscription()
    {
        return $this->venueAdminSubscriptions()
            ->whereIn('status', ['ACTIVE', 'TRIAL'])
            ->whereNotNull('starts_at')
            ->where('starts_at', '<=', now())
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->latest('expires_at');
    }

    public function hasActiveVenueAdminSubscription(): bool
    {
        return $this->activeVenueAdminSubscription()->exists();
    }

    public function referralCode(): HasOne
    {
        return $this->hasOne(ReferralCode::class);
    }

    public function referralRewards(): HasMany
    {
        return $this->hasMany(ReferralReward::class, 'referrer_user_id');
    }

    public function availableReferralRewards(): HasMany
    {
        return $this->referralRewards()->where('status', 'available');
    }

    public function reservationPlayerEntries(): HasMany
    {
        return $this->hasMany(ReservationPlayer::class);
    }

    public function venuesAsStaff()
    {
        return $this->belongsToMany(Venue::class, 'venue_staff')->withTimestamps();
    }

    public function isStaffOf(int $venueId): bool
    {
        return $this->venuesAsStaff()->where('venue_id', $venueId)->exists();
    }

    public function isVenueStaff(): bool
    {
        return $this->venuesAsStaff()->exists();
    }

    public function pendingStaffInvitations()
    {
        return $this->hasMany(VenueStaffInvitation::class)->where('status', 'pending');
    }
}