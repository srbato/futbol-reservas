<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournament_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->unique()->constrained()->onDelete('cascade');
            $table->boolean('tournament_enabled')->default(false);
            $table->decimal('tournament_price_per_match', 12, 2)->nullable();
            $table->json('available_days')->nullable(); // [0,6] for sat/sun
            $table->time('available_start_time')->nullable();
            $table->time('available_end_time')->nullable();
            $table->string('contact_method')->default('whatsapp'); // whatsapp, phone, email, internal
            $table->string('contact_value')->nullable();
            $table->boolean('auto_approve')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_settings');
    }
};
