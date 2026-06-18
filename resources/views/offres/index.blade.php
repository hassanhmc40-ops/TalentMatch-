<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-neutral-900 leading-tight">
                    Mes offres d'emploi
                </h2>
                <p class="text-sm text-neutral-500 mt-1">Gérez vos offres et suivez les candidatures</p>
            </div>
            <a href="{{ route('offres.create') }}">
                <x-button size="sm">Nouvelle offre</x-button>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <x-alert type="success" dismissible>{{ session('success') }}</x-alert>
            @endif

            @if ($offers->isEmpty())
                <x-card>
                    <div class="text-center py-8">
                        <p class="text-neutral-500">Vous n'avez pas encore créé d'offre d'emploi.</p>
                        <a href="{{ route('offres.create') }}" class="mt-4 inline-block text-primary-600 hover:underline text-sm">
                            Créer votre première offre
                        </a>
                    </div>
                </x-card>
            @else
                <x-card>
                    <x-table
                        :headers="[
                            ['key' => 'title', 'label' => 'Titre'],
                            ['key' => 'skills', 'label' => 'Compétences'],
                            ['key' => 'experience', 'label' => 'Exp. min.'],
                            ['key' => 'created', 'label' => 'Créé le'],
                        ]"
                        :rows="$offers->getCollection()->map(fn($o) => [
                            'title' => $o->title,
                            'skills' => implode(', ', $o->required_skills ?? []),
                            'experience' => $o->min_experience_years . ' an' . ($o->min_experience_years > 1 ? 's' : ''),
                            'created' => $o->created_at->format('d/m/Y'),
                        ])"
                    >
                        <x-slot name="actions">
                            <a href="#" class="text-primary-600 hover:text-primary-800 text-sm font-medium">Voir</a>
                        </x-slot>
                    </x-table>

                    <div class="mt-4">
                        {{ $offers->links() }}
                    </div>
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>
