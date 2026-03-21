<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FaltaUnoGame extends Model
{
    protected $fillable = [
        'field_id',
        'reservation_id',
        'initiator_user_id',
        'total_players',
        'initiator_players',
        'players_needed',
        'status',
        'start_at',
        'amount_paid',
        'cancelled_at',
    ];

    protected $casts = [
        'start_at'     => 'datetime',
        'cancelled_at' => 'datetime',
        'amount_paid'  => 'decimal:2',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiator_user_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(FaltaUnoParticipant::class, 'game_id');
    }

    public function activeParticipants(): HasMany
    {
        return $this->hasMany(FaltaUnoParticipant::class, 'game_id')
            ->where('status', 'confirmed');
    }

    /** Jugadores confirmados = los del iniciador + los que se unieron */
    public function confirmedCount(): int
    {
        return $this->initiator_players + $this->activeParticipants()->count();
    }

    /** Cuántos faltan todavía */
    public function remainingSlots(): int
    {
        return max(0, $this->players_needed - $this->activeParticipants()->count());
    }

    /** Si el partido ya está completo */
    public function isFull(): bool
    {
        return $this->activeParticipants()->count() >= $this->players_needed;
    }

    /** Si todavía se puede cancelar con reembolso */
    public function canRefund(): bool
    {
        $setting = $this->field->faltaUnoSetting;
        if (!$setting) return false;
        $deadline = $this->start_at->copy()->subMinutes($setting->refund_deadline_minutes);
        return now()->lessThan($deadline);
    }

    /** Si el partido ya expiró por tiempo (no se llenó a tiempo) */
    public function hasPassedFillDeadline(): bool
    {
        $setting = $this->field->faltaUnoSetting;
        if (!$setting) return false;
        $deadline = $this->start_at->copy()->subMinutes($setting->fill_deadline_minutes);
        return now()->greaterThanOrEqualTo($deadline);
    }
}
