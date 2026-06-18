<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 text-sm font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <a href="{{ route('offres.index') }}"
                   class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 transition block">
                    <h3 class="text-lg font-semibold">Mes offres d'emploi</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Consultez et gérez vos offres d'emploi</p>
                </a>

                <a href="{{ route('offres.create') }}"
                   class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 transition block">
                    <h3 class="text-lg font-semibold">Créer une offre</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Publiez une nouvelle offre d'emploi</p>
                </a>

            </div>
        </div>
    </div>
</x-app-layout>
