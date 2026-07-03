@props([
    'wire'          => null,
    'title'         => 'Confirm Action',
    'message'       => 'This action cannot be undone.',
    'confirmLabel'  => 'Confirm',
    'confirmAction' => null,
    'cancelAction'  => null,
])

@php
    $resolvedCancel  = $cancelAction  ?? "\$set('{$wire}', false)";
    $resolvedConfirm = $confirmAction;
@endphp

<flux:modal wire:model="{{ $wire ?? '' }}" class="max-w-sm">
    <flux:heading size="lg" class="mb-2">{{ $title }}</flux:heading>
    <flux:text class="mb-6">{{ $message }}</flux:text>
    <div class="flex justify-end gap-2">
        <flux:button variant="ghost" wire:click="{{ $resolvedCancel }}">
            Cancel
        </flux:button>
        @if($resolvedConfirm)
            <flux:button variant="danger" wire:click="{{ $resolvedConfirm }}">
                {{ $confirmLabel }}
            </flux:button>
        @else
            <flux:button variant="danger">
                {{ $confirmLabel }}
            </flux:button>
        @endif
    </div>
</flux:modal>
