<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mapa enum → edad aproximada para usuarios (valor representativo del grupo).
     */
    private array $userMap = [
        'sub10' => 9,  'sub12' => 11, 'sub14' => 13, 'sub16' => 15, 'sub18' => 17,
        '19a25' => 22, '26a34' => 30, 'open' => null, 'mas35' => 37, 'mas40' => 42,
        'mas45' => 47, 'mas50' => 52, 'mas55' => 57, 'mas60' => 62,
    ];

    /** Mapa enum → límite inferior del grupo (para age_min de partidos). */
    private array $minMap = [
        'sub10' => 5,  'sub12' => 10, 'sub14' => 12, 'sub16' => 14, 'sub18' => 16,
        '19a25' => 19, '26a34' => 26, 'open' => null, 'mas35' => 35, 'mas40' => 40,
        'mas45' => 45, 'mas50' => 50, 'mas55' => 55, 'mas60' => 60,
    ];

    /** Mapa enum → límite superior del grupo (para age_max de partidos). */
    private array $maxMap = [
        'sub10' => 9,  'sub12' => 11, 'sub14' => 13, 'sub16' => 15, 'sub18' => 18,
        '19a25' => 25, '26a34' => 34, 'open' => null, 'mas35' => 99, 'mas40' => 99,
        'mas45' => 99, 'mas50' => 99, 'mas55' => 99, 'mas60' => 99,
    ];

    public function up(): void
    {
        // ── USERS ─────────────────────────────────────────────────────────
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('age')->nullable()->after('phone');
        });

        foreach (DB::table('users')->whereNotNull('age_group')->get() as $u) {
            $age = $this->userMap[$u->age_group] ?? null;
            if ($age !== null) {
                DB::table('users')->where('id', $u->id)->update(['age' => $age]);
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('age_group');
        });

        // ── FALTA_UNO_GAMES ───────────────────────────────────────────────
        Schema::table('falta_uno_games', function (Blueprint $table) {
            $table->unsignedTinyInteger('age_min')->nullable()->after('age_group_max');
            $table->unsignedTinyInteger('age_max')->nullable()->after('age_min');
        });

        foreach (DB::table('falta_uno_games')->get() as $g) {
            $ageMin = $g->age_group_min ? ($this->minMap[$g->age_group_min] ?? null) : null;
            $ageMax = $g->age_group_max ? ($this->maxMap[$g->age_group_max] ?? null) : null;
            if ($ageMin !== null || $ageMax !== null) {
                DB::table('falta_uno_games')->where('id', $g->id)->update([
                    'age_min' => $ageMin,
                    'age_max' => $ageMax,
                ]);
            }
        }

        Schema::table('falta_uno_games', function (Blueprint $table) {
            $table->dropColumn(['age_group_min', 'age_group_max']);
        });
    }

    public function down(): void
    {
        // Restaurar columnas enum (sin recuperar el valor exacto original)
        Schema::table('users', function (Blueprint $table) {
            $table->string('age_group')->nullable()->after('phone');
            $table->dropColumn('age');
        });

        Schema::table('falta_uno_games', function (Blueprint $table) {
            $table->string('age_group_min')->nullable()->after('category_max');
            $table->string('age_group_max')->nullable()->after('age_group_min');
            $table->dropColumn(['age_min', 'age_max']);
        });
    }
};
