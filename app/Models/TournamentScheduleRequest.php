<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TournamentScheduleRequest extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'tournament_id', 'venue_request_id', 'field_id',
        'slots', 'status', 'response_message',
        'responded_by', 'responded_at',
    ];

    protected $casts = [
        'slots' => 'array',
        'responded_at' => 'datetime',
    ];

    public function tournament(): BelongsTo { return $this->belongsTo(Tournament::class); }
    public function venueRequest(): BelongsTo { return $this->belongsTo(TournamentVenueRequest::class, 'venue_request_id'); }
    public function field(): BelongsTo { return $this->belongsTo(Field::class); }
    public function responder(): BelongsTo { return $this->belongsTo(User::class, 'responded_by'); }

    public function isPending(): bool { return $this->status === self::STATUS_PENDING; }
    public function isApproved(): bool { return $this->status === self::STATUS_APPROVED; }
    public function isRejected(): bool { return $this->status === self::STATUS_REJECTED; }
}
