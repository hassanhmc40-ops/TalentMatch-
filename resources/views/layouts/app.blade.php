<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} — @yield('title', 'Tableau de bord')</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body
        x-data="{
            sidebarCollapsed: localStorage.getItem('sidebar_collapsed') === 'true',
            mobileSidebarOpen: false,
        }"
        x-on:toggle-sidebar.window="mobileSidebarOpen = !mobileSidebarOpen"
        :class="sidebarCollapsed ? 'sidebar-collapsed' : ''"
        class="font-sans antialiased text-neutral-900 bg-neutral-100"
    >
        <x-sidebar />

        <div
            class="min-h-screen flex flex-col transition-all duration-300 ease-in-out"
            :class="sidebarCollapsed ? 'md:ml-16' : 'md:ml-64'"
        >
            <x-topbar />

            <main class="flex-1 p-4 lg:p-8">
                @isset($header)
                    <header class="mb-6">
                        {{ $header }}
                    </header>
                @endisset

                {{ $slot }}
            </main>
        </div>

        @stack('scripts')
    </body>
</html>
