@props([
    'value' => 0,
    'variant' => 'primary',
    'label' => '',
])

@php
$variantClasses = [
    'primary' => 'bg-primary-600',
    'success' => 'bg-success-500',
    'warning'  => 'bg-warning-500',
    'danger'  => 'bg-danger-500',
];

$barClasses = $variantClasses[$variant] ?? $variantClasses['primary'];

$displayValue = min(100, max(0, (int) $value));
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    @if ($label)
        <div class="flex items-center justify-between mb-1">
            <span class="text-sm font-medium text-neutral-700">{{ $label }}</span>
            <span class="text-sm font-medium text-neutral-500">{{ $displayValue }}%</span>
        </div>
    @endif
    <div class="w-full h-2.5 bg-neutral-200 rounded-full overflow-hidden">
        <div
            class="h-full rounded-full transition-all duration-500 ease-in-out {{ $barClasses }}"
            style="width: {{ $displayValue }}%"
        ></div>
    </div>
</div>
