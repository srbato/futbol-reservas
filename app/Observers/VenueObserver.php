<?php

namespace App\Observers;

use App\Models\Venue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VenueObserver
{
    public function saved(Venue $venue): void
    {
        // Solo geocodificar si cambió la dirección y no tiene coords todavía,
        // o si la dirección cambió (para actualizar coords viejas)
        if (! $venue->wasChanged('address') && $venue->lat !== null) {
            return;
        }

        if (blank($venue->address)) {
            return;
        }

        $key = config('services.google_geocoding.key');

        if (blank($key)) {
            return;
        }

        try {
            $response = Http::when(app()->isLocal(), fn ($h) => $h->withoutVerifying())
                ->get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'address' => $venue->address,
                    'key'     => $key,
                ]);

            $data = $response->json();

            if (($data['status'] ?? '') === 'OK') {
                $location = $data['results'][0]['geometry']['location'];
                $venue->lat = $location['lat'];
                $venue->lng = $location['lng'];
                $venue->saveQuietly();
            } else {
                Log::warning("VenueObserver: geocoding fallido para venue {$venue->id} — status: {$data['status']}");
            }
        } catch (\Throwable $e) {
            Log::warning("VenueObserver: error geocoding venue {$venue->id} — {$e->getMessage()}");
        }
    }
}
