<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE falta_uno_participants DROP CONSTRAINT IF EXISTS falta_uno_participants_status_check");
            DB::statement("ALTER TABLE falta_uno_participants ADD CONSTRAINT falta_uno_participants_status_check CHECK (status::text = ANY (ARRAY['confirmed', 'cancelled', 'no_show']))");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE falta_uno_participants DROP CONSTRAINT IF EXISTS falta_uno_participants_status_check");
            DB::statement("ALTER TABLE falta_uno_participants ADD CONSTRAINT falta_uno_participants_status_check CHECK (status::text = ANY (ARRAY['confirmed', 'cancelled']))");
        }
    }
};
