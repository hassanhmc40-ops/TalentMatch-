@props([
    'recommendation' => null,
])

@php
$variant = match ($recommendation?->value) {
    'convoquer' => 'success',
    'attente' => 'warning',
    'rejeter' => 'danger',
    default => 'neutral',
};

$label = $recommendation?->label() ?? 'Non définie';

$icon = match ($recommendation?->value) {
    'convoquer' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    'attente' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    'rejeter' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    default => '',
};
@endphp

<x-badge :variant="$variant" {{ $attributes }}>
    @if ($icon)
        {!! $icon !!}
        <span class="ml-1.5">{{ $label }}</span>
    @else
        {{ $label }}
    @endif
</x-badge>
