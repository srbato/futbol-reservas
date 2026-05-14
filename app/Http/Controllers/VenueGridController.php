<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Services\FieldAvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Vista nueva tipo "ATC": grid de canchas (filas) x horas (columnas) para un complejo.
 * Permite seleccionar uno o varios slots consecutivos en la misma cancha y reservar.
 *
 * Endpoints existentes (AvailabilityController, fields.show, etc.) NO se modifican.
 */
class VenueGridController extends Controller
{
    /**
     * /venues/{venue}/reservar — el grid ahora vive embebido en venues.show,
     * así que esta URL redirige al ancla #reservar de la vista del complejo.
     * Mantenemos la ruta para no romper links externos / SEO.
     */
    public function show(Venue $venue)
    {
        if (!$venue->is_active) {
            abort(404);
        }
        return redirect()->away(route('venues.show', $venue) . '#reservar', 301);
    }

    /**
     * Endpoint JSON con la disponibilidad de TODAS las canchas activas del complejo
     * para una fecha. Lo consume el frontend del grid.
     */
    public function availability(Request $request, Venue $venue, FieldAvailabilityService $service)
    {
        $request->validate([
            'date' => ['required', 'date'],
        ]);

        if (!$venue->is_active) {
            abort(404);
        }

        $date = Carbon::parse($request->query('date'))->startOfDay();

        $fields = $venue->fields()
            ->where('is_active', true)
            ->with(['price', 'schedules', 'exceptions', 'discounts'])
            ->orderBy('name')
            ->get();

        $payload = $fields->map(function ($field) use ($service, $date) {
            return [
                'id'           => $field->id,
                'name'         => $field->name,
                'sport'        => $field->sport,
                'format'       => $field->format,
                'slot_minutes' => (int) ($field->slot_minutes ?: 60),
                'is_indoor'    => (bool) $field->is_indoor,
                'cover_image_path' => $field->cover_image_path,
                'slots'        => $service->computeSlots($field, $date),
            ];
        });

        // Calcular el rango de horas global (min open, max close) para el header del grid
        $allHours = [];
        foreach ($payload as $f) {
            foreach ($f['slots'] as $s) {
                $allHours[] = $s['start_at'];
            }
        }
        sort($allHours);
        $firstHour = $allHours[0] ?? null;
        $lastHour  = end($allHours) ?: null;

        return response()->json([
            'date'       => $date->toDateString(),
            'venue_id'   => $venue->id,
            'fields'     => $payload,
            'first_hour' => $firstHour,
            'last_hour'  => $lastHour,
        ]);
    }
}
