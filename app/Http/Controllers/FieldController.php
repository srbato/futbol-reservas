<?php

namespace App\Http\Controllers;

use App\Models\Field;

class FieldController extends Controller
{
    public function show(Field $field)
    {
        $field->load(['venue', 'price']);
        return view('fields.show', compact('field'));
    }
}
