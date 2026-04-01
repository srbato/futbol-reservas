<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('onboarding_completed_at')->nullable()->after('role');
        });

        // Marcar como completado para venue_admins que ya tienen venues con fields
        DB::table('users')
            ->where('role', 'venue_admin')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('venues')
                    ->whereColumn('venues.owner_user_id', 'users.id')
                    ->whereExists(function ($q2) {
                        $q2->select(DB::raw(1))
                            ->from('fields')
                            ->whereColumn('fields.venue_id', 'venues.id')
                            ->whereNull('fields.deleted_at');
                    })
                    ->whereNull('venues.deleted_at');
            })
            ->update(['onboarding_completed_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('onboarding_completed_at');
        });
    }
};
