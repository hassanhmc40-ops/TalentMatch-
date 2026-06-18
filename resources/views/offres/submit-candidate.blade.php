<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Soumettre un candidat — {{ $offre->title }}
            </h2>
            <a href="{{ route('offres.show', $offre) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                Retour à l'offre
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 text-sm font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('offres.candidats.submit', $offre) }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="nom" value="Nom du candidat" />
                            <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full"
                                :value="old('nom')" required maxlength="255" />
                            <x-input-error :messages="$errors->get('nom')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="cv_text" value="Texte du CV" />
                            <textarea id="cv_text" name="cv_text" rows="12"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required maxlength="50000">{{ old('cv_text') }}</textarea>
                            <x-input-error :messages="$errors->get('cv_text')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end">
                            <x-primary-button>Soumettre le candidat</x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
