<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('modify_mp_preference_id')->nullable()->after('modification_status');
            $table->decimal('modify_diff_amount', 10, 2)->nullable()->after('modify_mp_preference_id');
            $table->unsignedBigInteger('modify_field_id')->nullable()->after('modify_diff_amount');
            $table->datetime('modify_start_at')->nullable()->after('modify_field_id');

            $table->foreign('modify_field_id')->references('id')->on('fields')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['modify_field_id']);
            $table->dropColumn([
                'modify_mp_preference_id',
                'modify_diff_amount',
                'modify_field_id',
                'modify_start_at',
            ]);
        });
    }
};
