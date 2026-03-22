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
            $table->enum('gender_filter', ['male', 'female', 'mixed'])->default('mixed')->after('status');
            $table->string('category_min')->nullable()->after('gender_filter');
            $table->string('category_max')->nullable()->after('category_min');
            $table->timestamp('reminder_sent_at')->nullable()->after('category_max');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('falta_uno_games', function (Blueprint $table) {
            $table->dropColumn(['gender_filter', 'category_min', 'category_max', 'reminder_sent_at']);
        });
    }
};
