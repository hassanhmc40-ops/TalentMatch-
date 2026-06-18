<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="space-y-4">
            <x-input name="email" label="Email" type="email" :value="old('email', $request->email)" :message="$errors->first('email')" required autofocus autocomplete="username" />

            <x-input name="password" label="Nouveau mot de passe" type="password" :message="$errors->first('password')" required autocomplete="new-password" />

            <x-input name="password_confirmation" label="Confirmer le mot de passe" type="password" :message="$errors->first('password_confirmation')" required autocomplete="new-password" />

            <div class="flex items-center justify-end">
                <x-button>Réinitialiser</x-button>
            </div>
        </div>
    </form>
</x-guest-layout>
