@props([
    'showing',
    'total',
    'noun' => 'results',
])

<div {{ $attributes->merge(['class' => 'flex items-center justify-between border-t border-zinc-100 bg-zinc-50 px-4 py-2.5 dark:border-zinc-700 dark:bg-zinc-800/40']) }}>
    <p class="text-xs text-zinc-500 dark:text-zinc-400">
        Showing {{ $showing }} of {{ $total }} {{ $noun }}
    </p>
    @isset($actions)
        <div>{{ $actions }}</div>
    @endisset
</div>
