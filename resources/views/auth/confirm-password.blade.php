<x-guest-layout>
    <div class="mb-4 text-sm text-neutral-600">
        Cette zone est sécurisée. Veuillez confirmer votre mot de passe avant de continuer.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="space-y-4">
            <x-input name="password" label="Mot de passe" type="password" :message="$errors->first('password')" required autocomplete="current-password" />

            <div class="flex justify-end">
                <x-button>Confirmer</x-button>
            </div>
        </div>
    </form>
</x-guest-layout>
