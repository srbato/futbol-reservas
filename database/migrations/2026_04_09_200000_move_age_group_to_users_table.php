<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Agregar columna age_group a users
        Schema::table('users', function (Blueprint $table) {
            $table->string('age_group')->nullable()->after('phone');
        });

        // 2. Migrar datos existentes: copiar age_group de sport_profiles al user
        $profiles = DB::table('falta_uno_sport_profiles')
            ->whereNotNull('age_group')
            ->where('age_group', '!=', '')
            ->get();

        foreach ($profiles as $profile) {
            DB::table('users')
                ->where('id', $profile->user_id)
                ->whereNull('age_group')
                ->update(['age_group' => $profile->age_group]);
        }

        // 3. Eliminar columna age_group de sport_profiles
        Schema::table('falta_uno_sport_profiles', function (Blueprint $table) {
            $table->dropColumn('age_group');
        });
    }

    public function down(): void
    {
        // Restaurar columna en sport_profiles
        Schema::table('falta_uno_sport_profiles', function (Blueprint $table) {
            $table->string('age_group')->nullable()->after('gender');
        });

        // Copiar datos de vuelta (mejor esfuerzo)
        $users = DB::table('users')
            ->whereNotNull('age_group')
            ->get();

        foreach ($users as $user) {
            DB::table('falta_uno_sport_profiles')
                ->where('user_id', $user->id)
                ->update(['age_group' => $user->age_group]);
        }

        // Eliminar columna de users
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('age_group');
        });
    }
};
