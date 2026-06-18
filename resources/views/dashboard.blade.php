<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-neutral-900 leading-tight">
                Tableau de bord
            </h2>
            <p class="text-sm text-neutral-500 mt-1">Bienvenue, {{ Auth::user()->name }}</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <x-alert type="success" dismissible class="mb-6">{{ session('success') }}</x-alert>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <a href="{{ route('offres.index') }}"
                   class="bg-white rounded-lg shadow-card border border-neutral-200 p-6 text-neutral-900 hover:bg-neutral-50 transition block">
                    <h3 class="text-h4">Mes offres d'emploi</h3>
                    <p class="mt-1 text-sm text-neutral-500">Consultez et gérez vos offres d'emploi</p>
                </a>

                <a href="{{ route('offres.create') }}"
                   class="bg-white rounded-lg shadow-card border border-neutral-200 p-6 text-neutral-900 hover:bg-neutral-50 transition block">
                    <h3 class="text-h4">Créer une offre</h3>
                    <p class="mt-1 text-sm text-neutral-500">Publiez une nouvelle offre d'emploi</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
