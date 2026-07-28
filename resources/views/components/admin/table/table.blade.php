@props([
    'tableHeaders',
    'data',
    'sortBy' => null,
    'sortDir' => null,
    'emptyMessage' => 'No records found.',
    'emptyIcon' => 'inbox',
    'hasActions' => true,
    'showFooter' => false,
    'total' => null,
    'footerNoun' => 'items',
])

<div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
    <table class="w-full text-sm">
        <thead class="bg-zinc-50 dark:bg-zinc-800/60">
            <tr>
                @foreach ($tableHeaders as $header)
                    @if ($header['sortable'])
                        <x-admin.sortable-th column="{{ $header['column'] }}" :sort-by="$sortBy" :sort-dir="$sortDir">
                            {{ $header['label'] }}
                        </x-admin.sortable-th>
                    @else
                        <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">
                            {{ $header['label'] }}
                        </th>
                    @endif
                @endforeach
                @if($hasActions)
                    <th class="px-4 py-3"></th>
                @endif
            </tr>
        </thead>

        <tbody class="divide-y divide-zinc-100 bg-white dark:divide-zinc-700/50 dark:bg-zinc-900">
            @if($data->isNotEmpty())
                {{ $slot }}
            @else
                <x-admin.table-empty
                    :icon="$emptyIcon"
                    :message="$emptyMessage"
                    :colspan="count($tableHeaders) + ($hasActions ? 1 : 0)">
                    @isset($emptyActions)
                        <x-slot:actions>
                            {{ $emptyActions }}
                        </x-slot:actions>
                    @endisset
                </x-admin.table-empty>
            @endif
        </tbody>
    </table>

    @if($showFooter)
        <x-admin.table-footer
            :showing="count($data)"
            :total="$total ?? count($data)"
            :noun="$footerNoun" />
    @endif
</div>
