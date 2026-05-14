<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\FieldDiscount;
use App\Models\FieldRecurringDiscount;
use Illuminate\Http\Request;

class FieldDiscountController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isVenueStaff()) {
            abort_if(!$user->hasStaffPermission('manage_discounts', $user->activeStaffVenueId()), 403);
        }

        $fields = Field::query()
            ->whereHas('venue', fn ($q) => $q->accessibleBy($user))
            ->with('venue')
            ->orderBy('name')
            ->get();

        $discounts = FieldDiscount::query()
            ->whereHas('field.venue', fn ($q) => $q->accessibleBy($user))
            ->with('field.venue')
            ->orderByDesc('date')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $recurringDiscounts = FieldRecurringDiscount::query()
            ->whereHas('field.venue', fn ($q) => $q->accessibleBy($user))
            ->with('field.venue')
            ->orderBy('min_occurrences')
            ->get();

        return view('va.discounts.index', compact('fields', 'discounts', 'recurringDiscounts'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->isVenueStaff()) {
            abort_if(!$user->hasStaffPermission('manage_discounts', $user->activeStaffVenueId()), 403);
        }

        $data = $request->validate([
            'field_id' => ['required', 'integer', 'exists:fields,id'],
            'day_of_week' => ['nullable', 'integer', 'min:0', 'max:6'],
            'date' => ['nullable', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'discount_price' => ['required', 'numeric', 'min:0'],
            'label' => ['nullable', 'string', 'max:120'],
        ]);

        $field = Field::query()
            ->whereHas('venue', fn ($q) => $q->accessibleBy($user))
            ->findOrFail($data['field_id']);

        $dow = $data['day_of_week'] ?? null;
        FieldDiscount::create([
            'field_id'       => $field->id,
            'day_of_week'    => ($dow !== '' && $dow !== null) ? $dow : null,
            'date'           => $data['date'] ?? null,
            'start_time'     => $data['start_time'] ?? null,
            'end_time'       => $data['end_time'] ?? null,
            'discount_price' => $data['discount_price'],
            'label'          => $data['label'] ?? null,
            'is_active'      => true,
        ]);

        return redirect()->route('va.discounts.index')->with('success', 'Descuento creado.');
    }

    public function destroy(Request $request, FieldDiscount $discount)
    {
        $user = $request->user();

        $discount->load('field.venue');

        if ($user->role !== 'super_admin' && $discount->field->venue->owner_user_id !== $user->id && !$user->isStaffOf($discount->field->venue->id)) {
            abort(403);
        }

        if ($user->isVenueStaff()) {
            abort_if(!$user->hasStaffPermission('manage_discounts', $discount->field->venue->id), 403);
        }

        $discount->delete();

        return redirect()->route('va.discounts.index')->with('success', 'Descuento eliminado.');
    }
}
