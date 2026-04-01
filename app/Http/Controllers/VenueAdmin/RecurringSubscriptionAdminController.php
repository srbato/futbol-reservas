<?php

namespace App\Http\Controllers\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\RecurringSubscription;
use App\Models\Venue;
use App\Services\RecurringReservationSubscriptionService;
use Illuminate\Http\Request;

class RecurringSubscriptionAdminController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Build query: suscripciones cuya cancha pertenece a venues del usuario
        $query = RecurringSubscription::with([
            'user',
            'field.venue',
            'periods' => fn($q) => $q->latest()->limit(1),
        ])->orderByDesc('created_at');

        if ($user->role === 'super_admin') {
            // Super admin ve todas
        } elseif ($user->isVenueStaff()) {
            $venueId = $user->activeStaffVenueId();
            $query->whereHas('field', fn($q) => $q->where('venue_id', $venueId));
        } else {
            // venue_admin: solo sus venues
            $venueIds = Venue::where('owner_user_id', $user->id)->pluck('id');
            $query->whereHas('field', fn($q) => $q->whereIn('venue_id', $venueIds));
        }

        $subscriptions = $query->get();

        return view('va.recurring-subscriptions.index', compact('subscriptions'));
    }

    public function cancel(Request $request, RecurringSubscription $subscription)
    {
        $subscription->load('field.venue');
        $user = $request->user();

        if ($user->role !== 'super_admin'
            && $subscription->field->venue->owner_user_id !== $user->id
            && !$user->isStaffOf($subscription->field->venue->id)
        ) {
            abort(403);
        }

        app(RecurringReservationSubscriptionService::class)
            ->cancelSubscription($subscription, 'venue_cancelled');

        return back()->with('success', 'Suscripción cancelada.');
    }
}
