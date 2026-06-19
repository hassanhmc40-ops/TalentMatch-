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
            :trend="$trends['totalOffers']"
        />

        <x-kpi-card
            icon="candidats"
            :value="$analyzedCandidates"
            label="Candidats analysés"
            color="success"
            :trend="$trends['analyzedCandidates']"
        />

        <x-kpi-card
            icon="score"
            :value="$avgScore"
            label="Score moyen"
            color="warning"
            :trend="$trends['avgScore']"
        />

        <x-kpi-card
            icon="pending"
            :value="$pendingAnalyses"
            label="Analyses en attente"
            color="danger"
            :trend="$trends['pendingAnalyses']"
        />
    </div>

    @php
        $maxBand = max($scoreBands) ?: 1;
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <x-card>
            <h3 class="text-h4 mb-1">Distribution des scores</h3>
            <p class="text-sm text-neutral-500 mb-6">Répartition des scores de correspondance</p>

            @if (array_sum($scoreBands) === 0)
                <p class="text-neutral-400 text-sm py-8 text-center">Aucune analyse disponible</p>
            @else
                <div class="flex items-end justify-between gap-4 h-48">
                    @foreach ($scoreBands as $i => $count)
                        @php
                            $height = $count > 0 ? max(8, ($count / $maxBand) * 100) : 8;
                            $colorClass = match ($scoreColors[$i]) {
                                'danger' => 'bg-danger-500',
                                'warning' => 'bg-warning-500',
                                'primary' => 'bg-primary-500',
                                'success' => 'bg-success-500',
                                default => 'bg-neutral-300',
                            };
                        @endphp
                        <div class="flex-1 flex flex-col items-center">
                            <span class="text-sm font-semibold text-neutral-700 mb-1">{{ $count }}</span>
                            <div class="w-full flex justify-center">
                                <div class="w-10 rounded-t-md {{ $colorClass }} transition-all duration-500" style="height: {{ $height }}%"></div>
                            </div>
                            <span class="text-xs text-neutral-500 mt-2">{{ $scoreLabels[$i] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>

        <x-card>
            <h3 class="text-h4 mb-1">Statut des analyses</h3>
            <p class="text-sm text-neutral-500 mb-6">Vue d'ensemble par statut</p>

            <dl class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-success-500"></span>
                        <span class="text-sm text-neutral-700">Terminées</span>
                    </div>
                    <span class="font-semibold text-neutral-900">{{ $scoreBands[0] + $scoreBands[1] + $scoreBands[2] + $scoreBands[3] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-warning-500"></span>
                        <span class="text-sm text-neutral-700">En attente</span>
                    </div>
                    <span class="font-semibold text-neutral-900">{{ $pendingAnalyses }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-danger-500"></span>
                        <span class="text-sm text-neutral-700">Échouées</span>
                    </div>
                    <span class="font-semibold text-neutral-900">{{ $failedAnalyses }}</span>
                </div>
            </dl>
        </x-card>
    </div>

    <x-card>
        <div x-data="{ tab: 'all' }">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-h4">Analyses récentes</h3>
                    <p class="text-sm text-neutral-500 mt-0.5">Les 10 dernières analyses</p>
                </div>
                <div class="flex gap-1 bg-neutral-100 rounded-lg p-0.5">
                    <button @click="tab = 'all'" :class="{ 'bg-white shadow-sm': tab === 'all' }" class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors text-neutral-600 hover:text-neutral-900">Tous</button>
                    <button @click="tab = 'completed'" :class="{ 'bg-white shadow-sm': tab === 'completed' }" class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors text-neutral-600 hover:text-neutral-900">Terminés</button>
                    <button @click="tab = 'pending'" :class="{ 'bg-white shadow-sm': tab === 'pending' }" class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors text-neutral-600 hover:text-neutral-900">En attente</button>
                    <button @click="tab = 'failed'" :class="{ 'bg-white shadow-sm': tab === 'failed' }" class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors text-neutral-600 hover:text-neutral-900">Échoués</button>
                </div>
            </div>

            @foreach (['all', 'completed', 'pending', 'failed'] as $status)
                @php
                    $items = $analysesByStatus[$status] ?? collect();
                    $statusLabels = ['all' => 'Tous', 'completed' => 'Terminés', 'pending' => 'En attente', 'failed' => 'Échoués'];
                    $statusColors = ['all' => 'neutral', 'completed' => 'success', 'pending' => 'warning', 'failed' => 'danger'];
                @endphp
                <div x-show="tab === '{{ $status }}'" x-cloak>
                    @if ($items->isEmpty())
                        <p class="text-neutral-400 text-sm py-8 text-center">Aucune analyse trouvée</p>
                    @else
                        <div class="overflow-x-auto rounded-lg border border-neutral-200">
                            <table class="min-w-full divide-y divide-neutral-200">
                                <thead class="bg-neutral-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">Candidat</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">Offre</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">Score</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">Recommandation</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">Statut</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">Date</th>
                                        <th class="px-4 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-neutral-200">
                                    @foreach ($items as $analysis)
                                        <tr class="hover:bg-neutral-50 transition-colors duration-150">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-neutral-900">{{ $analysis->candidate->name }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-600">{{ $analysis->jobOffer->title }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                @php
                                                    $scoreVariant = match (true) {
                                                        $analysis->matching_score >= 81 => 'success',
                                                        $analysis->matching_score >= 61 => 'primary',
                                                        $analysis->matching_score >= 31 => 'warning',
                                                        default => 'danger',
                                                    };
                                                @endphp
                                                <x-badge :variant="$scoreVariant">{{ $analysis->matching_score }}%</x-badge>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                <x-recommendation-badge :recommendation="$analysis->recommendation" />
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                @php
                                                    $statusVariant = match ($analysis->status) {
                                                        'completed' => 'success',
                                                        'pending' => 'warning',
                                                        'failed' => 'danger',
                                                        default => 'neutral',
                                                    };
                                                    $statusLabel = match ($analysis->status) {
                                                        'completed' => 'Terminée',
                                                        'pending' => 'En attente',
                                                        'failed' => 'Échouée',
                                                        default => $analysis->status,
                                                    };
                                                @endphp
                                                <x-badge :variant="$statusVariant">{{ $statusLabel }}</x-badge>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-500">{{ $analysis->created_at->format('d/m/Y') }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right">
                                                <a href="{{ route('analyses.show', [$analysis->jobOffer, $analysis]) }}" class="text-primary-600 hover:text-primary-700 font-medium">Voir</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </x-card>

    <x-card class="mt-6">
        <div x-data="conversationsState()">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-h4">Conversations récentes</h3>
                    <p class="text-sm text-neutral-500 mt-0.5">Les 10 dernières conversations</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 mb-4">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" x-model="search" placeholder="Rechercher par candidat ou offre..." class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                </div>
                <div>
                    <input type="date" x-model="dateFrom" placeholder="Du" class="rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                </div>
                <div>
                    <input type="date" x-model="dateTo" placeholder="Au" class="rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                </div>
            </div>

            <template x-if="filteredConversations.length === 0">
                <p class="text-neutral-400 text-sm py-8 text-center">Aucune conversation trouvée</p>
            </template>

            <template x-if="filteredConversations.length > 0">
                <div class="overflow-x-auto rounded-lg border border-neutral-200">
                    <table class="min-w-full divide-y divide-neutral-200">
                        <thead class="bg-neutral-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">Candidat</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">Offre</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">Titre</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">Messages</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">Dernier message</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">Activité</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-neutral-200">
                            <template x-for="conv in filteredConversations" :key="conv.id">
                                <tr class="hover:bg-neutral-50 transition-colors duration-150 cursor-pointer" @click="window.location.href = conv.url">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-neutral-900" x-text="conv.candidate_name"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-600" x-text="conv.offer_title"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-600" x-text="conv.title || 'Sans titre'"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        <span class="inline-flex items-center justify-center min-w-[1.5rem] h-5 px-1.5 rounded-full bg-primary-100 text-primary-700 text-xs font-medium" x-text="conv.message_count"></span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-500 max-w-[200px] truncate" x-text="conv.last_message"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-neutral-500" x-text="conv.last_activity"></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right">
                                        <a :href="conv.url" class="text-primary-600 hover:text-primary-700 font-medium">Ouvrir</a>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </template>
        </div>
    </x-card>
</x-app-layout>

@push('scripts')
<script>
function conversationsState() {
    return {
        search: '',
        dateFrom: '',
        dateTo: '',
        conversations: @json($recentConversations),
        get filteredConversations() {
            let items = this.conversations;
            if (this.search) {
                const q = this.search.toLowerCase();
                items = items.filter(c => c.candidate_name.toLowerCase().includes(q) || c.offer_title.toLowerCase().includes(q));
            }
            if (this.dateFrom) {
                const from = new Date(this.dateFrom);
                items = items.filter(c => new Date(c.updated_at) >= from);
            }
            if (this.dateTo) {
                const to = new Date(this.dateTo);
                to.setHours(23, 59, 59, 999);
                items = items.filter(c => new Date(c.updated_at) <= to);
            }
            return items;
        }
    };
}
</script>
@endpush
