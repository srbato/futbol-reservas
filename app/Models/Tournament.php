<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Tournament extends Model
{
    const STATUS_DRAFT = 'draft';
    const STATUS_OPEN_REGISTRATION = 'open_registration';
    const STATUS_REGISTRATION_CLOSED = 'registration_closed';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_FINISHED = 'finished';
    const STATUS_CANCELLED = 'cancelled';
    const VALID_STATUSES = [
        'draft',
        'open_registration',
        'registration_closed',
        'in_progress',
        'finished',
        'cancelled',
    ];

    const VALID_SPORTS = ['football', 'padel', 'tennis', 'basketball', 'volleyball'];

    protected $fillable = [
        'slug',
        'organizer_user_id',
        'external_venue_name',
        'external_venue_address',
        'external_venue_contact',
        'name',
        'description',
        'sport',
        'format',
        'max_teams',
        'players_per_team',
        'gender_filter',
        'category',
        'inscription_price',
        'estimated_start_date',
        'actual_start_date',
        'rules',
        'cover_image_path',
        'status',
        'registration_deadline',
        'cancelled_at',
        'cancel_reason',
        'venue_id',
        'field_id',
        'venue_approval_status',
        'venue_approved_at',
        'venue_rejection_reason',
    ];

    protected $casts = [
        'estimated_start_date' => 'date',
        'actual_start_date'    => 'date',
        'registration_deadline' => 'datetime',
        'cancelled_at'         => 'datetime',
        'inscription_price'    => 'decimal:2',
        'venue_approved_at'    => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Tournament $tournament) {
            if (empty($tournament->slug)) {
                $base = Str::slug($tournament->name);
                $slug = $base;
                $counter = 2;

                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $counter;
                    $counter++;
                }

                $tournament->slug = $slug;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ── Relaciones ──────────────────────────────────────────

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_user_id');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(TournamentTeam::class);
    }

    public function confirmedTeams(): HasMany
    {
        return $this->hasMany(TournamentTeam::class)
            ->where('status', TournamentTeam::STATUS_CONFIRMED);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(TournamentMatch::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function fields(): BelongsToMany
    {
        return $this->belongsToMany(Field::class, 'tournament_fields')->withTimestamps();
    }

    public function venueRequests(): HasMany
    {
        return $this->hasMany(TournamentVenueRequest::class);
    }

    /**
     * Recalculate venue_approval_status from all venue requests.
     */
    public function recalculateApprovalStatus(): void
    {
        $requests = $this->venueRequests()->get();

        if ($requests->isEmpty()) {
            $this->update(['venue_approval_status' => null]);
            return;
        }

        $allApproved = $requests->every(fn($r) => $r->status === TournamentVenueRequest::STATUS_APPROVED);
        $anyRejected = $requests->contains(fn($r) => $r->status === TournamentVenueRequest::STATUS_REJECTED);
        $anyPending = $requests->contains(fn($r) => $r->status === TournamentVenueRequest::STATUS_PENDING);

        if ($allApproved) {
            $this->update([
                'venue_approval_status' => 'approved',
                'venue_approved_at' => now(),
            ]);
        } elseif ($anyRejected) {
            $rejectedReq = $requests->firstWhere('status', TournamentVenueRequest::STATUS_REJECTED);
            $this->update([
                'venue_approval_status' => 'rejected',
                'venue_rejection_reason' => $rejectedReq->response_message,
            ]);
        } elseif ($anyPending) {
            $this->update(['venue_approval_status' => 'pending']);
        }
    }

    // ── Métodos de estado ───────────────────────────────────

    public function isOrganizer(User $user): bool
    {
        return $this->organizer_user_id === $user->id;
    }

    public function isFull(): bool
    {
        return $this->confirmedTeams()->count() >= $this->max_teams;
    }

    public function canRegister(): bool
    {
        if ($this->status !== self::STATUS_OPEN_REGISTRATION) {
            return false;
        }

        if ($this->isFull()) {
            return false;
        }

        if ($this->registration_deadline && $this->registration_deadline->isPast()) {
            return false;
        }

        return true;
    }

    public function hasStarted(): bool
    {
        return in_array($this->status, [self::STATUS_IN_PROGRESS, self::STATUS_FINISHED]);
    }

    public function isFinished(): bool
    {
        return $this->status === self::STATUS_FINISHED;
    }
}
