<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TournamentVenueRequest extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COUNTER_PROPOSED = 'counter_proposed';

    protected $fillable = [
        'tournament_id', 'venue_id', 'field_id', 'requested_by',
        'proposed_dates', 'message', 'status', 'response_message',
        'counter_proposed_dates', 'responded_at', 'responded_by',
        'organizer_last_read_at', 'admin_last_read_at',
    ];

    protected $casts = [
        'proposed_dates' => 'array',
        'counter_proposed_dates' => 'array',
        'responded_at' => 'datetime',
        'organizer_last_read_at' => 'datetime',
        'admin_last_read_at' => 'datetime',
    ];

    public function tournament(): BelongsTo { return $this->belongsTo(Tournament::class); }
    public function venue(): BelongsTo { return $this->belongsTo(Venue::class); }
    public function field(): BelongsTo { return $this->belongsTo(Field::class); }
    public function requester(): BelongsTo { return $this->belongsTo(User::class, 'requested_by'); }
    public function responder(): BelongsTo { return $this->belongsTo(User::class, 'responded_by'); }

    public function messages(): HasMany
    {
        return $this->hasMany(TournamentRequestMessage::class, 'venue_request_id')->orderBy('created_at');
    }

    public function isPending(): bool { return $this->status === self::STATUS_PENDING; }
}
