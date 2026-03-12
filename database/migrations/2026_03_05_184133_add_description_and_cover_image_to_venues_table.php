<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('venues', function (Blueprint $table) {
      $table->string('cover_image_path')->nullable()->after('name');
      $table->string('description', 255)->nullable()->after('cover_image_path');
    });
  }

  public function down(): void
  {
    Schema::table('venues', function (Blueprint $table) {
      $table->dropColumn(['cover_image_path','description']);
    });
  }
};
