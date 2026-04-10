<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tournament_venue_requests', function (Blueprint $table) {
            $table->timestamp('organizer_last_read_at')->nullable();
            $table->timestamp('admin_last_read_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tournament_venue_requests', function (Blueprint $table) {
            $table->dropColumn(['organizer_last_read_at', 'admin_last_read_at']);
        });
    }
};
