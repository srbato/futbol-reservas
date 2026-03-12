<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venue_reviews', function (Blueprint $table) {
            $table->dropUnique(['venue_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('venue_reviews', function (Blueprint $table) {
            $table->unique(['venue_id', 'user_id']);
        });
    }
};

