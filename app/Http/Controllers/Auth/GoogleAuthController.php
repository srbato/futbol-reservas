<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->stateless()->setHttpClient(
            new \GuzzleHttp\Client(['verify' => app()->isProduction()])
        )->user();

        $user = User::where('google_id', $googleUser->getId())->first();

        if (! $user) {
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $user->update(['google_id' => $googleUser->getId()]);
            } else {
                // Redirigir al registro con datos pre-completados
                session([
                    'google_name'  => $googleUser->getName(),
                    'google_email' => $googleUser->getEmail(),
                    'google_id'    => $googleUser->getId(),
                ]);

                return redirect()->route('register');
            }
        }

        Auth::login($user, true);

        return redirect()->route('venues.index');
    }
}
