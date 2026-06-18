@props([
    'headers' => [],
    'rows' => [],
])

@php
$hasActions = ! empty(trim($actions ?? ''));
@endphp

<div class="overflow-x-auto rounded-lg border border-neutral-200">
    <table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-neutral-200']) }}>
        @if (count($headers) > 0)
            <thead class="bg-neutral-50">
                <tr>
                    @foreach ($headers as $header)
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider">
                            {{ $header['label'] ?? $header }}
                        </th>
                    @endforeach
                    @if ($hasActions)
                        <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-neutral-500 uppercase tracking-wider">
                            Actions
                        </th>
                    @endif
                </tr>
            </thead>
        @endif

        <tbody class="bg-white divide-y divide-neutral-200">
            @forelse ($rows as $row)
                <tr class="hover:bg-neutral-50 transition-colors duration-150">
                    @foreach ($headers as $header)
                        @php
                            $key = $header['key'] ?? $header;
                        @endphp
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700">
                            {{ data_get($row, $key) }}
                        </td>
                    @endforeach
                    @if ($hasActions)
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                            {{ $actions }}
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) + ($hasActions ? 1 : 0) }}" class="px-6 py-12 text-center text-sm text-neutral-500">
                        Aucun résultat
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
