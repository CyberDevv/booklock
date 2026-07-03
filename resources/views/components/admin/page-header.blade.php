@props([
    'title',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'flex items-center justify-between']) }}>
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $title }}</h1>
        @if($description)
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $description }}</p>
        @endif
    </div>

    @isset($action)
        <div>{{ $action }}</div>
    @endisset
</div>
