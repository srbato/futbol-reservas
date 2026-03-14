<?php

namespace App\Services;

use App\Mail\ReferralRewardMail;
use App\Models\ReferralCode;
use App\Models\ReferralReward;
use App\Models\User;
use App\Models\VenueAdminSubscription;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReferralService
{
    public function getOrCreateCode(User $user): ReferralCode
    {
        return $user->referralCode ?? ReferralCode::generateForUser($user);
    }

    public function processReward(VenueAdminSubscription $subscription): void
    {
        $codeUsed = $subscription->referral_code_used;
        if (!$codeUsed) return;

        // Avoid duplicates
        if (ReferralReward::where('subscription_id', $subscription->id)->exists()) return;

        $referralCode = ReferralCode::where('code', $codeUsed)->with('user')->first();
        if (!$referralCode) {
            Log::warning('Referral: code not found', ['code' => $codeUsed]);
            return;
        }

        // Referrer can't refer themselves
        if ($referralCode->user_id === $subscription->user_id) return;

        $reward = ReferralReward::create([
            'referrer_user_id' => $referralCode->user_id,
            'referred_user_id' => $subscription->user_id,
            'subscription_id'  => $subscription->id,
            'status'           => 'available',
        ]);

        $reward->load('referred');

        try {
            Mail::to($referralCode->user->email)
                ->send(new ReferralRewardMail($referralCode->user, $reward));
        } catch (\Throwable $e) {
            Log::error('ReferralRewardMail failed', ['error' => $e->getMessage()]);
        }

        Log::info('Referral reward created', [
            'reward_id'    => $reward->id,
            'referrer'     => $referralCode->user_id,
            'referred'     => $subscription->user_id,
        ]);
    }
}
