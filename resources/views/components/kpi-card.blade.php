@props([
    'icon' => '',
    'value' => 0,
    'label' => '',
    'color' => 'primary',
    'trend' => 'neutral',
])

@php
$colorClasses = [
    'primary' => 'bg-primary-50 text-primary-600',
    'success' => 'bg-success-50 text-success-600',
    'warning' => 'bg-warning-50 text-warning-600',
    'danger' => 'bg-danger-50 text-danger-600',
];

$trendIcons = [
    'up' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
    'down' => 'M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6',
    'neutral' => 'M12 5v14',
];

$iconClasses = $colorClasses[$color] ?? $colorClasses['primary'];
$trendPath = $trendIcons[$trend] ?? $trendIcons['neutral'];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow-card border border-neutral-200 p-6']) }}>
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-neutral-500">{{ $label }}</p>
            <p class="mt-2 text-3xl font-bold text-neutral-900">{{ $value }}</p>
        </div>
        @if ($icon)
            <div class="w-12 h-12 rounded-lg {{ $iconClasses }} flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if ($icon === 'briefcase' || $icon === 'offres')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    @elseif ($icon === 'users' || $icon === 'candidats')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    @elseif ($icon === 'chart' || $icon === 'score')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    @elseif ($icon === 'clock' || $icon === 'pending')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    @endif
                </svg>
            </div>
        @endif
    </div>
</div>
