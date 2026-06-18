<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="space-y-4">
            <x-input name="email" label="Email" type="email" :value="old('email')" :message="$errors->first('email')" required autofocus autocomplete="username" />

            <x-input name="password" label="Mot de passe" type="password" :message="$errors->first('password')" required autocomplete="current-password" />

            <div class="flex items-center">
                <input id="remember_me" type="checkbox" name="remember" class="rounded border-neutral-300 text-primary-600 shadow-sm focus:ring-primary-500">
                <label for="remember_me" class="ml-2 text-sm text-neutral-600">Se souvenir de moi</label>
            </div>

            <div class="flex items-center justify-end gap-4">
                @if (Route::has('password.request'))
                    <a class="text-sm text-neutral-600 hover:text-neutral-900 underline rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" href="{{ route('password.request') }}">
                        Mot de passe oublié ?
                    </a>
                @endif

                <x-button>Connexion</x-button>
            </div>
        </div>
    </form>
</x-guest-layout>
