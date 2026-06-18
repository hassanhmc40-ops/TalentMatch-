@props([
    'type' => 'info',
    'dismissible' => false,
])

@php
$typeClasses = [
    'success' => 'bg-success-50 text-success-800 border-success-200',
    'warning'  => 'bg-warning-50 text-warning-800 border-warning-200',
    'danger'  => 'bg-danger-50 text-danger-800 border-danger-200',
    'info'    => 'bg-primary-50 text-primary-800 border-primary-200',
];

$typeIcons = [
    'success' => '&#10003;',
    'warning'  => '&#9888;',
    'danger'  => '&#10007;',
    'info'    => '&#8505;',
];

$classes = $typeClasses[$type] ?? $typeClasses['info'];
$icon = $typeIcons[$type] ?? $typeIcons['info'];
@endphp

<div
    x-data="{ visible: true }"
    x-show="visible"
    role="alert"
    {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-lg border p-4 text-sm ' . $classes]) }}
>
    <span class="flex-shrink-0 text-lg leading-none">{!! $icon !!}</span>
    <div class="flex-1">
        {{ $slot }}
    </div>
    @if ($dismissible)
        <button
            type="button"
            class="flex-shrink-0 ml-auto -mx-1 -my-1 rounded-lg p-1.5 inline-flex hover:opacity-75 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-current"
            x-on:click="visible = false"
            aria-label="Fermer"
        >
            <span class="text-lg leading-none">&times;</span>
        </button>
    @endif
</div>
