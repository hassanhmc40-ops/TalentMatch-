@props([
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-semibold rounded-lg transition ease-in-out duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2';

    $variantClasses = [
        'primary' => 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500 active:bg-primary-800',
        'secondary' => 'bg-white text-neutral-700 border border-neutral-300 hover:bg-neutral-50 focus:ring-primary-500 active:bg-neutral-100',
        'outline' => 'bg-transparent text-primary-600 border border-primary-600 hover:bg-primary-50 focus:ring-primary-500 active:bg-primary-100',
        'danger' => 'bg-danger-600 text-white hover:bg-danger-700 focus:ring-danger-500 active:bg-danger-800',
        'ghost' => 'bg-transparent text-neutral-600 hover:bg-neutral-100 focus:ring-primary-500 active:bg-neutral-200',
    ];

    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-sm gap-1.5',
        'md' => 'px-4 py-2 text-sm gap-2',
        'lg' => 'px-6 py-3 text-base gap-2.5',
    ];

    $classes = $baseClasses . ' ' . ($variantClasses[$variant] ?? $variantClasses['primary']) . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md']);

    if ($disabled) {
        $classes .= ' opacity-50 cursor-not-allowed';
    }
@endphp

<button
    {{ $attributes->merge(['type' => 'submit', 'class' => $classes]) }}
    @disabled($disabled)
>
    {{ $slot }}
</button>
