<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('field_prices', function (Blueprint $table) {
      $table->id();
      $table->foreignId('field_id')->constrained('fields')->cascadeOnDelete();
      $table->decimal('price_per_slot', 12, 2);
      $table->string('currency', 3)->default('ARS');
      $table->timestamps();

      $table->unique('field_id');
    });
  }

  public function down(): void {
    Schema::dropIfExists('field_prices');
  }
};