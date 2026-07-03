@props([
    'icon' => 'inbox',
    'message' => 'No results found.',
    'colspan' => 8,
])

<tr>
    <td colspan="{{ $colspan }}" class="px-4 py-16 text-center">
        <flux:icon name="{{ $icon }}" class="mx-auto mb-3 size-10 text-zinc-300 dark:text-zinc-600" />
        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $message }}</p>
        @isset($actions)
            <div class="mt-3">{{ $actions }}</div>
        @endisset
    </td>
</tr>
