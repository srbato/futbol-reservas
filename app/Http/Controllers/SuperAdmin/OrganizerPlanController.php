<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\OrganizerPlan;
use Illuminate\Http\Request;

class OrganizerPlanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role !== 'super_admin') abort(403);

        $plans = OrganizerPlan::orderBy('sort_order')->get();

        return view('sa.organizer-plans.index', compact('plans'));
    }

    public function update(Request $request, OrganizerPlan $plan)
    {
        if ($request->user()->role !== 'super_admin') abort(403);

        $data = $request->validate([
            'name'                       => ['required', 'string', 'max:100'],
            'monthly_price'              => ['required', 'numeric', 'min:0'],
            'max_tournaments'            => ['required', 'integer', 'min:-1'],
            'max_teams_per_tournament'   => ['required', 'integer', 'min:1'],
            'available_formats'          => ['nullable', 'array'],
            'available_formats.*'        => ['string', 'in:single_elimination,round_robin,groups_elimination'],
            'has_stats'                  => ['boolean'],
            'has_notifications'          => ['boolean'],
            'has_custom_branding'        => ['boolean'],
            'has_mp_payments'            => ['boolean'],
            'is_active'                  => ['boolean'],
            'sort_order'                 => ['required', 'integer', 'min:0'],
        ]);

        $plan->update([
            'name'                       => $data['name'],
            'monthly_price'              => $data['monthly_price'],
            'max_tournaments'            => $data['max_tournaments'],
            'max_teams_per_tournament'   => $data['max_teams_per_tournament'],
            'available_formats'          => $data['available_formats'] ?? [],
            'has_stats'                  => $request->boolean('has_stats'),
            'has_notifications'          => $request->boolean('has_notifications'),
            'has_custom_branding'        => $request->boolean('has_custom_branding'),
            'has_mp_payments'            => $request->boolean('has_mp_payments'),
            'is_active'                  => $request->boolean('is_active'),
            'sort_order'                 => $data['sort_order'],
        ]);

        return back()->with('success', "Plan {$plan->name} actualizado correctamente.");
    }
}
