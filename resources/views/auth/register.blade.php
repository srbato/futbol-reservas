<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-3xl font-bold text-gray-900">Crear cuenta</h1>
        <p class="mt-2 text-sm text-gray-600">
            Registrate para reservar canchas, pagar online y gestionar tus turnos.
        </p>
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
                :value="old('name')"
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
                :value="old('email')"
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
