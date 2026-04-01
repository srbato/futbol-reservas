<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->unsignedBigInteger('recurring_subscription_id')->nullable()->after('batch_id');
            $table->foreign('recurring_subscription_id')
                ->references('id')
                ->on('recurring_subscriptions')
                ->nullOnDelete();
            $table->index('recurring_subscription_id');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['recurring_subscription_id']);
            $table->dropIndex(['recurring_subscription_id']);
            $table->dropColumn('recurring_subscription_id');
        });
    }
};
