<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // 'platform' = cobrado con el token de TuCancha
            // 'venue'    = cobrado con el token propio del venue (marketplace)
            $table->string('payment_mp_token_owner')->nullable()->after('mp_preference_id');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('payment_mp_token_owner');
        });
    }
};
