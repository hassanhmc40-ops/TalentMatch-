@props([
    'header' => null,
    'footer' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow-card border border-neutral-200']) }}>
    @if ($header)
        <div class="px-6 py-4 border-b border-neutral-200 font-medium text-neutral-900">
            {{ $header }}
        </div>
    @endif

    <div class="px-6 py-4">
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="px-6 py-4 border-t border-neutral-200 bg-neutral-50 rounded-b-lg">
            {{ $footer }}
        </div>
    @endif
</div>
