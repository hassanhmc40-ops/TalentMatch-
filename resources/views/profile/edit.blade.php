<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-neutral-900 leading-tight">
                Profil
            </h2>
            <p class="text-sm text-neutral-500 mt-1">Gérez vos informations personnelles et votre mot de passe</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-card header="Informations du profil">
                @include('profile.partials.update-profile-information-form')
            </x-card>

            <x-card header="Mot de passe">
                @include('profile.partials.update-password-form')
            </x-card>

            <x-card header="Supprimer le compte">
                @include('profile.partials.delete-user-form')
            </x-card>
        </div>
    </div>
</x-app-layout>
