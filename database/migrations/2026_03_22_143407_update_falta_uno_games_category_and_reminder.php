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
            $table->dropColumn('category_filter');
            $table->string('category_min')->nullable()->after('gender_filter');
            $table->string('category_max')->nullable()->after('category_min');
            $table->timestamp('reminder_sent_at')->nullable()->after('category_max');
        });
    }

    public function down(): void
    {
        Schema::table('falta_uno_games', function (Blueprint $table) {
            $table->dropColumn(['category_min', 'category_max', 'reminder_sent_at']);
            $table->string('category_filter')->nullable()->after('gender_filter');
        });
    }
};
