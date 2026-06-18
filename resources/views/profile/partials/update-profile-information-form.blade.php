<section>
    <header>
        <p class="mt-1 text-sm text-neutral-500">
            Mettez à jour les informations de votre compte et votre adresse email.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <x-input name="name" label="Nom" type="text" :value="old('name', $user->name)" :message="$errors->first('name')" required autofocus autocomplete="name" />

        <div>
            <x-input name="email" label="Email" type="email" :value="old('email', $user->email)" :message="$errors->first('email')" required autocomplete="username" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-sm text-amber-600">
                        Votre adresse email n'est pas vérifiée.

                        <button form="send-verification" class="underline text-sm text-primary-600 hover:text-primary-800 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                            Cliquez ici pour renvoyer l'email de vérification.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-success-600">
                            Un nouveau lien de vérification a été envoyé à votre adresse email.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-button>Enregistrer</x-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-success-600 font-medium">
                    Enregistré.
                </p>
            @endif
        </div>
    </form>
</section>
