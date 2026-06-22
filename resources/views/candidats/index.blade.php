<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-neutral-900 leading-tight">
                    Candidats
                </h2>
                <p class="text-sm text-neutral-500 mt-1">Tous les candidats analysés</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if ($analyses->isEmpty())
                <x-card>
                    <div class="text-center py-8">
                        <p class="text-neutral-500">Aucun candidat pour le moment.</p>
                        <a href="{{ route('offres.index') }}" class="mt-4 inline-block text-primary-600 hover:underline text-sm">
                            Voir les offres d'emploi
                        </a>
                    </div>
                </x-card>
            @else
                <x-card>
                    <x-table
                        :headers="[
                            ['key' => 'candidat', 'label' => 'Candidat'],
                            ['key' => 'offre', 'label' => 'Offre'],
                            ['key' => 'score', 'label' => 'Score'],
                            ['key' => 'recommandation', 'label' => 'Recommandation'],
                            ['key' => 'date', 'label' => 'Date'],
                            ['key' => 'actions', 'label' => ''],
                        ]"
                        :rows="$analyses->map(fn($a) => [
                            'candidat' => $a->candidate?->name ?? 'Inconnu',
                            'offre' => $a->jobOffer?->title ?? 'N/A',
                            'score' => $a->matching_score . '%',
                            'recommandation' => match ($a->recommendation?->value) {
                                'convoquer' => 'À convoquer',
                                'attente' => 'En attente',
                                'rejeter' => 'À rejeter',
                                default => 'N/A',
                            },
                            'date' => $a->created_at->format('d/m/Y'),
                            'actions' => '<a href=\''.route('analyses.show', [$a->jobOffer, $a]).'\' class=\'text-primary-600 hover:text-primary-800 text-sm font-medium\'>Voir</a>',
                        ])"
                        :raw-keys="['actions']"
                    >
                    </x-table>

                    <div class="mt-4">
                        {{ $analyses->links() }}
                    </div>
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>
