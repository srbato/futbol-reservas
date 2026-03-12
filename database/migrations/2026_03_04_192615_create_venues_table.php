<?php

// database/migrations/xxxx_xx_xx_create_venues_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('venues', function (Blueprint $table) {
      $table->id();
      $table->foreignId('owner_user_id')->constrained('users')->cascadeOnDelete();
      $table->string('name');
      $table->string('address')->nullable();
      $table->string('zone')->nullable();
      $table->boolean('is_active')->default(true);
      $table->timestamps();

      $table->index(['owner_user_id', 'is_active']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('venues');
  }
};
