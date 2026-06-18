<x-guest-layout>
    <div class="mb-4 text-sm text-neutral-600">
        Mot de passe oublié ? Indiquez votre adresse email et nous vous enverrons un lien de réinitialisation.
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="space-y-4">
            <x-input name="email" label="Email" type="email" :value="old('email')" :message="$errors->first('email')" required autofocus />

            <div class="flex items-center justify-end">
                <x-button>Envoyer le lien</x-button>
            </div>
        </div>
    </form>
</x-guest-layout>
