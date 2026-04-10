<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournament_request_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_request_id')->constrained('tournament_venue_requests')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('message');
            $table->timestamps();

            $table->index(['venue_request_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_request_messages');
    }
};
