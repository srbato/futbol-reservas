<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venue_admin_subscriptions', function (Blueprint $table) {
            $table->timestamp('reminder_sent_7d_at')->nullable()->after('expires_at');
            $table->timestamp('reminder_sent_2d_at')->nullable()->after('reminder_sent_7d_at');
            $table->timestamp('reminder_sent_0d_at')->nullable()->after('reminder_sent_2d_at');
        });
    }

    public function down(): void
    {
        Schema::table('venue_admin_subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'reminder_sent_7d_at',
                'reminder_sent_2d_at',
                'reminder_sent_0d_at',
            ]);
        });
    }
};
