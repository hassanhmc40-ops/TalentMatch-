<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $offre->title }}
            </h2>
            <a href="{{ route('offres.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                Retour à la liste
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Offer Criteria --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Critères de l'offre</h3>

                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Titre</dt>
                            <dd class="mt-1 font-medium">{{ $offre->title }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Description</dt>
                            <dd class="mt-1">{{ $offre->description }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Compétences requises</dt>
                            <dd class="mt-1">
                                @forelse ($offre->required_skills ?? [] as $skill)
                                    <span class="inline-block bg-gray-100 dark:bg-gray-700 text-sm px-3 py-1 rounded-full mr-1 mb-1">{{ $skill }}</span>
                                @empty
                                    <span class="text-gray-400">Aucune compétence requise</span>
                                @endforelse
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Années d'expérience minimum</dt>
                            <dd class="mt-1">{{ $offre->min_experience_years }} an{{ $offre->min_experience_years > 1 ? 's' : '' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Candidate Analyses --}}
            <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Candidats analysés</h3>
                        <a href="{{ route('offres.candidats.create', $offre) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                            Soumettre un candidat
                        </a>
                    </div>

                    @if ($offre->candidateAnalyses->isEmpty())
                        <p class="text-gray-500 dark:text-gray-400">Aucun candidat analysé pour cette offre.</p>
                    @else
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="pb-3 font-semibold">Candidat</th>
                                    <th class="pb-3 font-semibold">Score</th>
                                    <th class="pb-3 font-semibold">Recommandation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($offre->candidateAnalyses as $analysis)
                                    <tr class="border-b border-gray-100 dark:border-gray-700 last:border-0">
                                        <td class="py-3">{{ $analysis->candidate->name }}</td>
                                        <td class="py-3">
                                            <span class="font-mono {{ $analysis->matching_score >= 70 ? 'text-green-600' : ($analysis->matching_score >= 40 ? 'text-yellow-600' : 'text-red-600') }}">
                                                {{ $analysis->matching_score }}%
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            @php
                                                $badge = match ($analysis->recommendation?->value) {
                                                    'convoquer' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                                    'attente' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                                    'rejeter' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400',
                                                };
                                            @endphp
                                            <span class="inline-block px-3 py-1 rounded-full text-sm font-medium {{ $badge }}">
                                                {{ $analysis->recommendation?->label() ?? 'Non défini' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
