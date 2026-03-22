<?php

namespace App\Http\Controllers;

use App\Models\FaltaUnoSportProfile;
use Illuminate\Http\Request;

class FaltaUnoSportProfileController extends Controller
{
    public function index()
    {
        return redirect('/profile#sport-profile');
    }

    public function create(Request $request)
    {
        $sport      = $request->query('sport');
        $categories = $sport ? FaltaUnoSportProfile::getCategoriesForSport($sport) : [];

        return view('falta-uno.sport-profile.create', compact('sport', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sport'     => ['required', 'string'],
            'category'  => ['required', 'string'],
            'gender'    => ['required', 'in:male,female'],
            'age_group' => ['nullable', 'string', 'required_if:sport,padel'],
        ]);

        auth()->user()->faltaUnoSportProfiles()->create($data);

        return redirect('/profile#sport-profile')
            ->with('success', 'Perfil deportivo creado correctamente.');
    }

    public function edit(string $sport)
    {
        $profile = auth()->user()->sportProfileFor($sport);

        if (!$profile) {
            abort(404);
        }

        $categories = FaltaUnoSportProfile::getCategoriesForSport($sport);

        return view('falta-uno.sport-profile.edit', compact('profile', 'categories', 'sport'));
    }

    public function update(Request $request, string $sport)
    {
        $profile = auth()->user()->sportProfileFor($sport);

        if (!$profile) {
            abort(404);
        }

        $data = $request->validate([
            'category'  => ['required', 'string'],
            'gender'    => ['required', 'in:male,female'],
            'age_group' => ['nullable', 'string', 'required_if:sport,padel'],
        ]);

        $profile->update($data);

        return redirect('/profile#sport-profile')
            ->with('success', 'Perfil deportivo actualizado correctamente.');
    }
}
