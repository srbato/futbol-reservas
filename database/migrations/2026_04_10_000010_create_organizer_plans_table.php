<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizer_plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->decimal('monthly_price', 12, 2)->default(0);
            $table->integer('max_tournaments')->default(1);
            $table->integer('max_teams_per_tournament')->default(8);
            $table->json('available_formats')->nullable();
            $table->boolean('has_stats')->default(false);
            $table->boolean('has_notifications')->default(false);
            $table->boolean('has_custom_branding')->default(false);
            $table->boolean('has_mp_payments')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizer_plans');
    }
};
