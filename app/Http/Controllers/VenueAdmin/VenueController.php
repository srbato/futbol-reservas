<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VenueController extends Controller
{
    public static function amenitiesList(): array
    {
        return [
            'wifi'            => ['emoji' => '📶', 'label' => 'WiFi'],
            'estacionamiento' => ['emoji' => '🅿️', 'label' => 'Estacionamiento'],
            'vestuarios'      => ['emoji' => '🚿', 'label' => 'Vestuarios'],
            'parrilla'        => ['emoji' => '🔥', 'label' => 'Parrilla'],
            'bar'             => ['emoji' => '🍺', 'label' => 'Bar / Cantina'],
            'techado'         => ['emoji' => '🏛️', 'label' => 'Techado'],
            'iluminacion'     => ['emoji' => '💡', 'label' => 'Iluminación nocturna'],
            'gradas'          => ['emoji' => '👥', 'label' => 'Gradas'],
            'escuelita'       => ['emoji' => '⚽', 'label' => 'Escuelita de fútbol'],
            'beelup'          => ['emoji' => '📱', 'label' => 'BeelUP'],
            'alquiler_pelota' => ['emoji' => '🏐', 'label' => 'Alquiler de pelota'],
            'accesible'       => ['emoji' => '♿', 'label' => 'Accesible'],
        ];
    }

    public function create(Request $request)
    {
        $user = $request->user();
        abort_if($user->isVenueStaff(), 403);
        if ($user->role !== 'super_admin' && Venue::where('owner_user_id', $user->id)->exists()) {
            return redirect()->route('va.dashboard')
                ->with('error', 'Ya tenés un complejo creado. Solo podés administrar un complejo por cuenta.');
        }

        $amenitiesList = self::amenitiesList();
        return view('va.venues.create', compact('amenitiesList'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        abort_if($user->isVenueStaff(), 403);

        $validAmenityKeys = array_keys(self::amenitiesList());

        $data = $request->validate([
            'name'               => ['required', 'string', 'max:120'],
            'description'        => ['nullable', 'string', 'max:255'],
            'phone'              => ['nullable', 'string', 'max:30'],
            'cover_image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'address'            => ['nullable', 'string', 'max:200'],
            'zone'               => ['nullable', 'string', 'max:120'],
            'cancellation_hours'      => ['nullable', 'integer', 'min:1', 'max:720'],
            'modification_hours'      => ['nullable', 'integer', 'min:1', 'max:720'],
            'recurring_payment_mode'  => ['nullable', 'string', 'in:upfront,manual,subscription'],
            'amenities'               => ['nullable', 'array'],
            'amenities.*'             => ['string', 'in:' . implode(',', $validAmenityKeys)],
            'accepts_cash_payment'    => ['nullable'],
        ]);

        if ($user->role !== 'super_admin' && Venue::where('owner_user_id', $user->id)->exists()) {
            return redirect()->route('va.dashboard')
                ->with('error', 'Ya tenés un complejo creado. Solo podés administrar un complejo por cuenta.');
        }

        $coords = $this->geocodeAddress($data['address'] ?? null);

        $venue = new Venue();
        $venue->owner_user_id      = $user->id;
        $venue->name               = $data['name'];
        $venue->description        = $data['description'] ?? null;
        $venue->phone              = $data['phone'] ?? null;
        $venue->address            = $data['address'] ?? null;
        $venue->zone               = $data['zone'] ?? null;
        $venue->lat                = $coords['lat'];
        $venue->lng                = $coords['lng'];
        $venue->cancellation_hours     = $data['cancellation_hours'] ?? null;
        $venue->modification_hours     = $data['modification_hours'] ?? null;
        $venue->recurring_payment_mode = $data['recurring_payment_mode'] ?? 'upfront';
        $venue->amenities              = $data['amenities'] ?? [];
        $venue->accepts_cash_payment   = $request->has('accepts_cash_payment');
        $venue->is_active              = true;

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('venues', 'public');
            $venue->cover_image_path = $path;
        }

        $venue->save();

        return redirect()->route('va.venues.connect_mp', $venue);
    }

    public function connectMp(Request $request, Venue $venue)
    {
        if ($venue->owner_user_id !== $request->user()->id && $request->user()->role !== 'super_admin') {
            abort(403);
        }

        return view('va.venues.connect-mp', compact('venue'));
    }

    public function edit(Request $request, Venue $venue)
    {
        if ($venue->owner_user_id !== $request->user()->id && $request->user()->role !== 'super_admin') {
            abort(403);
        }

        $amenitiesList = self::amenitiesList();

        return view('va.venues.edit', compact('venue', 'amenitiesList'));
    }

    public function update(Request $request, Venue $venue)
    {
        if ($venue->owner_user_id !== $request->user()->id && $request->user()->role !== 'super_admin') {
            abort(403);
        }

        $validAmenityKeys = array_keys(self::amenitiesList());

        $data = $request->validate([
            'name'               => ['required', 'string', 'max:120'],
            'description'        => ['nullable', 'string', 'max:255'],
            'phone'              => ['nullable', 'string', 'max:30'],
            'cover_image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'address'            => ['nullable', 'string', 'max:200'],
            'zone'               => ['nullable', 'string', 'max:120'],
            'cancellation_hours'      => ['nullable', 'integer', 'min:1', 'max:720'],
            'modification_hours'      => ['nullable', 'integer', 'min:1', 'max:720'],
            'recurring_payment_mode'  => ['nullable', 'string', 'in:upfront,manual,subscription'],
            'amenities'               => ['nullable', 'array'],
            'amenities.*'             => ['string', 'in:' . implode(',', $validAmenityKeys)],
            'accepts_cash_payment'    => ['nullable'],
        ]);

        if (($data['recurring_payment_mode'] ?? null) === 'subscription' && empty($venue->mp_access_token)) {
            return back()
                ->withInput()
                ->withErrors(['recurring_payment_mode' => 'Para activar la suscripcion mensual, primero conecta tu cuenta de MercadoPago.']);
        }

        // Re-geocodificar solo si la dirección cambió
        $newAddress = $data['address'] ?? null;
        if ($newAddress !== $venue->address || ($newAddress && !$venue->lat)) {
            $coords = $this->geocodeAddress($newAddress);
            $venue->lat = $coords['lat'];
            $venue->lng = $coords['lng'];
        }

        $venue->name               = $data['name'];
        $venue->description        = $data['description'] ?? null;
        $venue->phone              = $data['phone'] ?? null;
        $venue->address            = $newAddress;
        $venue->zone               = $data['zone'] ?? null;
        $venue->cancellation_hours     = $data['cancellation_hours'] ?? null;
        $venue->modification_hours     = $data['modification_hours'] ?? null;
        $venue->recurring_payment_mode = $data['recurring_payment_mode'] ?? 'upfront';
        $venue->amenities              = $data['amenities'] ?? [];
        $venue->accepts_cash_payment   = $request->has('accepts_cash_payment');

        if ($request->hasFile('cover_image')) {
            if ($venue->cover_image_path) {
                Storage::disk('public')->delete($venue->cover_image_path);
            }

            $path = $request->file('cover_image')->store('venues', 'public');
            $venue->cover_image_path = $path;
        }

        $venue->save();

        return redirect()->route('va.dashboard');
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
