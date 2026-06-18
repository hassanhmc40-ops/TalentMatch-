<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-neutral-900 leading-tight">
                    {{ $offre->title }}
                </h2>
                <p class="text-sm text-neutral-500 mt-1">Détails de l'offre et candidats soumis</p>
            </div>
            <a href="{{ route('offres.index') }}">
                <x-button variant="outline" size="sm">Retour à la liste</x-button>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-card>
                <h3 class="text-h3 mb-4">Critères de l'offre</h3>

                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm text-neutral-500">Titre</dt>
                        <dd class="mt-1 font-medium text-neutral-900">{{ $offre->title }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-neutral-500">Description</dt>
                        <dd class="mt-1 text-neutral-700">{{ $offre->description }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-neutral-500">Compétences requises</dt>
                        <dd class="mt-1">
                            @forelse ($offre->required_skills ?? [] as $skill)
                                <x-badge variant="neutral" class="mr-1 mb-1">{{ $skill }}</x-badge>
                            @empty
                                <span class="text-neutral-400 text-sm">Aucune compétence requise</span>
                            @endforelse
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-neutral-500">Années d'expérience minimum</dt>
                        <dd class="mt-1 font-medium text-neutral-900">{{ $offre->min_experience_years }} an{{ $offre->min_experience_years > 1 ? 's' : '' }}</dd>
                    </div>
                </dl>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-h4">Candidats analysés</h3>
                        <p class="text-sm text-neutral-500 mt-0.5">Liste des candidats soumis à cette offre</p>
                    </div>
                    <a href="{{ route('offres.candidats.create', $offre) }}">
                        <x-button size="sm">Soumettre un candidat</x-button>
                    </a>
                </div>

                @if ($offre->candidateAnalyses->isEmpty())
                    <p class="text-neutral-500 text-sm py-4 text-center">Aucun candidat analysé pour cette offre.</p>
                @else
                    @php
                        $headers = [
                            ['key' => 'candidate', 'label' => 'Candidat'],
                            ['key' => 'score', 'label' => 'Score'],
                            ['key' => 'recommendation', 'label' => 'Recommandation'],
                            ['key' => 'actions', 'label' => ''],
                        ];

                        $rows = $offre->candidateAnalyses->map(fn($a) => [
                            'candidate' => $a->candidate->name,
                            'score' => $a->matching_score . '%',
                            'recommendation' => $a->recommendation?->label() ?? 'Non défini',
                            'actions' => '<a href="' . route('conversations.show', [$offre, $a->candidate]) . '" class="text-primary-600 hover:text-primary-700 text-sm font-medium">Assistant →</a>',
                        ]);
                    @endphp
                    <x-table :headers="$headers" :rows="$rows" />
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
