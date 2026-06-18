<x-guest-layout>
    <div class="mb-4 text-sm text-neutral-600">
        Merci de vous être inscrit ! Avant de commencer, veuillez vérifier votre adresse email en cliquant sur le lien que nous venons de vous envoyer. Si vous n'avez pas reçu l'email, nous vous en enverrons un autre.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-success-600">
            Un nouveau lien de vérification a été envoyé à l'adresse email que vous avez fournie lors de l'inscription.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-button>Renvoyer l'email de vérification</x-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-button variant="ghost">Déconnexion</x-button>
        </form>
    </div>
</x-guest-layout>
