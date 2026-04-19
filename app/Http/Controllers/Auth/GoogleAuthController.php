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
                // SEGURIDAD: solo vincular automáticamente si el email ya estaba verificado
                // por otro medio (ej: click en el link de verificación). Si no, un atacante
                // podría registrar el email de la víctima y esperar a que entre con Google
                // para tomar control de la cuenta.
                if (! $user->email_verified_at) {
                    return redirect()->route('login')->withErrors([
                        'email' => 'Ya existe una cuenta con este email pero sin verificar. Ingresá con tu contraseña original y verificá el email antes de vincular Google.',
                    ]);
                }
                $user->update([
                    'google_id' => $googleUser->getId(),
                    // Si Google confirma el email, consolidamos la verificación
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
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

        // Validación de cuenta activa
        if (! $user->is_active) {
            return redirect()->route('login')->withErrors([
                'email' => 'Tu cuenta está desactivada. Contactanos si es un error.',
            ]);
        }

        Auth::login($user, true);

        return redirect()->route('venues.index');
    }
}
