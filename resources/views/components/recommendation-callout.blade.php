@props([
    'recommendation' => null,
    'justification' => '',
])

@php
$variant = match ($recommendation?->value) {
    'convoquer' => 'success',
    'attente' => 'warning',
    'rejeter' => 'danger',
    default => 'neutral',
};

$label = $recommendation?->label() ?? 'Recommandation';

$borderColor = match ($variant) {
    'success' => 'border-l-success-500',
    'warning' => 'border-l-warning-500',
    'danger' => 'border-l-danger-500',
    default => 'border-l-neutral-500',
};

$bgColor = match ($variant) {
    'success' => 'bg-success-50',
    'warning' => 'bg-warning-50',
    'danger' => 'bg-danger-50',
    default => 'bg-neutral-50',
};

$iconColor = match ($variant) {
    'success' => 'text-success-600',
    'warning' => 'text-warning-600',
    'danger' => 'text-danger-600',
    default => 'text-neutral-600',
};

$icon = match ($recommendation?->value) {
    'convoquer' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    'attente' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    'rejeter' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    default => '',
};
@endphp

<div class="border-l-4 {{ $borderColor }} {{ $bgColor }} rounded-r-lg p-6">
    <div class="flex items-start gap-4">
        <div class="flex-shrink-0 {{ $iconColor }}">
            {!! $icon !!}
        </div>
        <div class="flex-1 min-w-0">
            <h3 class="text-lg font-semibold text-neutral-900">{{ $label }}</h3>
            @if ($justification)
                <p class="mt-2 text-neutral-700 leading-relaxed">{{ $justification }}</p>
            @endif
        </div>
    </div>
</div>
