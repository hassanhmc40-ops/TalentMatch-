<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Créer une offre d'emploi
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (session('success'))
                        <div class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('offres.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="title" value="Titre de l'offre" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div>
                            <x-input-label for="description" value="Description" />
                            <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" rows="6">{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div>
                            <x-input-label for="required_skills" value="Compétences requises" />
                            <x-text-input id="required_skills" name="required_skills[]" type="text" class="mt-1 block w-full" placeholder="PHP" />
                            <x-text-input id="required_skills" name="required_skills[]" type="text" class="mt-1 block w-full" placeholder="Laravel" />
                            <x-text-input id="required_skills" name="required_skills[]" type="text" class="mt-1 block w-full" placeholder="MySQL" />
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ajoutez chaque compétence dans un champ. Laissez les champs vides si vous avez moins de 3 compétences.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('required_skills')" />
                            <x-input-error class="mt-2" :messages="$errors->get('required_skills.*')" />
                        </div>

                        <div>
                            <x-input-label for="min_experience_years" value="Années d'expérience minimum" />
                            <x-text-input id="min_experience_years" name="min_experience_years" type="number" class="mt-1 block w-full" :value="old('min_experience_years', 0)" min="0" max="50" />
                            <x-input-error class="mt-2" :messages="$errors->get('min_experience_years')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>Créer l'offre</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
