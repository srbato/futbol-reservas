<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-3xl font-bold text-gray-900">Crear cuenta</h1>
        <p class="mt-2 text-sm text-gray-600">
            Registrate para reservar canchas, pagar online y gestionar tus turnos.
        </p>
    </div>

    <a href="{{ route('auth.google') }}"
       style="display:flex; align-items:center; justify-content:center; gap:10px; width:100%; padding:11px 16px; border:1px solid #dadce0; border-radius:12px; background:#fff; color:#3c4043; font-size:15px; font-weight:500; text-decoration:none; margin-bottom:20px; transition:box-shadow 0.2s;"
       onmouseover="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.15)'" onmouseout="this.style.boxShadow='none'">
        <svg width="20" height="20" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/><path fill="none" d="M0 0h48v48H0z"/></svg>
        Registrarse con Google
    </a>

    <div style="display:flex; align-items:center; gap:12px; margin-bottom:20px;">
        <div style="flex:1; height:1px; background:#e5e7eb;"></div>
        <span style="font-size:13px; color:#9ca3af;">o</span>
        <div style="flex:1; height:1px; background:#e5e7eb;"></div>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="name" :value="'Nombre'" />
            <x-text-input
                id="name"
                class="block mt-1 w-full rounded-xl"
                type="text"
                name="name"
                :value="old('name', $googleName ?? '')"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="'Email'" />
            <x-text-input
                id="email"
                class="block mt-1 w-full rounded-xl"
                type="email"
                name="email"
                :value="old('email', $googleEmail ?? '')"
                required
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="'Contraseña'" />
            <x-text-input
                id="password"
                class="block mt-1 w-full rounded-xl"
                type="password"
                name="password"
                required
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="'Confirmar contraseña'" />
            <x-text-input
                id="password_confirmation"
                class="block mt-1 w-full rounded-xl"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; padding-top:6px;">
            <a
                href="{{ route('login') }}"
                style="font-size:14px; color:#666;"
            >
                Ya tengo cuenta
            </a>

            <x-primary-button class="rounded-xl px-5 py-3">
                Crear cuenta
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
