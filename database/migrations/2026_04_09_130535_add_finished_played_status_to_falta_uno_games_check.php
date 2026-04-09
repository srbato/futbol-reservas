<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE falta_uno_games DROP CONSTRAINT IF EXISTS falta_uno_games_status_check");
            DB::statement("ALTER TABLE falta_uno_games ADD CONSTRAINT falta_uno_games_status_check CHECK (status::text = ANY (ARRAY['open', 'full', 'cancelled', 'expired', 'finished', 'played']))");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE falta_uno_games DROP CONSTRAINT IF EXISTS falta_uno_games_status_check");
            DB::statement("ALTER TABLE falta_uno_games ADD CONSTRAINT falta_uno_games_status_check CHECK (status::text = ANY (ARRAY['open', 'full', 'cancelled', 'expired']))");
        }
    }
};
