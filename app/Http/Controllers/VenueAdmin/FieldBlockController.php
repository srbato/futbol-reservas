<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\FieldBlock;
use Illuminate\Http\Request;

class FieldBlockController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isVenueStaff()) {
            abort_if(!$user->hasStaffPermission('manage_blocks', $user->activeStaffVenueId()), 403);
        }

        $fields = Field::query()
            ->whereHas('venue', fn ($q) => $q->accessibleBy($user))
            ->with('venue')
            ->orderBy('name')
            ->get();

        $blocks = FieldBlock::query()
            ->whereHas('field.venue', fn ($q) => $q->accessibleBy($user))
            ->with(['field.venue'])
            ->orderByDesc('date')
            ->orderBy('start_time')
            ->get();

        return view('va.blocks.index', compact('fields', 'blocks'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->isVenueStaff()) {
            abort_if(!$user->hasStaffPermission('manage_blocks', $user->activeStaffVenueId()), 403);
        }

        $data = $request->validate([
            'field_id' => ['required', 'integer', 'exists:fields,id'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $field = Field::query()
            ->whereHas('venue', fn ($q) => $q->accessibleBy($user))
            ->findOrFail($data['field_id']);

        // Validar overlap con reservas activas (PAID, PENDING_CASH, o PENDING_PAYMENT no expirado).
        // Si el dueño bloquea un horario que ya tiene una reserva confirmada, queda
        // un "fantasma" donde el cliente cree que reservó pero la cancha figura bloqueada.
        $start = \Carbon\Carbon::parse($data['date'] . ' ' . $data['start_time']);
        $end   = \Carbon\Carbon::parse($data['date'] . ' ' . $data['end_time']);

        $conflict = \App\Models\Reservation::query()
            ->where('field_id', $field->id)
            ->whereIn('status', ['PAID', 'PENDING_CASH', 'PENDING_PAYMENT'])
            ->where(function ($q) {
                $q->whereIn('status', ['PAID', 'PENDING_CASH'])
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'PENDING_PAYMENT')
                         ->whereNotNull('expires_at')
                         ->where('expires_at', '>', now());
                  });
            })
            ->where('start_at', '<', $end)
            ->where('end_at', '>', $start)
            ->with('user')
            ->first();

        if ($conflict) {
            $clientName = $conflict->user->name ?? 'cliente';
            $clientTime = $conflict->start_at->format('H:i') . '–' . $conflict->end_at->format('H:i');
            return back()->with('error',
                "No se pudo crear el bloqueo: ya existe una reserva de {$clientName} en {$clientTime}. " .
                "Cancelá esa reserva primero o ajustá el rango del bloqueo."
            );
        }

        FieldBlock::create([
            'field_id'   => $field->id,
            'date'       => $data['date'],
            'start_time' => $data['start_time'],
            'end_time'   => $data['end_time'],
            'reason'     => $data['reason'] ?? null,
        ]);

        broadcast(new \App\Events\FieldAvailabilityChanged($field->id, $data['date']));

        return back()->with('success', 'Bloqueo creado correctamente.');
    }

    public function destroy(Request $request, FieldBlock $block)
    {
        $user = $request->user();

        $block->load('field.venue');

        if ($user->role !== 'super_admin' && $block->field->venue->owner_user_id !== $user->id && !$user->isStaffOf($block->field->venue->id)) {
            abort(403);
        }

        if ($user->isVenueStaff()) {
            abort_if(!$user->hasStaffPermission('manage_blocks', $block->field->venue->id), 403);
        }

        $fieldId = $block->field_id;
        $blockDate = $block->date instanceof \Carbon\Carbon ? $block->date->toDateString() : (string) $block->date;

        $block->delete();

        broadcast(new \App\Events\FieldAvailabilityChanged($fieldId, $blockDate));

        return back()->with('success', 'Bloqueo eliminado correctamente.');
    }
}
