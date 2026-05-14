<?php

namespace Tests\Concerns;

use App\Models\Field;
use App\Models\FieldPrice;
use App\Models\FieldSchedule;
use App\Models\User;
use App\Models\Venue;

/**
 * Helpers comunes para crear venues+fields ya configurados (price + schedule 7 días)
 * en los feature tests, para no repetir setup boilerplate.
 */
trait MakesVenues
{
    /**
     * Crea un venue activo con un dueño super_admin (sin necesidad de subscription
     * para no entorpecer los tests de pago/reservas).
     */
    protected function makeVenue(array $overrides = []): Venue
    {
        $owner = User::factory()->create([
            'role'      => 'super_admin',
            'is_active' => true,
        ]);
        return Venue::factory()->create(array_merge(['owner_user_id' => $owner->id], $overrides));
    }

    /**
     * Crea un field con price + schedule (7 días, 08:00-22:00).
     * Devuelve el field ya con todas las relaciones cargadas listo para usar.
     */
    protected function makeField(?Venue $venue = null, array $overrides = [], float $price = 5000, int $slotMinutes = 60): Field
    {
        $venue = $venue ?? $this->makeVenue();
        $field = Field::factory()->create(array_merge([
            'venue_id'     => $venue->id,
            'slot_minutes' => $slotMinutes,
        ], $overrides));

        FieldPrice::factory()->create([
            'field_id'       => $field->id,
            'price_per_slot' => $price,
        ]);

        for ($dow = 0; $dow <= 6; $dow++) {
            FieldSchedule::factory()->create([
                'field_id'    => $field->id,
                'day_of_week' => $dow,
                'open_time'   => '08:00',
                'close_time'  => '22:00',
            ]);
        }

        return $field->fresh(['venue', 'price', 'schedules', 'exceptions', 'discounts']);
    }

    protected function makeUser(array $overrides = []): User
    {
        return User::factory()->create(array_merge(['is_active' => true], $overrides));
    }
}
