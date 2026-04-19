<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\OrganizerPlanSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Organizer plans can be seeded in any environment
        $this->call(OrganizerPlanSeeder::class);

        if (app()->isProduction()) {
            $this->command->warn('Seeder deshabilitado en producción.');
            return;
        }

        // Segunda defensa: bloquear si el APP_URL parece productivo
        $appUrl = (string) config('app.url');
        $looksProd = !str_contains($appUrl, 'localhost')
            && !str_contains($appUrl, '127.0.0.1')
            && !str_contains($appUrl, '.ngrok')
            && !str_contains($appUrl, '.trycloudflare')
            && !str_contains($appUrl, '.test');
        if ($looksProd) {
            $this->command->error('Seeder bloqueado: APP_URL parece productivo (' . $appUrl . ')');
            $this->command->error('Si realmente querés seedear, cambiá APP_URL a algo local primero.');
            return;
        }

        // Super admin
        User::create([
            'name' => 'Santiago',
            'email' => 'srbattini@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Usuario1 - venue_admin
        $usuario1 = User::create([
            'name' => 'Usuario1',
            'email' => 'usuario1@test.com',
            'password' => Hash::make('password'),
            'role' => 'venue_admin',
            'is_active' => true,
            'email_verified_at' => now(),
            'onboarding_completed_at' => now(),
        ]);

        // Usuario2 - empleado de usuario1
        $usuario2 = User::create([
            'name' => 'Usuario2',
            'email' => 'usuario2@test.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Usuario3
        User::create([
            'name' => 'Usuario3',
            'email' => 'usuario3@test.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Usuario4
        User::create([
            'name' => 'Usuario4',
            'email' => 'usuario4@test.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Usuario5
        User::create([
            'name' => 'Usuario5',
            'email' => 'usuario5@test.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Venue de usuario1 para que usuario2 sea su empleado
        $venue = Venue::create([
            'owner_user_id' => $usuario1->id,
            'name' => 'Complejo de Usuario1',
            'address' => 'Av. Ejemplo 1234, Buenos Aires',
            'phone' => '1155551234',
            'is_active' => true,
        ]);

        // Usuario2 como staff del venue de usuario1
        DB::table('venue_staff')->insert([
            'venue_id' => $venue->id,
            'user_id' => $usuario2->id,
            'permissions' => json_encode(['manage_reservations', 'view_fields', 'manage_fields']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
