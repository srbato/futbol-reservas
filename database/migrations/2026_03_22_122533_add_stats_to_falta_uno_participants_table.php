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
        Schema::table('falta_uno_participants', function (Blueprint $table) {
            $table->unsignedInteger('goals')->nullable()->after('status');
            $table->unsignedInteger('assists')->nullable()->after('goals');
            $table->enum('result', ['win', 'draw', 'loss'])->nullable()->after('assists');
            $table->timestamp('stats_submitted_at')->nullable()->after('result');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('falta_uno_participants', function (Blueprint $table) {
            $table->dropColumn(['goals', 'assists', 'result', 'stats_submitted_at']);
        });
    }
};
