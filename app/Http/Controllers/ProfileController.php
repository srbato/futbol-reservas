<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\BadgeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $badgeService = new BadgeService();
        $sportProfiles = $user->faltaUnoSportProfiles()->get();
        $allBadges = $badgeService->getUniqueBadges($sportProfiles);
        $badgesBySport = $badgeService->getBadgesForProfiles($sportProfiles);

        return view('profile.edit', [
            'user' => $user,
            'allBadges' => $allBadges,
            'badgesBySport' => $badgesBySport,
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // Subida de avatar
        if ($request->hasFile('avatar')) {
            if ($request->user()->avatar_path) {
                Storage::disk('public')->delete($request->user()->avatar_path);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $request->user()->avatar_path = $path;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}