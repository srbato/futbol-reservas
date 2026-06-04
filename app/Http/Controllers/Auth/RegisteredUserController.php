<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register', [
            'googleName'  => session('google_name'),
            'googleEmail' => session('google_email'),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // ── Antibot 1: honeypot — un bot completa este campo invisible
        if ($request->filled('website_url')) {
            abort(422);
        }

        // ── Antibot 2: timing — un humano tarda más de 3s en completar el form
        $loadedAt = (int) $request->input('form_loaded_at', 0);
        if ($loadedAt > 0) {
            $elapsedMs = now()->valueOf() - $loadedAt;
            if ($elapsedMs < 3000 || $elapsedMs > 6 * 60 * 60 * 1000) {
                abort(422);
            }
        }

        $request->validate([
            'name' => [
                'required', 'string', 'max:80',
                // ── Antibot 3: un nombre real no tiene URLs ni es mayormente no-latino
                function ($attr, $value, $fail) {
                    if (preg_match('~https?://|www\.|\.com|\.net|\.ru|\.ua~i', $value)) {
                        $fail('El nombre no puede contener enlaces.');
                    }
                    $letters = preg_replace('/[^\p{L}]/u', '', $value);
                    if (mb_strlen($letters) >= 6) {
                        $latin = preg_match_all('/[\p{Latin}]/u', $letters);
                        if (($latin / mb_strlen($letters)) < 0.5) {
                            $fail('Ingresá un nombre válido.');
                        }
                    }
                },
            ],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'google_id' => session('google_id'),
        ]);

        session()->forget(['google_name', 'google_email', 'google_id']);

        event(new Registered($user));

        Mail::to($user->email)->send(new WelcomeMail($user));

        Auth::login($user);

        return redirect()->route('venues.index');
    }
}
