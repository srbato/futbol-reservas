<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\FaltaUnoSetting;
use App\Models\Field;
use App\Models\FieldPrice;
use App\Models\FieldSchedule;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OnboardingController extends Controller
{
    public function show(Request $request, int $step)
    {
        $user = $request->user();

        if ($step < 1 || $step > 4) {
            $step = 1;
        }

        $venue = Venue::where('owner_user_id', $user->id)->first();
        $field = $venue?->fields()->first();

        // No permitir saltar pasos que no se completaron
        if ($step >= 2 && !$venue) {
            return redirect()->route('va.onboarding.step', ['step' => 1]);
        }
        if ($step >= 3 && !$field) {
            return redirect()->route('va.onboarding.step', ['step' => 2]);
        }
        if ($step >= 4 && (!$field || !$field->schedules()->exists())) {
            return redirect()->route('va.onboarding.step', ['step' => 3]);
        }

        $data = compact('step', 'venue', 'field');

        // Para paso 4, cargar resumen completo
        if ($step === 4 && $venue && $field) {
            $field->load(['price', 'schedules']);
            $data['field'] = $field;
        }

        return view("va.onboarding.step{$step}", $data);
    }

    public function storeVenue(Request $request)
    {
        $user = $request->user();

        // No crear duplicado si ya tiene venue
        if (Venue::where('owner_user_id', $user->id)->exists()) {
            return redirect()->route('va.onboarding.step', ['step' => 2]);
        }

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:120'],
            'address'     => ['required', 'string', 'max:200'],
            'zone'        => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'phone'       => ['nullable', 'string', 'max:30'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $coords = $this->geocodeAddress($data['address']);

        $venue = new Venue();
        $venue->owner_user_id = $user->id;
        $venue->name          = $data['name'];
        $venue->address       = $data['address'];
        $venue->zone          = $data['zone'] ?? null;
        $venue->description   = $data['description'] ?? null;
        $venue->phone         = $data['phone'] ?? null;
        $venue->lat           = $coords['lat'];
        $venue->lng           = $coords['lng'];
        $venue->is_active     = true;

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('venues', 'public');
            $venue->cover_image_path = $path;
        }

        $venue->save();

        return redirect()->route('va.onboarding.step', ['step' => 2]);
    }

    public function storeField(Request $request)
    {
        $user  = $request->user();
        $venue = Venue::where('owner_user_id', $user->id)->firstOrFail();

        // No crear duplicado si ya tiene field (onboarding solo crea 1)
        if ($venue->fields()->exists()) {
            return redirect()->route('va.onboarding.step', ['step' => 3]);
        }

        $data = $request->validate([
            'name'           => ['required', 'string', 'max:120'],
            'sport'          => ['required', 'in:football,padel,tennis,basketball,volleyball'],
            'surface'        => ['required', 'in:sintetico,natural,cemento,otro'],
            'is_indoor'      => ['nullable', 'boolean'],
            'format'         => ['required', 'integer', 'min:1', 'max:11'],
            'price_per_slot' => ['required', 'numeric', 'min:0'],
            'currency'       => ['required', 'string', 'size:3'],
        ]);

        $field = Field::create([
            'venue_id'     => $venue->id,
            'name'         => $data['name'],
            'sport'        => $data['sport'],
            'surface'      => $data['surface'],
            'format'       => $data['format'],
            'slot_minutes' => 60, // Default en onboarding
            'is_indoor'    => $request->boolean('is_indoor'),
            'is_active'    => true,
        ]);

        FieldPrice::create([
            'field_id'       => $field->id,
            'price_per_slot' => $data['price_per_slot'],
            'currency'       => strtoupper($data['currency']),
        ]);

        FaltaUnoSetting::create([
            'field_id'                => $field->id,
            'enabled'                 => false,
            'refund_deadline_minutes' => 60,
            'fill_deadline_minutes'   => 120,
        ]);

        return redirect()->route('va.onboarding.step', ['step' => 3]);
    }

    public function storeSchedule(Request $request)
    {
        $user  = $request->user();
        $venue = Venue::where('owner_user_id', $user->id)->firstOrFail();
        $field = $venue->fields()->firstOrFail();

        $data = $request->validate([
            'open_time'    => ['required', 'date_format:H:i'],
            'close_time'   => ['required', 'date_format:H:i', 'after:open_time'],
            'slot_minutes' => ['required', 'integer', 'min:30', 'max:180'],
            'days'         => ['nullable', 'array'],
            'days.*'       => ['nullable', 'array'],
            'days.*.open_time'  => ['nullable', 'date_format:H:i'],
            'days.*.close_time' => ['nullable', 'date_format:H:i'],
            'days.*.is_closed'  => ['nullable'],
        ]);

        // Actualizar slot_minutes en el field
        $field->update(['slot_minutes' => $data['slot_minutes']]);

        // Borrar horarios previos (si re-ejecuta el paso)
        $field->schedules()->delete();

        $dayNames = [0 => 'Domingo', 1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado'];

        for ($dow = 0; $dow <= 6; $dow++) {
            $dayData = $data['days'][$dow] ?? null;

            // Si tiene configuración personalizada por día
            if ($dayData && isset($dayData['is_closed'])) {
                continue; // Día cerrado
            }

            $openTime  = $dayData['open_time'] ?? $data['open_time'];
            $closeTime = $dayData['close_time'] ?? $data['close_time'];

            if ($openTime && $closeTime) {
                FieldSchedule::create([
                    'field_id'    => $field->id,
                    'day_of_week' => $dow,
                    'open_time'   => $openTime,
                    'close_time'  => $closeTime,
                ]);
            }
        }

        return redirect()->route('va.onboarding.step', ['step' => 4]);
    }

    public function complete(Request $request)
    {
        $request->user()->update(['onboarding_completed_at' => now()]);

        return redirect()->route('va.dashboard')
            ->with('success', 'Tu complejo está listo. Bienvenido a TuCancha.');
    }

    public function skip(Request $request)
    {
        $user = $request->user();

        // Solo permitir saltear si el usuario ya tiene al menos un venue creado
        if (!Venue::where('owner_user_id', $user->id)->exists()) {
            return redirect()->route('va.onboarding.step', ['step' => 1])
                ->with('error', 'Necesitas crear al menos un complejo antes de saltear la configuracion.');
        }

        $user->update(['onboarding_completed_at' => now()]);

        return redirect()->route('va.dashboard')
            ->with('success', 'Salteaste la configuracion inicial. Podes completarla cuando quieras desde el panel.');
    }

    public function resume(Request $request)
    {
        $request->user()->update(['onboarding_completed_at' => null]);

        return redirect()->route('va.onboarding.step', ['step' => 1]);
    }

    private function geocodeAddress(?string $address): array
    {
        if (!$address) {
            return ['lat' => null, 'lng' => null];
        }

        try {
            $key = config('services.google_geocoding.key');
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $address,
                'key'     => $key,
            ]);

            $data = $response->json();

            if (($data['status'] ?? '') === 'OK') {
                $location = $data['results'][0]['geometry']['location'];
                return ['lat' => $location['lat'], 'lng' => $location['lng']];
            }
        } catch (\Throwable $e) {
            Log::warning('Geocoding failed for address: ' . $address . ' — ' . $e->getMessage());
        }

        return ['lat' => null, 'lng' => null];
    }
}
