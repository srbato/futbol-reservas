<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('falta_uno_games', function (Blueprint $table) {
            $table->string('age_group_min')->nullable()->after('category_max');
            $table->string('age_group_max')->nullable()->after('age_group_min');
        });
    }

    public function down(): void
    {
        Schema::table('falta_uno_games', function (Blueprint $table) {
            $table->dropColumn(['age_group_min', 'age_group_max']);
        });
    }
};
