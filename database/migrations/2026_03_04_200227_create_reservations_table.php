<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('reservations', function (Blueprint $table) {
      $table->id();
      $table->foreignId('field_id')->constrained('fields')->cascadeOnDelete();
      $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

      $table->dateTime('start_at');
      $table->dateTime('end_at');

      $table->string('status', 30); // PENDING_PAYMENT, PAID, CANCELLED, EXPIRED, CHECKED_IN
      $table->decimal('total_amount', 12, 2)->default(0);
      $table->string('currency', 3)->default('ARS');

      $table->string('verification_code', 32)->nullable();
      $table->dateTime('expires_at')->nullable();

      $table->timestamps();

      $table->index(['field_id', 'start_at']);
      $table->index(['user_id', 'start_at']);
      $table->index(['status', 'expires_at']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('reservations');
  }
};
