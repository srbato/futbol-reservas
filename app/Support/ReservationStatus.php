<?php

namespace App\Support;

class ReservationStatus
{
    public static function label(string $status): string
    {
        return match ($status) {
            'PENDING_PAYMENT' => 'Pendiente de pago',
            'PAID' => 'Pagada',
            'CHECKED_IN' => 'Validada',
            'CANCELLED' => 'Cancelada',
            'EXPIRED' => 'Expirada',
            default => $status,
        };
    }

    public static function color(string $status): string
    {
        return match ($status) {
            'PENDING_PAYMENT' => 'background:#fff3cd; color:#856404;',
            'PAID' => 'background:#d1e7dd; color:#0f5132;',
            'CHECKED_IN' => 'background:#cfe2ff; color:#084298;',
            'CANCELLED' => 'background:#e2e3e5; color:#41464b;',
            'EXPIRED' => 'background:#f8d7da; color:#842029;',
            default => 'background:#f3f3f3; color:#666;',
        };
    }
}