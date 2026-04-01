<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar campos a falta_uno_participants
        Schema::table('falta_uno_participants', function (Blueprint $table) {
            $table->boolean('is_late_leave')->default(false)->after('status');
            $table->timestamp('left_at')->nullable()->after('is_late_leave');
        });

        // Agregar campos a falta_uno_sport_profiles
        Schema::table('falta_uno_sport_profiles', function (Blueprint $table) {
            $table->decimal('attendance_rate', 5, 2)->default(100.00)->after('average_rating');
            $table->unsignedInteger('late_leaves_count')->default(0)->after('attendance_rate');
        });

        // Agregar campo a falta_uno_settings
        Schema::table('falta_uno_settings', function (Blueprint $table) {
            $table->unsignedInteger('late_leave_deadline_minutes')->default(240)->after('fill_deadline_minutes');
        });

        // Crear tabla de penalidades
        Schema::create('falta_uno_penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('sport');
            $table->string('type'); // cooldown o ban
            $table->string('reason')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('falta_uno_penalties');

        Schema::table('falta_uno_settings', function (Blueprint $table) {
            $table->dropColumn('late_leave_deadline_minutes');
        });

        Schema::table('falta_uno_sport_profiles', function (Blueprint $table) {
            $table->dropColumn(['attendance_rate', 'late_leaves_count']);
        });

        Schema::table('falta_uno_participants', function (Blueprint $table) {
            $table->dropColumn(['is_late_leave', 'left_at']);
        });
    }
};
