<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('field_exceptions', function (Blueprint $table) {
      $table->id();
      $table->foreignId('field_id')->constrained('fields')->cascadeOnDelete();
      $table->date('date');
      $table->boolean('is_closed')->default(false);
      $table->time('open_time')->nullable();
      $table->time('close_time')->nullable();
      $table->string('note')->nullable();
      $table->timestamps();

      $table->unique(['field_id', 'date']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('field_exceptions');
  }
};
