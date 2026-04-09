<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->text('mp_access_token')->nullable()->change();
            $table->text('mp_refresh_token')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->string('mp_access_token', 255)->nullable()->change();
            $table->string('mp_refresh_token', 255)->nullable()->change();
        });
    }
};
