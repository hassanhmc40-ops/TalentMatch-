<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-h3 font-bold text-neutral-900">Tableau de bord</h1>
            <p class="text-sm text-neutral-500 mt-1">Bienvenue, {{ Auth::user()->name }}</p>
        </div>
    </x-slot>

    @if (session('success'))
        <x-alert type="success" dismissible class="mb-6">{{ session('success') }}</x-alert>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-kpi-card
            icon="offres"
            :value="$totalOffers"
            label="Offres d'emploi"
            color="primary"
        />

        <x-kpi-card
            icon="candidats"
            :value="$analyzedCandidates"
            label="Candidats analysés"
            color="success"
        />

        <x-kpi-card
            icon="score"
            :value="$avgScore"
            label="Score moyen"
            color="warning"
        />

        <x-kpi-card
            icon="pending"
            :value="$pendingAnalyses"
            label="Analyses en attente"
            color="danger"
        />
    </div>

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
</x-app-layout>
