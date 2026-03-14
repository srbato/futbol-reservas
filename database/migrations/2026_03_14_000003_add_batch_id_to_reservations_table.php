<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->foreignId('batch_id')
                ->nullable()
                ->after('id')
                ->constrained('reservation_batches')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\ReservationBatch::class, 'batch_id');
            $table->dropColumn('batch_id');
        });
    }
};
