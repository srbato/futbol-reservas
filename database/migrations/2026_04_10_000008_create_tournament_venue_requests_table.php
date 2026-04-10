<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournament_venue_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->onDelete('cascade');
            $table->foreignId('venue_id')->constrained()->onDelete('cascade');
            $table->foreignId('field_id')->nullable()->constrained()->onDelete('set null');
            $table->unsignedBigInteger('requested_by');
            $table->foreign('requested_by')->references('id')->on('users');
            $table->json('proposed_dates')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected, counter_proposed
            $table->text('response_message')->nullable();
            $table->json('counter_proposed_dates')->nullable();
            $table->datetime('responded_at')->nullable();
            $table->unsignedBigInteger('responded_by')->nullable();
            $table->foreign('responded_by')->references('id')->on('users');
            $table->timestamps();

            $table->index(['tournament_id', 'status']);
            $table->index(['venue_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_venue_requests');
    }
};
