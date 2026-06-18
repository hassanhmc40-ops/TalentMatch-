<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-neutral-900 leading-tight">
                    Soumettre un candidat
                </h2>
                <p class="text-sm text-neutral-500 mt-1">{{ $offre->title }}</p>
            </div>
            <a href="{{ route('offres.show', $offre) }}">
                <x-button variant="outline" size="sm">Retour à l'offre</x-button>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                @if (session('success'))
                    <x-alert type="success" dismissible>{{ session('success') }}</x-alert>
                @endif

                <form method="POST" action="{{ route('offres.candidats.submit', $offre) }}" class="space-y-6">
                    @csrf

                    <x-input name="nom" label="Nom du candidat" :value="old('nom')" :message="$errors->first('nom')" required maxlength="255" />

                    <div>
                        <label for="cv_text" class="block text-sm font-medium text-neutral-700">Texte du CV</label>
                        <textarea id="cv_text" name="cv_text" rows="12" required maxlength="50000"
                            class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm text-neutral-900 shadow-sm transition duration-150 ease-in-out focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-0">{{ old('cv_text') }}</textarea>
                        @if ($errors->first('cv_text'))
                            <p class="mt-1 text-sm text-danger-600">{{ $errors->first('cv_text') }}</p>
                        @endif
                    </div>

                    <div class="flex items-center justify-end">
                        <x-button>Soumettre le candidat</x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
