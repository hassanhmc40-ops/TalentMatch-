@props(['collapsed' => false, 'mobileOpen' => false])

@php
$links = [
    ['label' => 'Tableau de bord', 'route' => 'dashboard', 'icon' => 'dashboard', 'pattern' => 'dashboard'],
    ['label' => 'Mes offres', 'route' => 'offres.index', 'icon' => 'offres', 'pattern' => 'offres.*'],
    ['label' => 'Candidats', 'route' => 'offres.index', 'icon' => 'candidats', 'pattern' => 'candidats.*'],
];

$icons = [
    'dashboard' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    'offres' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    'candidats' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
];
@endphp

<aside
    :class="sidebarCollapsed ? 'w-16' : 'w-64'"
    class="fixed inset-y-0 left-0 z-30 bg-white border-r border-neutral-200 flex-col transition-all duration-300 ease-in-out hidden md:flex"
>
    <div class="flex items-center h-16 px-4 border-b border-neutral-200">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <x-application-logo class="h-8 w-auto flex-shrink-0 fill-current text-primary-600" />
            <span x-show="!sidebarCollapsed" class="font-bold text-lg text-neutral-900 whitespace-nowrap">TalentMatch</span>
        </a>
    </div>

    <nav class="flex-1 py-4 space-y-1 overflow-y-auto">
        @foreach ($links as $link)
            @php $active = request()->routeIs($link['pattern']); @endphp
            <a
                href="{{ route($link['route']) }}"
                :class="sidebarCollapsed ? 'justify-center mx-2' : ''"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium transition-colors duration-150 rounded-lg mx-2 {{ $active ? 'bg-primary-50 text-primary-700' : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900' }}"
            >
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons[$link['icon']] }}" />
                </svg>
                <span x-show="!sidebarCollapsed">{{ $link['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="p-4 border-t border-neutral-200">
        <button
            x-on:click="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('sidebar_collapsed', sidebarCollapsed)"
            class="flex items-center justify-center w-full gap-2 px-3 py-2 text-sm text-neutral-500 hover:text-neutral-700 hover:bg-neutral-100 rounded-lg transition-colors duration-150"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
            <span x-show="!sidebarCollapsed">Réduire</span>
        </button>
    </div>
</aside>

<div
    x-show="mobileSidebarOpen"
    x-cloak
    class="fixed inset-0 z-40 md:hidden"
>
    <div class="absolute inset-0 bg-neutral-900/50" x-on:click="mobileSidebarOpen = false"></div>
    <aside class="relative w-64 h-full bg-white shadow-lg overflow-y-auto">
        <div class="flex items-center justify-between h-16 px-4 border-b border-neutral-200">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <x-application-logo class="h-8 w-auto fill-current text-primary-600" />
                <span class="font-bold text-lg text-neutral-900">TalentMatch</span>
            </a>
            <button x-on:click="mobileSidebarOpen = false" class="text-neutral-400 hover:text-neutral-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <nav class="py-4 space-y-1">
            @foreach ($links as $link)
                @php $active = request()->routeIs($link['pattern']); @endphp
                <a
                    href="{{ route($link['route']) }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm font-medium {{ $active ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-600' : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900' }}"
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons[$link['icon']] }}" />
                    </svg>
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>
    </aside>
</div>
