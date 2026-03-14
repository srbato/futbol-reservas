<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venue_admin_subscriptions', function (Blueprint $table) {
            $table->string('plan_slug', 50)->nullable()->after('user_id');
            $table->string('billing_cycle', 20)->default('monthly')->after('plan_slug');
        });
    }

    public function down(): void
    {
        Schema::table('venue_admin_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['plan_slug', 'billing_cycle']);
        });
    }
};
