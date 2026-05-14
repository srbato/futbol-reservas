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
        Schema::table('falta_uno_games', function (Blueprint $table) {
            $table->text('message')->nullable()->after('age_group_max');
            $table->boolean('is_private')->default(false)->after('message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('falta_uno_games', function (Blueprint $table) {
            $table->dropColumn(['message', 'is_private']);
        });
    }
};
