<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tournament_teams', function (Blueprint $table) {
            $table->boolean('payment_confirmed')->default(false);
            $table->datetime('payment_confirmed_at')->nullable();
            $table->string('payment_method')->nullable(); // 'manual', 'mercadopago'
            $table->string('payment_external_id')->nullable(); // MP payment ID
            $table->string('mp_preference_id')->nullable(); // MP preference ID for checkout
        });
    }

    public function down(): void
    {
        Schema::table('tournament_teams', function (Blueprint $table) {
            $table->dropColumn([
                'payment_confirmed',
                'payment_confirmed_at',
                'payment_method',
                'payment_external_id',
                'mp_preference_id',
            ]);
        });
    }
};
