<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\FieldRecurringDiscount;
use Illuminate\Http\Request;

class FieldRecurringDiscountController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'field_id'            => ['required', 'integer', 'exists:fields,id'],
            'min_occurrences'     => ['required', 'integer', 'min:2', 'max:12'],
            'discount_percentage' => ['required', 'numeric', 'min:1', 'max:100'],
        ]);

        $field = Field::query()
            ->whereHas('venue', fn($q) => $q->where('owner_user_id', $user->id))
            ->findOrFail($data['field_id']);

        FieldRecurringDiscount::create([
            'field_id'            => $field->id,
            'min_occurrences'     => $data['min_occurrences'],
            'discount_percentage' => $data['discount_percentage'],
            'is_active'           => true,
        ]);

        return redirect()->route('va.discounts.index')->with('success', 'Descuento recurrente creado.');
    }

    public function destroy(Request $request, FieldRecurringDiscount $recurringDiscount)
    {
        $user = $request->user();

        $recurringDiscount->load('field.venue');

        if ($user->role !== 'super_admin' && $recurringDiscount->field->venue->owner_user_id !== $user->id) {
            abort(403);
        }

        $recurringDiscount->delete();

        return redirect()->route('va.discounts.index')->with('success', 'Descuento recurrente eliminado.');
    }
}
