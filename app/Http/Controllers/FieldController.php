<?php

namespace App\Http\Controllers;

use App\Models\Field;

class FieldController extends Controller
{
    public function show(Field $field)
    {
        $field->load(['venue', 'price']);

        $recurringDiscounts = $field->recurringDiscounts()
            ->where('is_active', true)
            ->orderBy('min_occurrences')
            ->get(['min_occurrences', 'discount_percentage']);

        return view('fields.show', compact('field', 'recurringDiscounts'));
    }
}
