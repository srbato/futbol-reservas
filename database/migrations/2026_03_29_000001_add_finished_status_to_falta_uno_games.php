<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * SQLite no soporta ALTER COLUMN para cambiar enums.
     * La columna status ya es string/enum — simplemente agregamos el nuevo valor
     * recreando la restricción (en SQLite no existe CHECK nativo heredado, la restricción
     * está solo en el nivel de aplicación). En MySQL habría que usar DB::statement.
     *
     * Como el proyecto usa SQLite, la columna enum se almacena como string, por lo que
     * no hace falta modificar la DDL — solo actualizamos el fillable / casts del modelo.
     * Igualmente creamos la migración para documentar el cambio y para compatibilidad MySQL.
     */
    public function up(): void
    {
        // En SQLite las columnas enum son varchar — no requieren modificación.
        // En MySQL sí hace falta alterar el enum para agregar 'finished'.
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE falta_uno_games MODIFY COLUMN status ENUM('open','full','cancelled','expired','finished') NOT NULL DEFAULT 'open'");
        }
        // SQLite: no requiere cambio de DDL — el valor string 'finished' ya es válido.
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE falta_uno_games MODIFY COLUMN status ENUM('open','full','cancelled','expired') NOT NULL DEFAULT 'open'");
        }
    }
};
