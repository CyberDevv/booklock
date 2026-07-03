@props([
    'cols' => 4,
])

<div {{ $attributes->merge(['class' => 'grid grid-cols-2 gap-4 sm:grid-cols-' . $cols]) }}>
    {{ $slot }}
</div>
