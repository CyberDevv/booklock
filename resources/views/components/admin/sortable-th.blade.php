@props([
    'column',
    'sortBy',
    'sortDir',
    'label' => null
])

<th {{ $attributes->merge(['class' => 'px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400']) }}>
    <button wire:click="sort('{{ $column }}')" class="flex items-center gap-1 hover:text-zinc-900 dark:hover:text-white transition-colors">
        {{ $label ?? $slot }}
        @if($sortBy === $column)
            <flux:icon name="{{ $sortDir === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="size-3" />
        @endif
    </button>
</th>
