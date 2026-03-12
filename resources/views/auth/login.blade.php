<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-3xl font-bold text-gray-900">Ingresar</h1>
        <p class="mt-2 text-sm text-gray-600">
            Entrá a tu cuenta para reservar, pagar y ver tus turnos.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="'Email'" />
            <x-text-input
                id="email"
                class="block mt-1 w-full rounded-xl"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
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
                autocomplete="current-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
            <label for="remember_me" style="display:flex; align-items:center; gap:8px; font-size:14px; color:#555;">
                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    style="width:auto; margin:0;"
                >
                <span>Recordarme</span>
            </label>

            @if (Route::has('password.request'))
                <a
                    href="{{ route('password.request') }}"
                    style="font-size:14px; color:#666;"
                >
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; padding-top:6px;">
            <a
                href="{{ route('register') }}"
                style="font-size:14px; color:#666;"
            >
                Crear cuenta
            </a>

            <x-primary-button class="rounded-xl px-5 py-3">
                Ingresar
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
