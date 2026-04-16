<?php

namespace App\Support;

class ReservationStatus
{
    public static function label(string $status): string
    {
        return match ($status) {
            'PENDING_PAYMENT' => 'Pendiente de pago',
            'PENDING_CASH' => 'Pago en el complejo',
            'PAID' => 'Pagada',
            'CHECKED_IN' => 'Check-in realizado',
            'CANCELLED' => 'Cancelada',
            'EXPIRED' => 'Expirada',
            default => $status,
        };
    }

    public static function color(string $status): string
    {
        return match ($status) {
            'PENDING_PAYMENT' => 'background:#fff3cd; color:#856404;',
            'PENDING_CASH' => 'background:#e0e7ff; color:#3730a3;',
            'PAID' => 'background:#d1e7dd; color:#0f5132;',
            'CHECKED_IN' => 'background:#cff4fc; color:#055160;',
            'CANCELLED' => 'background:#e2e3e5; color:#41464b;',
            'EXPIRED' => 'background:#f8d7da; color:#842029;',
            default => 'background:#f3f3f3; color:#666;',
        };
    }
}