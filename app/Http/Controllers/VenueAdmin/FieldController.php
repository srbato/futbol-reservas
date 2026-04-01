<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Mail\FaltaUnoCancelledMail;
use App\Models\FaltaUnoGame;
use App\Models\FaltaUnoSetting;
use App\Models\Field;
use App\Models\FieldPrice;
use App\Models\MembershipPlan;
use App\Models\Reservation;
use App\Models\Venue;
use App\Notifications\FaltaUnoCancelledNotification;
use App\Services\MercadoPagoRefundService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class FieldController extends Controller
{
    public function create(Request $request, Venue $venue)
    {
        $user = $request->user();
        if ($user->role !== 'super_admin' && $venue->owner_user_id !== $user->id && !$user->isStaffOf($venue->id)) {
            abort(403);
        }

        if ($user->isVenueStaff()) {
            abort_if(!$user->hasStaffPermission('manage_fields', $venue->id), 403);
        }

        return view('va.fields.create', compact('venue'));
    }

    public function store(Request $request, Venue $venue)
    {
        $user = $request->user();

        if ($user->role !== 'super_admin' && $venue->owner_user_id !== $user->id && !$user->isStaffOf($venue->id)) {
            abort(403);
        }

        if ($user->isVenueStaff()) {
            abort_if(!$user->hasStaffPermission('manage_fields', $venue->id), 403);
        }

        // Enforce plan field limit against the venue owner (super_admin is exempt)
        $owner = $venue->owner;
        if ($user->role !== 'super_admin') {
            $subscription = $owner->activeVenueAdminSubscription()->first();
            $plan = $subscription?->plan_slug
                ? MembershipPlan::where('slug', $subscription->plan_slug)->first()
                : null;

            if ($plan && $plan->max_fields !== null) {
                $limitError = DB::transaction(function () use ($owner, $plan) {
                    $activeFieldCount = Field::whereHas('venue', fn($q) => $q->where('owner_user_id', $owner->id))
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
            'name'                    => ['required', 'string', 'max:120'],
            'sport'                   => ['required', 'in:football,padel,tennis,basketball,volleyball'],
            'format'                  => ['required', 'integer', 'min:1', 'max:11'],
            'slot_minutes'            => ['required', 'integer', 'min:30', 'max:180'],
            'price_per_slot'          => ['required', 'numeric', 'min:0'],
            'currency'                => ['required', 'string', 'size:3'],
            'cover_image'             => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'night_price_per_slot'    => ['nullable', 'numeric', 'min:0'],
            'night_start_time'        => ['nullable', 'date_format:H:i', 'required_with:night_price_per_slot'],
            'night_end_time'          => ['nullable', 'date_format:H:i', 'required_with:night_price_per_slot', 'after:night_start_time'],
            'falta_uno_enabled'       => ['nullable', 'boolean'],
            'refund_deadline_minutes' => ['nullable', 'integer', 'min:0', 'max:10080'],
            'fill_deadline_minutes'   => ['nullable', 'integer', 'min:0', 'max:10080'],
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

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('fields', 'public');
            $field->cover_image_path = $path;
            $field->save();
        }

        FieldPrice::create([
            'field_id'             => $field->id,
            'price_per_slot'       => $data['price_per_slot'],
            'currency'             => strtoupper($data['currency']),
            'night_price_per_slot' => $data['night_price_per_slot'] ?? null,
            'night_start_time'     => $data['night_start_time'] ?? null,
            'night_end_time'       => $data['night_end_time'] ?? null,
        ]);

        FaltaUnoSetting::create([
            'field_id'                => $field->id,
            'enabled'                 => $request->boolean('falta_uno_enabled'),
            'refund_deadline_minutes' => $data['refund_deadline_minutes'] ?? 60,
            'fill_deadline_minutes'   => $data['fill_deadline_minutes'] ?? 120,
        ]);

        return redirect()->route('va.schedule.edit', $field);
    }

    public function edit(Request $request, Field $field)
    {
        $field->load(['venue', 'price']);

        if ($request->user()->role !== 'super_admin' && $field->venue->owner_user_id !== $request->user()->id && !$request->user()->isStaffOf($field->venue->id)) {
            abort(403);
        }

        if ($request->user()->isVenueStaff()) {
            abort_if(!$request->user()->hasStaffPermission('manage_fields', $field->venue->id), 403);
        }

        return view('va.fields.edit', compact('field'));
    }

    public function update(Request $request, Field $field)
    {
        $field->load(['venue', 'price']);

        if ($request->user()->role !== 'super_admin' && $field->venue->owner_user_id !== $request->user()->id && !$request->user()->isStaffOf($field->venue->id)) {
            abort(403);
        }

        if ($request->user()->isVenueStaff()) {
            abort_if(!$request->user()->hasStaffPermission('manage_fields', $field->venue->id), 403);
        }

        $data = $request->validate([
            'name'                 => ['required', 'string', 'max:120'],
            'sport'                => ['required', 'in:football,padel,tennis,basketball,volleyball'],
            'format'               => ['required', 'integer', 'min:1', 'max:11'],
            'slot_minutes'         => ['required', 'integer', 'min:30', 'max:180'],
            'price_per_slot'       => ['required', 'numeric', 'min:0'],
            'currency'             => ['required', 'string', 'size:3'],
            'cover_image'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
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

    public function toggleActive(Request $request, Field $field, MercadoPagoRefundService $refundService)
    {
        $user = $request->user();
        $field->load('venue');

        if ($user->role !== 'super_admin' && $field->venue->owner_user_id !== $user->id && !$user->isStaffOf($field->venue->id)) {
            abort(403);
        }

        if ($user->isVenueStaff()) {
            abort_if(!$user->hasStaffPermission('manage_fields', $field->venue->id), 403);
        }

        // If re-activating, enforce plan limit
        if (!$field->is_active && $user->role !== 'super_admin') {
            $owner = $field->venue->owner;
            $subscription = $owner->activeVenueAdminSubscription()->first();
            $plan = $subscription?->plan_slug
                ? MembershipPlan::where('slug', $subscription->plan_slug)->first()
                : null;

            if ($plan && $plan->max_fields !== null) {
                $activeFieldCount = Field::whereHas('venue', fn($q) => $q->where('owner_user_id', $owner->id))
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

        // --- Lógica de desactivación con verificación de reservas futuras ---
        if ($field->is_active) {
            $futureReservations = Reservation::where('field_id', $field->id)
                ->where('start_at', '>', now())
                ->whereIn('status', ['PAID', 'PENDING_PAYMENT'])
                ->with('user')
                ->orderBy('start_at')
                ->get();

            // Si hay reservas futuras y no viene confirmación explícita, pedimos confirmación
            if ($futureReservations->isNotEmpty() && !$request->boolean('confirmed')) {
                $reservationsData = $futureReservations->map(fn($r) => [
                    'id'           => $r->id,
                    'date'         => $r->start_at->format('d/m/Y'),
                    'time'         => $r->start_at->format('H:i') . '–' . $r->end_at->format('H:i'),
                    'user_name'    => $r->user?->name ?? 'Sin nombre',
                    'status'       => $r->status,
                    'total_amount' => $r->total_amount,
                    'currency'     => $r->currency,
                ]);

                return response()->json([
                    'requires_confirmation' => true,
                    'field_name'            => $field->name,
                    'reservations'          => $reservationsData,
                    'total_amount'          => $futureReservations->sum('total_amount'),
                    'currency'              => $futureReservations->first()->currency,
                ]);
            }

            // Tiene confirmación: procesar reembolsos si corresponde
            if ($futureReservations->isNotEmpty() && $request->boolean('refund')) {
                foreach ($futureReservations as $reservation) {
                    $refundResult = $refundService->refund($reservation);

                    if ($refundResult === false) {
                        // El reembolso falló: anotarlo en notas para gestión manual
                        $reservation->notes = trim(($reservation->notes ?? '') . "\n[REEMBOLSO PENDIENTE] Cancha desactivada el " . now()->format('d/m/Y H:i') . '. Procesar manualmente.');
                    }

                    $reservation->update([
                        'status'     => 'CANCELLED',
                        'expires_at' => null,
                        'notes'      => $reservation->notes,
                    ]);

                    Log::info('Reserva cancelada por desactivación de cancha', [
                        'reservation_id' => $reservation->id,
                        'field_id'       => $field->id,
                        'refund_result'  => $refundResult,
                    ]);
                }
            }
        }

        // Al desactivar, cancelar partidos Falta Uno abiertos/full y notificar participantes
        if ($field->is_active) {
            $openGames = FaltaUnoGame::where('field_id', $field->id)
                ->whereIn('status', ['open', 'full'])
                ->where('start_at', '>', now())
                ->get();

            foreach ($openGames as $game) {
                $game->load('activeParticipants.user', 'field.venue');

                foreach ($game->activeParticipants as $participant) {
                    if ($participant->user && $participant->user->email) {
                        Mail::to($participant->user->email)
                            ->send(new FaltaUnoCancelledMail($game, $participant->user));
                        $participant->user->notify(new FaltaUnoCancelledNotification($game));
                    }
                }

                $game->update([
                    'status'       => 'cancelled',
                    'cancelled_at' => now(),
                ]);

                Log::info('Partido Falta Uno cancelado por desactivacion de cancha', [
                    'game_id'  => $game->id,
                    'field_id' => $field->id,
                ]);
            }
        }

        $field->is_active = !$field->is_active;
        $field->save();

        $estado = $field->is_active ? 'activada' : 'desactivada';

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => "La cancha \"{$field->name}\" fue {$estado}."]);
        }

        return redirect()->route('va.dashboard')
            ->with('success', "La cancha \"{$field->name}\" fue {$estado}.");
    }
}
