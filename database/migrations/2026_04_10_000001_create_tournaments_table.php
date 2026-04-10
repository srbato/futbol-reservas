<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->foreignId('organizer_user_id')->constrained('users')->cascadeOnDelete();

            // Cancha manual
            $table->string('external_venue_name');
            $table->string('external_venue_address')->nullable();
            $table->string('external_venue_contact')->nullable();

            // Config
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('sport'); // football, padel, tennis, basketball, volleyball
            $table->string('format')->default('single_elimination');
            $table->unsignedInteger('max_teams');
            $table->unsignedInteger('players_per_team');
            $table->string('gender_filter')->default('mixed');
            $table->string('category')->nullable();
            $table->decimal('inscription_price', 12, 2)->nullable();
            $table->date('estimated_start_date');
            $table->date('actual_start_date')->nullable();
            $table->text('rules')->nullable();
            $table->string('cover_image_path')->nullable();

            // Estado
            $table->string('status')->default('draft');
            $table->timestamp('registration_deadline')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancel_reason')->nullable();

            $table->timestamps();
            $table->index(['organizer_user_id']);
            $table->index(['status']);
            $table->index(['sport', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
