<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex items-center gap-2 text-sm text-neutral-500 mb-1">
                    <a href="{{ route('offres.index') }}" class="hover:text-primary-600 transition-colors">Mes offres</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <a href="{{ route('offres.show', $offre) }}" class="hover:text-primary-600 transition-colors">{{ $offre->title }}</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-neutral-900 font-medium">Analyse de {{ $analyse->candidate->name }}</span>
                </nav>
                <h2 class="font-semibold text-xl text-neutral-900 leading-tight">
                    Analyse de {{ $analyse->candidate->name }}
                </h2>
                <p class="text-sm text-neutral-500 mt-1">{{ $offre->title }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('conversations.show', [$offre, $analyse->candidate]) }}">
                    <x-button variant="outline" size="sm">Assistant →</x-button>
                </a>
                <a href="{{ route('offres.show', $offre) }}">
                    <x-button variant="ghost" size="sm">Retour à l'offre</x-button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if ($analyse->status === 'pending')
                <x-card>
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto mb-4 text-primary-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <h3 class="text-h4 text-neutral-700 mb-2">Analyse en cours...</h3>
                        <p class="text-neutral-500">L'analyse du CV de {{ $analyse->candidate->name }} est en cours. Revenez dans quelques instants.</p>
                    </div>
                </x-card>
            @elseif ($analyse->status === 'failed')
                <x-card>
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto mb-4 text-danger-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <h3 class="text-h4 text-danger-700 mb-2">Analyse échouée</h3>
                        <p class="text-neutral-500 mb-6">L'analyse du CV de {{ $analyse->candidate->name }} n'a pas pu être effectuée.</p>
                        <a href="{{ route('offres.candidats.create', $offre) }}">
                            <x-button variant="primary" size="sm">Soumettre à nouveau</x-button>
                        </a>
                    </div>
                </x-card>
            @else
                @php
                    $score = $analyse->matching_score;
                    $scoreLevel = $analyse->scoreLevel();

                    $progressVariant = match (true) {
                        $score >= 81 => 'success',
                        $score >= 61 => 'primary',
                        $score >= 31 => 'warning',
                        default => 'danger',
                    };

                    $recommendationVariant = match ($analyse->recommendation?->value) {
                        'convoquer' => 'success',
                        'attente' => 'warning',
                        'rejeter' => 'danger',
                        default => 'neutral',
                    };
                @endphp

                <x-card>
                    <h3 class="text-h4 mb-4">Compétences extraites</h3>
                    @if (!empty($analyse->extracted_skills))
                        <div class="flex flex-wrap gap-2">
                            @foreach ($analyse->extracted_skills as $skill)
                                <x-badge variant="info">{{ $skill }}</x-badge>
                            @endforeach
                        </div>
                    @else
                        <p class="text-neutral-400 text-sm">Aucune compétence extraite</p>
                    @endif
                </x-card>

                <x-card>
                    <h3 class="text-h4 mb-4">Profil du candidat</h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div>
                            <dt class="text-sm text-neutral-500">Années d'expérience</dt>
                            <dd class="mt-1 font-medium text-neutral-900">{{ $analyse->years_experience }} an{{ $analyse->years_experience > 1 ? 's' : '' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-neutral-500">Niveau d'études</dt>
                            <dd class="mt-1 font-medium text-neutral-900">{{ $analyse->education_level ?: 'Non spécifié' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-neutral-500">Langues</dt>
                            <dd class="mt-1">
                                @if (!empty($analyse->languages))
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($analyse->languages as $language)
                                            <x-badge variant="neutral">{{ $language }}</x-badge>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-neutral-400 text-sm">Aucune langue spécifiée</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </x-card>

                <x-card>
                    <h3 class="text-h4 mb-4">Score de correspondance</h3>
                    <x-progress
                        :value="$score"
                        :variant="$progressVariant"
                        :label="'Score global — ' . $scoreLevel"
                    />
                    <p class="mt-3 text-sm text-neutral-500">
                        Le score mesure la correspondance entre le profil du candidat et les critères de l'offre.
                    </p>
                </x-card>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-card>
                        <h3 class="text-h4 mb-4">Points forts</h3>
                        @if (!empty($analyse->strengths))
                            <ul class="space-y-2">
                                @foreach ($analyse->strengths as $strength)
                                    <li class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-success-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-neutral-700">{{ $strength }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-neutral-400 text-sm">Aucun point fort identifié</p>
                        @endif
                    </x-card>

                    <x-card>
                        <h3 class="text-h4 mb-4">Lacunes</h3>
                        @if (!empty($analyse->gaps))
                            <ul class="space-y-2">
                                @foreach ($analyse->gaps as $gap)
                                    <li class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-warning-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-neutral-700">{{ $gap }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-neutral-400 text-sm">Aucune lacune identifiée</p>
                        @endif
                    </x-card>
                </div>

                <x-card>
                    <h3 class="text-h4 mb-4">Compétences manquantes</h3>
                    @if (!empty($analyse->missing_skills))
                        <div class="flex flex-wrap gap-2">
                            @foreach ($analyse->missing_skills as $skill)
                                <x-badge variant="danger">{{ $skill }}</x-badge>
                            @endforeach
                        </div>
                    @else
                        <p class="text-neutral-400 text-sm">Aucune compétence manquante</p>
                    @endif
                </x-card>

                <x-card>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-h4">Recommandation</h3>
                        <x-badge :variant="$recommendationVariant" class="text-sm px-3 py-1">
                            {{ $analyse->recommendation?->label() ?? 'Non définie' }}
                        </x-badge>
                    </div>
                    <p class="text-neutral-700 leading-relaxed">{{ $analyse->justification }}</p>
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>
