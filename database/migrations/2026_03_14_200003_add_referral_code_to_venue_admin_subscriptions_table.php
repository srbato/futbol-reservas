<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venue_admin_subscriptions', function (Blueprint $table) {
            $table->string('referral_code_used')->nullable()->after('billing_cycle');
        });
    }

    public function down(): void
    {
        Schema::table('venue_admin_subscriptions', function (Blueprint $table) {
            $table->dropColumn('referral_code_used');
        });
    }
};
