<section class="space-y-6">
    <header>
        <p class="mt-1 text-sm text-neutral-500">
            Une fois votre compte supprimé, toutes ses ressources et données seront définitivement effacées. Avant de supprimer votre compte, veuillez télécharger toutes les données que vous souhaitez conserver.
        </p>
    </header>

    <x-button variant="danger" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
        Supprimer le compte
    </x-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable title="Confirmer la suppression">
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 space-y-6">
            @csrf
            @method('delete')

            <h3 class="text-h4 text-neutral-900">Êtes-vous sûr de vouloir supprimer votre compte ?</h3>

            <p class="text-sm text-neutral-600">
                Une fois votre compte supprimé, toutes ses ressources et données seront définitivement effacées. Veuillez entrer votre mot de passe pour confirmer.
            </p>

            <x-input name="password" label="Mot de passe" type="password" :message="$errors->userDeletion->first('password')" placeholder="Mot de passe" />

            <div class="flex justify-end gap-4">
                <x-button variant="outline" x-on:click="$dispatch('close')">Annuler</x-button>
                <x-button variant="danger">Supprimer le compte</x-button>
            </div>
        </form>
    </x-modal>
</section>
