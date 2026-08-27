<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\FaltaUnoGame;
use App\Models\FaltaUnoParticipant;
use App\Models\FaltaUnoSetting;
use App\Models\FaltaUnoSportProfile;
use App\Models\Field;
use App\Models\FieldPrice;
use App\Models\FieldSchedule;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Venue;
use App\Models\VenueAdminSubscription;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Datos de prueba para desarrollo local.
 *
 * Genera un entorno navegable: complejos visibles en el listado público,
 * canchas de varios deportes con precios y horarios, reservas, partidos de
 * Falta Uno abiertos y posts del blog.
 *
 * Corre después de DatabaseSeeder (que crea los usuarios base).
 * NUNCA debe ejecutarse en producción — DatabaseSeeder ya bloquea eso.
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin    = User::where('email', 'srbattini@gmail.com')->first();
        $usuario1 = User::where('email', 'usuario1@test.com')->first();
        $players  = User::whereIn('email', [
            'usuario2@test.com', 'usuario3@test.com', 'usuario4@test.com', 'usuario5@test.com',
        ])->get();

        if (! $usuario1) {
            $this->command->warn('DemoDataSeeder: faltan los usuarios base, salteando.');
            return;
        }

        // ── Edades (el filtro de Falta Uno las necesita) ─────────────────
        $ages = [26, 31, 24, 38];
        foreach ($players as $i => $p) {
            $p->update(['age' => $ages[$i] ?? 30]);
        }
        $usuario1->update(['age' => 35]);

        // ── Suscripción del dueño ───────────────────────────────────────
        // Sin una suscripción vigente el complejo NO aparece en el listado
        // público (scope withActiveOwner). Es el motivo más común de "levanté
        // el proyecto y no veo ningún complejo".
        VenueAdminSubscription::updateOrCreate(
            ['user_id' => $usuario1->id],
            [
                'plan_slug'     => 'pro',
                'billing_cycle' => 'monthly',
                'status'        => 'ACTIVE',
                'monthly_price' => 25000,
                'currency'      => 'ARS',
                'starts_at'     => now()->subMonth(),
                'expires_at'    => now()->addYear(),
            ]
        );

        // ── Complejos ───────────────────────────────────────────────────
        $venue1 = Venue::where('owner_user_id', $usuario1->id)->first();
        if ($venue1) {
            $venue1->update([
                'description'        => 'Complejo con canchas de fútbol y pádel. Vestuarios, parrilla y estacionamiento.',
                'zone'               => 'Palermo',
                'lat'                => -34.5780,
                'lng'                => -58.4300,
                'cancellation_hours' => 12,
                'accepts_cash_payment' => true,
            ]);
        }

        $venue2 = Venue::updateOrCreate(
            ['name' => 'Club Deportivo Norte'],
            [
                'owner_user_id'      => $usuario1->id,
                'description'        => 'Canchas de tenis y pádel con iluminación LED.',
                'address'            => 'Av. Libertador 4500, Buenos Aires',
                'zone'               => 'Núñez',
                'phone'              => '1144445555',
                'lat'                => -34.5450,
                'lng'                => -58.4600,
                'is_active'          => true,
                'cancellation_hours' => 24,
                'accepts_cash_payment' => false,
            ]
        );

        // ── Canchas ─────────────────────────────────────────────────────
        $fieldsSpec = [
            [$venue1, 'Cancha 1 — Fútbol 5',  'football', '5',  12000, true],
            [$venue1, 'Cancha 2 — Fútbol 7',  'football', '7',  18000, true],
            [$venue1, 'Cancha 3 — Pádel',     'padel',    '2',   9000, true],
            [$venue2, 'Cancha A — Pádel',     'padel',    '2',  10000, true],
            [$venue2, 'Cancha B — Tenis',     'tennis',   '2',   8000, false],
        ];

        $fields = [];
        foreach ($fieldsSpec as [$venue, $name, $sport, $format, $price, $faltaUno]) {
            if (! $venue) {
                continue;
            }

            $field = Field::updateOrCreate(
                ['venue_id' => $venue->id, 'name' => $name],
                [
                    'sport'        => $sport,
                    'format'       => $format,
                    'slot_minutes' => 60,
                    'is_indoor'    => $sport === 'padel',
                    'is_active'    => true,
                ]
            );

            FieldPrice::updateOrCreate(
                ['field_id' => $field->id],
                [
                    'price_per_slot'       => $price,
                    'currency'             => 'ARS',
                    'night_price_per_slot' => $price * 1.25,
                    'night_start_time'     => '19:00',
                    'night_end_time'       => '23:00',
                ]
            );

            // Horarios: todos los días de 08:00 a 23:00
            for ($dow = 0; $dow <= 6; $dow++) {
                FieldSchedule::updateOrCreate(
                    ['field_id' => $field->id, 'day_of_week' => $dow],
                    ['open_time' => '08:00', 'close_time' => '23:00']
                );
            }

            if ($faltaUno) {
                FaltaUnoSetting::updateOrCreate(
                    ['field_id' => $field->id],
                    [
                        'enabled'                     => true,
                        'refund_deadline_minutes'     => 60,
                        'fill_deadline_minutes'       => 120,
                        'late_leave_deadline_minutes' => 240,
                    ]
                );
            }

            $fields[] = $field;
        }

        // ── Perfiles deportivos ─────────────────────────────────────────
        $profiles = [
            ['football', 'intermedio', 'male'],
            ['padel',    'cuarta',     'male'],
            ['football', 'avanzado',   'male'],
            ['padel',    'sexta',      'female'],
        ];
        foreach ($players as $i => $player) {
            [$sport, $category, $gender] = $profiles[$i] ?? $profiles[0];
            FaltaUnoSportProfile::updateOrCreate(
                ['user_id' => $player->id, 'sport' => $sport],
                [
                    'category'        => $category,
                    'gender'          => $gender,
                    'games_played'    => rand(3, 20),
                    'wins'            => rand(1, 10),
                    'draws'           => rand(0, 3),
                    'losses'          => rand(0, 7),
                    'average_rating'  => rand(35, 50) / 10,
                    'attendance_rate' => rand(80, 100),
                ]
            );
        }
        FaltaUnoSportProfile::updateOrCreate(
            ['user_id' => $usuario1->id, 'sport' => 'football'],
            ['category' => 'intermedio', 'gender' => 'male', 'games_played' => 12, 'average_rating' => 4.6, 'attendance_rate' => 95]
        );

        // ── Reservas ────────────────────────────────────────────────────
        if (! empty($fields) && $players->isNotEmpty()) {
            $futbol5 = $fields[0];

            // Futura pagada
            $this->makeReservation($futbol5, $players[0], now()->addDays(2)->setTime(20, 0), 'PAID');
            // Futura a pagar en efectivo
            $this->makeReservation($futbol5, $players[1] ?? $players[0], now()->addDays(3)->setTime(21, 0), 'PENDING_CASH');
            // Pasada (para el historial)
            $this->makeReservation($futbol5, $players[0], now()->subDays(5)->setTime(19, 0), 'PAID');
        }

        // ── Partidos de Falta Uno ───────────────────────────────────────
        if (! empty($fields) && $players->isNotEmpty()) {
            $this->makeFaltaUnoGame($fields[0], $usuario1, $players, now()->addDays(2)->setTime(22, 0), 10, 2);

            if (isset($fields[2])) {
                $this->makeFaltaUnoGame($fields[2], $players[0], $players, now()->addDays(1)->setTime(19, 0), 4, 1);
            }
        }

        // ── Blog ────────────────────────────────────────────────────────
        if ($admin) {
            $posts = [
                ['Cómo elegir tu primera paleta de pádel', 'Guía para principiantes: peso, forma y balance.'],
                ['Los 5 errores más comunes del jugador amateur', 'Qué corregir para mejorar tu juego rápido.'],
                ['Fútbol 5, 7 u 11: cuál elegir según tu grupo', 'Diferencias de ritmo, espacio y cantidad de jugadores.'],
            ];
            foreach ($posts as $i => [$title, $excerpt]) {
                BlogPost::updateOrCreate(
                    ['slug' => Str::slug($title)],
                    [
                        'title'            => $title,
                        'excerpt'          => $excerpt,
                        'body'             => "## {$title}\n\n{$excerpt}\n\nContenido de prueba para desarrollo local.\n\n### Subtítulo\n\nTexto de ejemplo con varios párrafos para verificar el diseño de la vista del post.",
                        'author_user_id'   => $admin->id,
                        'meta_title'       => $title,
                        'meta_description' => $excerpt,
                        'status'           => 'published',
                        'published_at'     => now()->subDays($i + 1),
                    ]
                );
            }
        }

        $this->command->info('✅ Datos de prueba cargados: 2 complejos, ' . count($fields) . ' canchas, reservas, partidos Falta Uno y blog.');
    }

    private function makeReservation(Field $field, User $user, $start, string $status): void
    {
        Reservation::updateOrCreate(
            ['field_id' => $field->id, 'user_id' => $user->id, 'start_at' => $start],
            [
                'end_at'            => $start->copy()->addMinutes($field->slot_minutes ?: 60),
                'status'            => $status,
                'total_amount'      => $field->price?->price_per_slot ?? 10000,
                'currency'          => 'ARS',
                'verification_code' => strtoupper(Str::random(8)),
            ]
        );
    }

    private function makeFaltaUnoGame(Field $field, User $initiator, $players, $start, int $total, int $initiatorPlayers): void
    {
        $reservation = Reservation::updateOrCreate(
            ['field_id' => $field->id, 'user_id' => $initiator->id, 'start_at' => $start],
            [
                'end_at'            => $start->copy()->addMinutes($field->slot_minutes ?: 60),
                'status'            => 'PAID',
                'total_amount'      => ($field->price?->price_per_slot ?? 10000) * ($initiatorPlayers / $total),
                'currency'          => 'ARS',
                'verification_code' => strtoupper(Str::random(8)),
            ]
        );

        $game = FaltaUnoGame::updateOrCreate(
            ['reservation_id' => $reservation->id],
            [
                'field_id'          => $field->id,
                'initiator_user_id' => $initiator->id,
                'total_players'     => $total,
                'initiator_players' => $initiatorPlayers,
                'players_needed'    => $total - $initiatorPlayers,
                'status'            => 'open',
                'start_at'          => $start,
                'amount_paid'       => $reservation->total_amount,
                'gender_filter'     => 'mixed',
                'message'           => 'Partido tranquilo, nivel intermedio. Llevo pelota.',
                'is_private'        => false,
            ]
        );

        // Un par de jugadores ya anotados
        foreach ($players->take(2) as $player) {
            if ($player->id === $initiator->id) {
                continue;
            }
            FaltaUnoParticipant::updateOrCreate(
                ['game_id' => $game->id, 'user_id' => $player->id],
                ['status' => 'confirmed']
            );
        }
    }
}
