<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // 'W' = ganamos, 'D' = empatamos, 'L' = perdimos, null = sin resultado
            $table->string('match_outcome', 1)->nullable()->after('match_result');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('match_outcome');
        });
    }
};
