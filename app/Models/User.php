<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use NotificationChannels\WebPush\HasPushSubscriptions;
// FaltaUnoSportProfile used via HasMany relation below

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasPushSubscriptions;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_active',
        'avatar_path',
        'google_id',
        'onboarding_completed_at',
        'age_group',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'      => 'datetime',
            'password'               => 'hashed',
            'is_active'              => 'boolean',
            'onboarding_completed_at' => 'datetime',
        ];
    }

    public function needsOnboarding(): bool
    {
        return $this->role === 'venue_admin' && is_null($this->onboarding_completed_at);
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

    public function faltaUnoSportProfiles(): HasMany
    {
        return $this->hasMany(FaltaUnoSportProfile::class);
    }

    public function faltaUnoPenalties(): HasMany
    {
        return $this->hasMany(FaltaUnoPenalty::class);
    }

    public function hasActiveCooldown(): bool
    {
        return $this->faltaUnoPenalties()->active()->cooldowns()->exists();
    }

    public function hasActiveBan(): bool
    {
        return $this->faltaUnoPenalties()->active()->bans()->exists();
    }

    public function recurringSubscriptions(): HasMany
    {
        return $this->hasMany(RecurringSubscription::class);
    }

    public function sportProfileFor(string $sport): ?FaltaUnoSportProfile
    {
        return $this->faltaUnoSportProfiles()->where('sport', $sport)->first();
    }

    public function venueStaffRecord(int $venueId): ?VenueStaff
    {
        return VenueStaff::where('user_id', $this->id)
            ->where('venue_id', $venueId)
            ->first();
    }

    public function activeStaffVenueId(): ?int
    {
        $record = VenueStaff::where('user_id', $this->id)->first();
        return $record?->venue_id;
    }

    public function organizerSubscription(): HasOne
    {
        return $this->hasOne(OrganizerSubscription::class)->latest();
    }

    public function activeOrganizerSubscription(): HasOne
    {
        return $this->hasOne(OrganizerSubscription::class)
            ->whereIn('status', ['ACTIVE', 'TRIAL'])
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->latest();
    }

    public function organizerTier(): string
    {
        $sub = $this->activeOrganizerSubscription;
        return ($sub && $sub->plan === 'pro') ? 'pro' : 'free';
    }

    public function organizedTournaments(): HasMany
    {
        return $this->hasMany(Tournament::class, 'organizer_user_id');
    }

    public function captainedTeams(): HasMany
    {
        return $this->hasMany(TournamentTeam::class, 'captain_user_id');
    }

    public function hasStaffPermission(string $permission, ?int $venueId): bool
    {
        if (in_array($this->role, ['super_admin', 'venue_admin'])) {
            return true;
        }

        if ($venueId === null) {
            return false;
        }

        $staff = $this->venueStaffRecord($venueId);

        if (!$staff || empty($staff->permissions) || !is_array($staff->permissions)) {
            return false;
        }

        return in_array($permission, $staff->permissions);
    }
}