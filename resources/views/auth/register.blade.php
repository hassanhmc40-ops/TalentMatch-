<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="space-y-4">
            <x-input name="name" label="Nom" type="text" :value="old('name')" :message="$errors->first('name')" required autofocus autocomplete="name" />

            <x-input name="email" label="Email" type="email" :value="old('email')" :message="$errors->first('email')" required autocomplete="username" />

            <x-input name="password" label="Mot de passe" type="password" :message="$errors->first('password')" required autocomplete="new-password" />

            <x-input name="password_confirmation" label="Confirmer le mot de passe" type="password" :message="$errors->first('password_confirmation')" required autocomplete="new-password" />

            <div class="flex items-center justify-end gap-4">
                <a class="text-sm text-neutral-600 hover:text-neutral-900 underline rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" href="{{ route('login') }}">
                    Déjà inscrit ?
                </a>

                <x-button>Inscription</x-button>
            </div>
        </div>
    </form>
</x-guest-layout>
