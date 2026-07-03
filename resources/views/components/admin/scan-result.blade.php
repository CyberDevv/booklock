@props([
    'status',   // 'success' | 'error' | ''
    'message',
])

@if($message)
    <div class="rounded-lg p-3 text-sm border flex gap-2 items-start
        {{ $status === 'success'
            ? 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-900/50'
            : 'bg-red-50 text-red-800 border-red-200 dark:bg-red-950/20 dark:text-red-400 dark:border-red-900/50' }}">
        <flux:icon name="{{ $status === 'success' ? 'check-circle' : 'exclamation-circle' }}" class="size-5 shrink-0 mt-0.5" />
        <p>{{ $message }}</p>
    </div>
@endif
