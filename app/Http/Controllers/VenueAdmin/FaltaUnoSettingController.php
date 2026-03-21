<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\FaltaUnoSetting;
use Illuminate\Http\Request;

class FaltaUnoSettingController extends Controller
{
    public function update(Request $request, Field $field)
    {
        $field->load('venue');
        $user = $request->user();

        if ($user->role !== 'super_admin' && $field->venue->owner_user_id !== $user->id && !$user->isStaffOf($field->venue->id)) {
            abort(403);
        }

        $data = $request->validate([
            'enabled'                 => ['boolean'],
            'refund_deadline_minutes' => ['required', 'integer', 'min:0', 'max:10080'],
            'fill_deadline_minutes'   => ['required', 'integer', 'min:0', 'max:10080'],
        ]);

        FaltaUnoSetting::updateOrCreate(
            ['field_id' => $field->id],
            [
                'enabled'                 => $request->boolean('enabled'),
                'refund_deadline_minutes' => $data['refund_deadline_minutes'],
                'fill_deadline_minutes'   => $data['fill_deadline_minutes'],
            ]
        );

        return back()->with('success', 'Configuración de Falta Uno guardada.');
    }
}
