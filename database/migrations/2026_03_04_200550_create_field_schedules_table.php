<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('field_schedules', function (Blueprint $table) {
      $table->id();
      $table->foreignId('field_id')->constrained('fields')->cascadeOnDelete();
      $table->unsignedTinyInteger('day_of_week'); // 0=Dom ... 6=Sab
      $table->time('open_time');
      $table->time('close_time');
      $table->timestamps();

      $table->unique(['field_id', 'day_of_week']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('field_schedules');
  }
};

