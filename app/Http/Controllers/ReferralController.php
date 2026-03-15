<?php

namespace App\Http\Controllers;

use App\Models\PlatformPayout;
use App\Models\ReferralReward;
use App\Models\Reservation;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferralController extends Controller
{
    public function __construct(private ReferralService $referralService) {}

    // GET /referral — show code + rewards list
    public function index(Request $request)
    {
        $user = $request->user();
        $referralCode = $this->referralService->getOrCreateCode($user);
        $rewards = $user->referralRewards()->with('referred')->latest()->get();
        $availableCount = $rewards->where('status', 'available')->count();

        return view('referral.index', compact('referralCode', 'rewards', 'availableCount'));
    }

    // POST /referral/redeem-reservation/{reservation} — use reward to pay a reservation for free
    public function redeemReservation(Request $request, Reservation $reservation)
    {
        $user = $request->user();

        if ($reservation->user_id !== $user->id) abort(403);
        if ($reservation->status !== 'PENDING_PAYMENT') {
            return back()->with('error', 'Esta reserva no está pendiente de pago.');
        }
        if ($reservation->expires_at && $reservation->expires_at->isPast()) {
            return back()->with('error', 'La reserva ya expiró.');
        }

        $result = DB::transaction(function () use ($user, $reservation) {
            // Lock the reward row to prevent race conditions
            $reward = $user->availableReferralRewards()->lockForUpdate()->first();
            if (!$reward) {
                return null;
            }

            $reward->update([
                'status'      => 'redeemed',
                'reward_type' => 'free_reservation',
                'redeemed_at' => now(),
            ]);

            $reservation->update([
                'status'             => 'PAID',
                'expires_at'         => null,
                'payment_provider'   => 'referral_reward',
                'payment_status'     => 'approved',
                'referral_reward_id' => $reward->id,
            ]);

            $reservation->loadMissing('field');

            PlatformPayout::create([
                'venue_id'           => $reservation->field->venue_id,
                'reservation_id'     => $reservation->id,
                'referral_reward_id' => $reward->id,
                'amount'             => $reservation->total_amount,
                'currency'           => $reservation->currency ?? 'ARS',
                'status'             => 'pending',
            ]);

            return $reward;
        });

        if (!$result) {
            return back()->with('error', 'No tenés recompensas disponibles.');
        }

        return redirect()
            ->route('reservations.success', $reservation)
            ->with('success', '¡Reserva pagada con tu recompensa de referido!');
    }

    // POST /referral/redeem-month/{reward} — extend venue admin subscription by 30 days
    public function redeemMonth(Request $request, ReferralReward $reward)
    {
        $user = $request->user();

        if ($reward->referrer_user_id !== $user->id) abort(403);
        if ($reward->status !== 'available') {
            return back()->with('error', 'Esta recompensa ya fue canjeada.');
        }
        if ($user->role !== 'venue_admin' && $user->role !== 'super_admin') {
            return back()->with('error', 'Esta recompensa solo está disponible para administradores de complejos.');
        }

        $subscription = $user->activeVenueAdminSubscription()->first();
        if (!$subscription) {
            return back()->with('error', 'No tenés una suscripción activa para extender.');
        }

        $subscription->expires_at = $subscription->expires_at->addDays(30);
        $subscription->save();

        $reward->update([
            'status'      => 'redeemed',
            'reward_type' => 'free_month',
            'redeemed_at' => now(),
        ]);

        return back()->with('success', '¡Mes gratis aplicado! Tu suscripción ahora vence el ' . $subscription->expires_at->format('d/m/Y') . '.');
    }
}
