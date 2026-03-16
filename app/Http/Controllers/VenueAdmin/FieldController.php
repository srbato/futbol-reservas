<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\FieldPrice;
use App\Models\MembershipPlan;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FieldController extends Controller
{
    public function create(Request $request, Venue $venue)
    {
        if ($request->user()->role !== 'super_admin' && $venue->owner_user_id !== $request->user()->id) {
            abort(403);
        }

        return view('va.fields.create', compact('venue'));
    }

    public function store(Request $request, Venue $venue)
    {
        $user = $request->user();

        // Solo el dueño (o super_admin) puede crear canchas
        if ($user->role !== 'super_admin' && $venue->owner_user_id !== $user->id) {
            abort(403);
        }

        // Enforce plan field limit (super_admin is exempt) — wrapped in transaction to prevent race condition
        if ($user->role !== 'super_admin') {
            $subscription = $user->activeVenueAdminSubscription()->first();
            $plan = $subscription?->plan_slug
                ? MembershipPlan::where('slug', $subscription->plan_slug)->first()
                : null;

            if ($plan && $plan->max_fields !== null) {
                $limitError = DB::transaction(function () use ($user, $plan) {
                    $activeFieldCount = Field::whereHas('venue', fn($q) => $q->where('owner_user_id', $user->id))
                        ->where('is_active', true)
                        ->lockForUpdate()
                        ->count();

                    if ($activeFieldCount >= $plan->max_fields) {
                        return "Tu plan {$plan->name} permite hasta {$plan->max_fields} " .
                            ($plan->max_fields === 1 ? 'cancha activa' : 'canchas activas') .
                            ". Desactivá una cancha existente o actualizá tu plan para agregar más.";
                    }
                    return null;
                });

                if ($limitError) {
                    return back()->with('error', $limitError);
                }
            }
        }

        $data = $request->validate([
            'name'                 => ['required', 'string', 'max:120'],
            'sport'                => ['required', 'in:football,padel,tennis,basketball,volleyball'],
            'format'               => ['required', 'integer', 'min:1', 'max:11'],
            'slot_minutes'         => ['required', 'integer', 'min:30', 'max:180'],
            'price_per_slot'       => ['required', 'numeric', 'min:0'],
            'currency'             => ['required', 'string', 'size:3'],
            'night_price_per_slot' => ['nullable', 'numeric', 'min:0'],
            'night_start_time'     => ['nullable', 'date_format:H:i', 'required_with:night_price_per_slot'],
            'night_end_time'       => ['nullable', 'date_format:H:i', 'required_with:night_price_per_slot', 'after:night_start_time'],
        ]);

        $field = Field::create([
            'venue_id'     => $venue->id,
            'name'         => $data['name'],
            'sport'        => $data['sport'],
            'format'       => $data['format'],
            'slot_minutes' => $data['slot_minutes'],
            'is_indoor'    => false,
            'is_active'    => true,
        ]);

        FieldPrice::create([
            'field_id'             => $field->id,
            'price_per_slot'       => $data['price_per_slot'],
            'currency'             => strtoupper($data['currency']),
            'night_price_per_slot' => $data['night_price_per_slot'] ?? null,
            'night_start_time'     => $data['night_start_time'] ?? null,
            'night_end_time'       => $data['night_end_time'] ?? null,
        ]);

        return redirect()->route('va.schedule.edit', $field);
    }

    public function edit(Request $request, Field $field)
    {
        $field->load(['venue', 'price']);

        if ($request->user()->role !== 'super_admin' && $field->venue->owner_user_id !== $request->user()->id && !$request->user()->isStaffOf($field->venue->id)) {
            abort(403);
        }

        return view('va.fields.edit', compact('field'));
    }

    public function update(Request $request, Field $field)
    {
        $field->load(['venue', 'price']);

        if ($request->user()->role !== 'super_admin' && $field->venue->owner_user_id !== $request->user()->id && !$request->user()->isStaffOf($field->venue->id)) {
            abort(403);
        }

        $data = $request->validate([
            'name'                 => ['required', 'string', 'max:120'],
            'sport'                => ['required', 'in:football,padel,tennis,basketball,volleyball'],
            'format'               => ['required', 'integer', 'min:1', 'max:11'],
            'slot_minutes'         => ['required', 'integer', 'min:30', 'max:180'],
            'price_per_slot'       => ['required', 'numeric', 'min:0'],
            'currency'             => ['required', 'string', 'size:3'],
            'cover_image'          => ['nullable', 'image', 'max:4096'],
            'night_price_per_slot' => ['nullable', 'numeric', 'min:0'],
            'night_start_time'     => ['nullable', 'date_format:H:i', 'required_with:night_price_per_slot'],
            'night_end_time'       => ['nullable', 'date_format:H:i', 'required_with:night_price_per_slot', 'after:night_start_time'],
        ]);

        $field->update([
            'name'         => $data['name'],
            'sport'        => $data['sport'],
            'format'       => $data['format'],
            'slot_minutes' => $data['slot_minutes'],
        ]);

        if ($request->hasFile('cover_image')) {
            if ($field->cover_image_path) {
                Storage::disk('public')->delete($field->cover_image_path);
            }
            $path = $request->file('cover_image')->store('fields', 'public');
            $field->cover_image_path = $path;
        }

        $field->save();

        FieldPrice::updateOrCreate(
            ['field_id' => $field->id],
            [
                'price_per_slot'       => $data['price_per_slot'],
                'currency'             => strtoupper($data['currency']),
                'night_price_per_slot' => $data['night_price_per_slot'] ?? null,
                'night_start_time'     => $data['night_start_time'] ?? null,
                'night_end_time'       => $data['night_end_time'] ?? null,
            ]
        );

        return redirect()->route('va.dashboard');
    }

    public function toggleActive(Request $request, Field $field)
    {
        $user = $request->user();
        $field->load('venue');

        if ($user->role !== 'super_admin' && $field->venue->owner_user_id !== $user->id && !$user->isStaffOf($field->venue->id)) {
            abort(403);
        }

        // If re-activating, enforce plan limit
        if (!$field->is_active && $user->role !== 'super_admin') {
            $subscription = $user->activeVenueAdminSubscription()->first();
            $plan = $subscription?->plan_slug
                ? MembershipPlan::where('slug', $subscription->plan_slug)->first()
                : null;

            if ($plan && $plan->max_fields !== null) {
                $activeFieldCount = Field::whereHas('venue', fn($q) => $q->where('owner_user_id', $user->id))
                    ->where('is_active', true)
                    ->count();

                if ($activeFieldCount >= $plan->max_fields) {
                    return redirect()->route('va.dashboard')
                        ->with('error',
                            "Tu plan {$plan->name} permite hasta {$plan->max_fields} " .
                            ($plan->max_fields === 1 ? 'cancha activa' : 'canchas activas') .
                            ". Desactivá otra cancha antes de reactivar esta."
                        );
                }
            }
        }

        $field->is_active = !$field->is_active;
        $field->save();

        $estado = $field->is_active ? 'activada' : 'desactivada';

        return redirect()->route('va.dashboard')
            ->with('success', "La cancha \"{$field->name}\" fue {$estado}.");
    }
}
