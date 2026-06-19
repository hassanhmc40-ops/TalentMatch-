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
                    <span class="text-neutral-900 font-medium">Comparaison</span>
                </nav>
                <h2 class="font-semibold text-xl text-neutral-900 leading-tight">
                    Comparaison de candidats
                </h2>
                <p class="text-sm text-neutral-500 mt-1">{{ $offre->title }}</p>
            </div>
            <a href="{{ route('offres.show', $offre) }}">
                <x-button variant="outline" size="sm">Retour à l'offre</x-button>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @php
                $analyses = [$analysisA, $analysisB];
                $completedAnalyses = array_filter($analyses, fn($a) => $a->status === 'completed');
                $scoreA = $analysisA->matching_score ?? 0;
                $scoreB = $analysisB->matching_score ?? 0;
                $scoreDiff = abs($scoreA - $scoreB);
                $higherName = $scoreA > $scoreB ? $analysisA->candidate->name : ($scoreB > $scoreA ? $analysisB->candidate->name : null);
            @endphp

            @if (count($completedAnalyses) === 2)
                <x-card class="border-l-4 border-l-primary-500 bg-primary-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-lg font-semibold text-primary-800">
                                Écart de score : {{ $scoreDiff }} point{{ $scoreDiff > 1 ? 's' : '' }}
                            </p>
                            @if ($higherName)
                                <p class="text-sm text-primary-600 mt-1">
                                    {{ $higherName }} a le score le plus élevé
                                </p>
                            @else
                                <p class="text-sm text-primary-600 mt-1">
                                    Les deux candidats ont le même score
                                </p>
                            @endif
                        </div>
                        <div class="text-3xl font-bold text-primary-700">{{ $scoreDiff }}</div>
                    </div>
                </x-card>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach ($analyses as $index => $analyse)
                    @php
                        $isLeft = $index === 0;
                        $score = $analyse->matching_score ?? 0;
                        $scoreLevel = $analyse->status === 'completed' ? $analyse->scoreLevel() : null;
                        $progressVariant = match (true) {
                            $score >= 81 => 'success',
                            $score >= 61 => 'primary',
                            $score >= 31 => 'warning',
                            default => 'danger',
                        };
                    @endphp

                    <x-card>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full bg-neutral-100 flex items-center justify-center text-neutral-600 font-bold text-sm">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                @if ($analyse->status === 'completed')
                                    <a href="{{ route('analyses.show', [$offre, $analyse]) }}" class="text-h4 text-primary-600 hover:text-primary-700 font-semibold">
                                        {{ $analyse->candidate->name }}
                                    </a>
                                @else
                                    <h3 class="text-h4 font-semibold text-neutral-900">{{ $analyse->candidate->name }}</h3>
                                @endif
                                <p class="text-xs text-neutral-500">Candidat {{ $isLeft ? 'A' : 'B' }}</p>
                            </div>
                        </div>

                        @if ($analyse->status === 'pending')
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 mx-auto mb-3 text-primary-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <p class="text-neutral-600 font-medium">Analyse en cours...</p>
                                <p class="text-sm text-neutral-400 mt-1">Revenez dans quelques instants.</p>
                            </div>
                        @elseif ($analyse->status === 'failed')
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 mx-auto mb-3 text-danger-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <p class="text-danger-600 font-medium">Analyse échouée</p>
                                <p class="text-sm text-neutral-400 mt-1">Soumettez à nouveau le candidat.</p>
                            </div>
                        @else
                            <div class="space-y-5">
                                <div>
                                    <h4 class="text-sm font-semibold text-neutral-500 uppercase tracking-wider mb-2">Score de correspondance</h4>
                                    <x-progress
                                        :value="$score"
                                        :variant="$progressVariant"
                                        :label="'Score global — ' . $scoreLevel"
                                    />
                                </div>

                                <div>
                                    <h4 class="text-sm font-semibold text-neutral-500 uppercase tracking-wider mb-2">Recommandation</h4>
                                    <x-recommendation-badge :recommendation="$analyse->recommendation" />
                                </div>

                                <div>
                                    <h4 class="text-sm font-semibold text-neutral-500 uppercase tracking-wider mb-2">Points forts</h4>
                                    @if (!empty($analyse->strengths))
                                        <ul class="space-y-1.5">
                                            @foreach ($analyse->strengths as $strength)
                                                <li class="flex items-start gap-2">
                                                    <svg class="w-4 h-4 text-success-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <span class="text-sm text-neutral-700">{{ $strength }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-sm text-neutral-400">Aucun</p>
                                    @endif
                                </div>

                                <div>
                                    <h4 class="text-sm font-semibold text-neutral-500 uppercase tracking-wider mb-2">Lacunes</h4>
                                    @if (!empty($analyse->gaps))
                                        <ul class="space-y-1.5">
                                            @foreach ($analyse->gaps as $gap)
                                                <li class="flex items-start gap-2">
                                                    <svg class="w-4 h-4 text-warning-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <span class="text-sm text-neutral-700">{{ $gap }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-sm text-neutral-400">Aucun</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </x-card>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
