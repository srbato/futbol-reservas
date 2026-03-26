<?php

namespace App\Console\Commands;

use App\Models\Venue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodeVenues extends Command
{
    protected $signature = 'venues:geocode
                            {--force : Geocodificar incluso los que ya tienen coordenadas}
                            {--dry-run : Solo mostrar cuántos se procesarían, sin guardar}';

    protected $description = 'Geocodifica todos los complejos con dirección pero sin coordenadas lat/lng';

    public function handle(): int
    {
        $query = Venue::whereNotNull('address')->where('address', '!=', '');

        if (!$this->option('force')) {
            $query->whereNull('lat');
        }

        $venues = $query->get(['id', 'name', 'address', 'lat', 'lng']);

        if ($venues->isEmpty()) {
            $this->info('No hay complejos para geocodificar.');
            return 0;
        }

        $this->info("Complejos a procesar: {$venues->count()}");

        if ($this->option('dry-run')) {
            foreach ($venues as $venue) {
                $this->line("  [{$venue->id}] {$venue->name} — {$venue->address}");
            }
            return 0;
        }

        $key = config('services.google_geocoding.key');
        $ok = 0;
        $failed = 0;

        $bar = $this->output->createProgressBar($venues->count());
        $bar->start();

        foreach ($venues as $venue) {
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
                    $venue->saveQuietly(); // sin disparar eventos/observers
                    $ok++;
                } else {
                    $this->newLine();
                    $this->warn("  Sin resultado [{$venue->id}] {$venue->name}: {$data['status']}");
                    $failed++;
                }
            } catch (\Throwable $e) {
                $this->newLine();
                $this->error("  Error [{$venue->id}] {$venue->name}: {$e->getMessage()}");
                Log::warning("GeocodeVenues: error en venue {$venue->id} — {$e->getMessage()}");
                $failed++;
            }

            // Pausa para no exceder el rate limit de la API (50 req/s en plan estándar)
            usleep(200_000); // 200ms entre cada request

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Completado: {$ok} geocodificados, {$failed} fallidos.");

        return 0;
    }
}
