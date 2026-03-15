<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('match_outcome', 1)->nullable(); // W, D, L
            $table->string('match_result', 100)->nullable(); // free text score
            $table->timestamps();

            $table->unique(['reservation_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_results');
    }
};
