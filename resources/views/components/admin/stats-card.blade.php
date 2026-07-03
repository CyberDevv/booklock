@props([
    'title',
    'value',
    'valueColor' => 'text-zinc-900 dark:text-white',
    'trend'      => null,
    'trendColor' => 'text-emerald-600 dark:text-emerald-400',
    'subtext'    => null,
])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900']) }}>
    <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ $title }}</p>
    <div class="flex items-baseline gap-2 mt-1">
        <span class="text-2xl font-bold {{ $valueColor }}">{{ $value }}</span>
        @if($trend)
            <span class="text-xs font-semibold {{ $trendColor }}">{{ $trend }}</span>
        @endif
        @if($subtext)
            <span class="text-xs text-zinc-400">{{ $subtext }}</span>
        @endif
    </div>
</div>
