<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use Illuminate\Http\Request;

class MembershipPlanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role !== 'super_admin') abort(403);

        $plans = MembershipPlan::orderBy('sort_order')->get();

        return view('sa.plans.index', compact('plans'));
    }

    public function update(Request $request, MembershipPlan $plan)
    {
        if ($request->user()->role !== 'super_admin') abort(403);

        $data = $request->validate([
            'name'                       => ['required', 'string', 'max:100'],
            'max_fields'                 => ['nullable', 'integer', 'min:1'],
            'monthly_price'              => ['required', 'numeric', 'min:1'],
            'annual_discount_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_active'                  => ['boolean'],
            'is_featured'                => ['boolean'],
            'sort_order'                 => ['required', 'integer', 'min:0'],
        ]);

        $plan->update([
            'name'                       => $data['name'],
            'max_fields'                 => $data['max_fields'] ?: null,
            'monthly_price'              => $data['monthly_price'],
            'annual_discount_percentage' => $data['annual_discount_percentage'],
            'is_active'                  => $request->boolean('is_active'),
            'is_featured'                => $request->boolean('is_featured'),
            'sort_order'                 => $data['sort_order'],
        ]);

        return back()->with('success', "Plan {$plan->name} actualizado correctamente.");
    }
}
