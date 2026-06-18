<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-neutral-900 leading-tight">
                Créer une offre d'emploi
            </h2>
            <p class="text-sm text-neutral-500 mt-1">Définissez les critères de sélection pour cette offre</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                @if (session('success'))
                    <x-alert type="success" dismissible class="mb-6">{{ session('success') }}</x-alert>
                @endif

                <form method="POST" action="{{ route('offres.store') }}" class="space-y-6">
                    @csrf

                    <x-input name="title" label="Titre de l'offre" :value="old('title')" :message="$errors->first('title')" required />

                    <div>
                        <label for="description" class="block text-sm font-medium text-neutral-700">Description</label>
                        <textarea id="description" name="description" rows="6" required
                            class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm text-neutral-900 shadow-sm transition duration-150 ease-in-out focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-0">{{ old('description') }}</textarea>
                        @if ($errors->first('description'))
                            <p class="mt-1 text-sm text-danger-600">{{ $errors->first('description') }}</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700">Compétences requises</label>
                        <div class="mt-1 space-y-2">
                            <input name="required_skills[]" placeholder="PHP"
                                class="block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <input name="required_skills[]" placeholder="Laravel"
                                class="block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <input name="required_skills[]" placeholder="MySQL"
                                class="block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <p class="mt-1 text-sm text-neutral-500">Ajoutez chaque compétence dans un champ.</p>
                        @if ($errors->first('required_skills') || $errors->first('required_skills.*'))
                            <p class="mt-1 text-sm text-danger-600">{{ $errors->first('required_skills') ?: $errors->first('required_skills.*') }}</p>
                        @endif
                    </div>

                    <x-input name="min_experience_years" label="Années d'expérience minimum" type="number" :value="old('min_experience_years', 0)" :message="$errors->first('min_experience_years')" min="0" max="50" />

                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('offres.index') }}">
                            <x-button variant="outline">Annuler</x-button>
                        </a>
                        <x-button>Créer l'offre</x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
