<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'   => ['required', 'string', 'max:255'],
            'email'  => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            // Teléfono: acepta formatos variados (+54 9 11 1234-5678, 01112345678, etc.)
            // pero debe tener entre 8 y 20 dígitos en total
            'phone'  => ['nullable', 'string', 'max:30', 'regex:/^[\d\s\+\-\(\)]{8,30}$/'],
            'avatar'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'age_group' => ['nullable', 'string', 'in:sub10,sub12,sub14,sub16,sub18,19a25,26a34,open,mas35,mas40,mas45,mas50,mas55,mas60'],
        ];
    }
}
