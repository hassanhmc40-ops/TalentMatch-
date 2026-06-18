<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Mes offres d'emploi
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 text-sm font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if ($offers->isEmpty())
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                        <p class="text-lg">Vous n'avez pas encore créé d'offre d'emploi.</p>
                        <a href="{{ route('offres.create') }}" class="mt-4 inline-block text-indigo-600 dark:text-indigo-400 hover:underline">
                            Créer votre première offre
                        </a>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="pb-3 font-semibold">Titre</th>
                                    <th class="pb-3 font-semibold">Compétences</th>
                                    <th class="pb-3 font-semibold">Exp. min.</th>
                                    <th class="pb-3 font-semibold">Créé le</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($offers as $offer)
                                    <tr class="border-b border-gray-100 dark:border-gray-700 last:border-0">
                                        <td class="py-3 font-medium">
                                            <a href="{{ route('offres.show', $offer) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                                {{ $offer->title }}
                                            </a>
                                        </td>
                                        <td class="py-3 text-sm text-gray-600 dark:text-gray-400">
                                            {{ implode(', ', $offer->required_skills ?? []) }}
                                        </td>
                                        <td class="py-3 text-sm">{{ $offer->min_experience_years }} an{{ $offer->min_experience_years > 1 ? 's' : '' }}</td>
                                        <td class="py-3 text-sm">{{ $offer->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-6">
                    {{ $offers->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
