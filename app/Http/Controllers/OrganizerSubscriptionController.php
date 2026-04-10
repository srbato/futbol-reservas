<?php

namespace App\Http\Controllers;

use App\Services\OrganizerSubscriptionService;
use Illuminate\Http\Request;

class OrganizerSubscriptionController extends Controller
{
    public function plans()
    {
        $user = auth()->user();
        $tier = $user->organizerTier();
        $subscription = $user->activeOrganizerSubscription;
        $plans = \App\Models\OrganizerPlan::where('is_active', true)->orderBy('sort_order')->get();

        return view('organizador.planes', compact('tier', 'subscription', 'plans'));
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'mp_email' => 'required|email',
        ]);

        $service = app(OrganizerSubscriptionService::class);
        [$ok, $result] = $service->subscribe(auth()->user(), $request->mp_email);

        if (!$ok) {
            return back()->with('error', $result);
        }

        // $result is the init_point URL from MercadoPago
        return redirect()->away($result);
    }

    public function cancel()
    {
        $service = app(OrganizerSubscriptionService::class);
        $service->cancel(auth()->user());

        return redirect()->route('organizador.planes')
            ->with('success', 'Suscripcion cancelada.');
    }

    public function success()
    {
        return redirect()->route('organizador.planes')
            ->with('success', 'Suscripcion iniciada. En cuanto MercadoPago confirme el pago, tu plan Pro se activara automaticamente.');
    }
}
