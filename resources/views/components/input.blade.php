@props([
    'name' => '',
    'label' => '',
    'type' => 'text',
    'value' => '',
    'help' => '',
    'message' => '',
    'disabled' => false,
])

<div class="space-y-1">
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-neutral-700">
            {{ $label }}
        </label>
    @endif

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ $value }}"
        @disabled($disabled)
        {{ $attributes->merge(['class' => 'block w-full rounded-lg border px-3 py-2 text-sm shadow-sm transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-0 ' . ($message ? 'border-danger-500 text-danger-900 focus:border-danger-500 focus:ring-danger-500' : 'border-neutral-300 text-neutral-900 focus:border-primary-500 focus:ring-primary-500')]) }}
    >

    @if ($message)
        <p class="text-sm text-danger-600">{{ $message }}</p>
    @endif

    @if ($help && !$message)
        <p class="text-sm text-neutral-500">{{ $help }}</p>
    @endif
</div>
