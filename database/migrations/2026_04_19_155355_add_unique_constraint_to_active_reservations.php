<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Partial unique index (Postgres): bloquea doble reserva PAID/PENDING_PAYMENT/PENDING_CASH
        // para (field_id, start_at). Evita race conditions a nivel DB, complemento del lock en ReservationService.
        // Ignora reservas canceladas/expiradas que ya liberaron el turno.
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'pgsql') {
            \Illuminate\Support\Facades\DB::statement(
                'CREATE UNIQUE INDEX IF NOT EXISTS reservations_field_active_slot_unique
                 ON reservations (field_id, start_at)
                 WHERE status IN (\'PAID\', \'PENDING_PAYMENT\', \'PENDING_CASH\')'
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'pgsql') {
            \Illuminate\Support\Facades\DB::statement('DROP INDEX IF EXISTS reservations_field_active_slot_unique');
        }
    }
};
