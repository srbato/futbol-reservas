<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_message_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['system_message_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_message_user');
    }
};
