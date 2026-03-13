<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->string('mp_access_token')->nullable()->after('is_active');
            $table->string('mp_refresh_token')->nullable()->after('mp_access_token');
            $table->unsignedBigInteger('mp_user_id')->nullable()->after('mp_refresh_token');
        });
    }

    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->dropColumn(['mp_access_token', 'mp_refresh_token', 'mp_user_id']);
        });
    }
};
