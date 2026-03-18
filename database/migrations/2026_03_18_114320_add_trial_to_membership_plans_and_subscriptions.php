<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->unsignedTinyInteger('trial_days')->default(0)->after('long_term_months');
        });

        Schema::table('venue_admin_subscriptions', function (Blueprint $table) {
            $table->timestamp('trial_ends_at')->nullable()->after('expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->dropColumn('trial_days');
        });

        Schema::table('venue_admin_subscriptions', function (Blueprint $table) {
            $table->dropColumn('trial_ends_at');
        });
    }
};
