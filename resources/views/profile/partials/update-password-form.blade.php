<section>
    <header>
        <p class="mt-1 text-sm text-neutral-500">
            Assurez-vous que votre compte utilise un mot de passe long et aléatoire pour rester sécurisé.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <x-input name="current_password" label="Mot de passe actuel" type="password" :message="$errors->updatePassword->first('current_password')" autocomplete="current-password" />

        <x-input name="password" label="Nouveau mot de passe" type="password" :message="$errors->updatePassword->first('password')" autocomplete="new-password" />

        <x-input name="password_confirmation" label="Confirmer le mot de passe" type="password" :message="$errors->updatePassword->first('password_confirmation')" autocomplete="new-password" />

        <div class="flex items-center gap-4">
            <x-button>Enregistrer</x-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-success-600 font-medium">
                    Enregistré.
                </p>
            @endif
        </div>
    </form>
</section>
