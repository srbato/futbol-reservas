<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('field_prices', function (Blueprint $table) {
            $table->decimal('night_price_per_slot', 12, 2)->nullable()->after('price_per_slot');
            $table->time('night_start_time')->nullable()->after('night_price_per_slot');
            $table->time('night_end_time')->nullable()->after('night_start_time');
        });
    }

    public function down(): void
    {
        Schema::table('field_prices', function (Blueprint $table) {
            $table->dropColumn(['night_price_per_slot', 'night_start_time', 'night_end_time']);
        });
    }
};
