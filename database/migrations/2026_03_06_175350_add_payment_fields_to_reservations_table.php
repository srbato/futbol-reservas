<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('payment_provider')->nullable()->after('verification_code');
            $table->string('payment_external_id')->nullable()->after('payment_provider');
            $table->string('payment_status')->nullable()->after('payment_external_id');
            $table->string('mp_preference_id')->nullable()->after('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'payment_provider',
                'payment_external_id',
                'payment_status',
                'mp_preference_id',
            ]);
        });
    }
};
