@props([
    'status',
    'type' => 'movie', // movie | ticket | booking | user
])

@php
$configs = [
    'movie' => [
        'active'      => ['color' => 'emerald', 'label' => 'Now Showing'],
        'coming_soon' => ['color' => 'amber',   'label' => 'Coming Soon'],
        'archived'    => ['color' => 'zinc',    'label' => 'Archived'],
    ],
    'ticket' => [
        'active'     => ['color' => 'blue',    'label' => 'Active'],
        'checked_in' => ['color' => 'teal',    'label' => 'Checked In'],
        'completed'  => ['color' => 'emerald', 'label' => 'Completed'],
        'expired'    => ['color' => 'amber',   'label' => 'Expired'],
        'cancelled'  => ['color' => 'red',     'label' => 'Cancelled'],
    ],
    'booking' => [
        'paid'     => ['color' => 'emerald', 'label' => 'Paid'],
        'pending'  => ['color' => 'amber',   'label' => 'Pending'],
        'refunded' => ['color' => 'blue',    'label' => 'Refunded'],
        'failed'   => ['color' => 'red',     'label' => 'Failed'],
    ],
    'user' => [
        'active'    => ['color' => 'emerald', 'label' => 'Active'],
        'suspended' => ['color' => 'red',     'label' => 'Suspended'],
        'inactive'  => ['color' => 'zinc',    'label' => 'Inactive'],
    ],
];

$map = $configs[$type] ?? [];
$cfg = $map[$status] ?? ['color' => 'zinc', 'label' => ucfirst(str_replace('_', ' ', $status))];
$color = $cfg['color'];
$label = $cfg['label'];

$colorClasses = match($color) {
    'emerald' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 [&>span]:bg-emerald-500',
    'amber'   => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 [&>span]:bg-amber-500',
    'blue'    => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 [&>span]:bg-blue-500',
    'teal'    => 'bg-teal-50 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400 [&>span]:bg-teal-500',
    'red'     => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400 [&>span]:bg-red-500',
    default   => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400 [&>span]:bg-zinc-400',
};
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ' . $colorClasses]) }}>
    <span class="size-1.5 rounded-full"></span>
    {{ $label }}
</span>
