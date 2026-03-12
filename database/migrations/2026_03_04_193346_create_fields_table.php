<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('fields', function (Blueprint $table) {
      $table->id();

      $table->foreignId('venue_id')
            ->constrained('venues')
            ->cascadeOnDelete();

      $table->string('name');                 // "Cancha 1"
      $table->string('sport')->default('football');
      $table->unsignedSmallInteger('format')->nullable(); // 5/7/11
      $table->unsignedSmallInteger('slot_minutes')->default(60);

      $table->boolean('is_indoor')->default(false);
      $table->boolean('is_active')->default(true);

      $table->timestamps();

      $table->index(['venue_id', 'is_active']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('fields');
  }
};
