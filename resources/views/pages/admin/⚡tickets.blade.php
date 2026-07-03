<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

new #[Title('Manage Tickets'), Layout('layouts.app')] class extends Component {
    public string $search = '';
    public string $filterStatus = '';
    public string $filterMovie = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';

    public bool $showDetailsModal = false;
    public bool $showCheckInModal = false;
    public ?array $selectedTicket = null;

    // Scan/Validate fields
    public string $scanCode = '';
    public string $scanMessage = '';
    public string $scanStatus = ''; // success, error

    public array $tickets = [
        [
            'id'             => 1,
            'reference'      => 'BKL-29471',
            'customer_name'  => 'John Doe',
            'customer_email' => 'john@example.com',
            'movie'          => 'Neon Horizon',
            'format'         => 'IMAX',
            'showtime'       => '2026-07-15 19:15:00',
            'seats'          => 'Row D • Seat 14',
            'price'          => 16.00,
            'status'         => 'active',
            'created_at'     => '2026-07-01 10:30:00',
        ],
        [
            'id'             => 2,
            'reference'      => 'BKL-83742',
            'customer_name'  => 'Jane Smith',
            'customer_email' => 'jane@example.com',
            'movie'          => 'Moonlight Run',
            'format'         => 'Dolby Atmos',
            'showtime'       => '2026-07-02 14:00:00',
            'seats'          => 'Row B • Seat 5, Seat 6',
            'price'          => 27.50,
            'status'         => 'checked_in',
            'created_at'     => '2026-07-01 14:15:00',
        ],
        [
            'id'             => 3,
            'reference'      => 'BKL-10485',
            'customer_name'  => 'Alex Johnson',
            'customer_email' => 'alex@example.com',
            'movie'          => 'Golden Hour',
            'format'         => 'Standard',
            'showtime'       => '2026-07-05 18:30:00',
            'seats'          => 'Row C • Seat 10',
            'price'          => 12.50,
            'status'         => 'active',
            'created_at'     => '2026-07-02 09:00:00',
        ],
        [
            'id'             => 4,
            'reference'      => 'BKL-92843',
            'customer_name'  => 'Sarah Connor',
            'customer_email' => 'sarah@example.com',
            'movie'          => 'Iron Veil',
            'format'         => 'IMAX',
            'showtime'       => '2026-07-01 15:00:00',
            'seats'          => 'Row A • Seat 3, Seat 4',
            'price'          => 31.50,
            'status'         => 'cancelled',
            'created_at'     => '2026-06-30 18:22:00',
        ],
        [
            'id'             => 5,
            'reference'      => 'BKL-77291',
            'customer_name'  => 'Michael Scott',
            'customer_email' => 'michael@dundermifflin.com',
            'movie'          => 'Neon Horizon',
            'format'         => 'IMAX',
            'showtime'       => '2026-07-15 19:15:00',
            'seats'          => 'Row E • Seat 9',
            'price'          => 16.00,
            'status'         => 'active',
            'created_at'     => '2026-07-02 11:45:00',
        ],
        [
            'id'             => 6,
            'reference'      => 'BKL-55410',
            'customer_name'  => 'Pam Beesly',
            'customer_email' => 'pam@dundermifflin.com',
            'movie'          => 'Golden Hour',
            'format'         => 'Standard',
            'showtime'       => '2026-07-05 18:30:00',
            'seats'          => 'Row F • Seat 1, Seat 2',
            'price'          => 23.50,
            'status'         => 'checked_in',
            'created_at'     => '2026-07-02 12:10:00',
        ],
        [
            'id'             => 7,
            'reference'      => 'BKL-11111',
            'customer_name'  => 'Jim Halpert',
            'customer_email' => 'jim@dundermifflin.com',
            'movie'          => 'Moonlight Run',
            'format'         => 'Dolby Atmos',
            'showtime'       => '2026-07-02 10:00:00',
            'seats'          => 'Row C • Seat 4',
            'price'          => 14.00,
            'status'         => 'active',
            'created_at'     => '2026-07-02 08:30:00',
        ],
    ];

    /** Resolve the display status (active/checked_in/completed/expired/cancelled) for a raw ticket. */
    private function resolveDisplayStatus(array $ticket): string
    {
        if ($ticket['status'] === 'cancelled') return 'cancelled';
        $isPast = \Carbon\Carbon::parse($ticket['showtime'])->isPast();
        if ($isPast) return $ticket['status'] === 'checked_in' ? 'completed' : 'expired';
        return $ticket['status'];
    }

    #[Computed]
    public function filteredTickets(): array
    {
        return collect($this->tickets)
            ->map(fn($t) => array_merge($t, ['display_status' => $this->resolveDisplayStatus($t)]))
            ->when($this->search, fn($c) => $c->filter(
                fn($t) => str_contains(strtolower($t['reference']), strtolower($this->search))
                       || str_contains(strtolower($t['customer_name']), strtolower($this->search))
                       || str_contains(strtolower($t['movie']), strtolower($this->search))
            ))
            ->when($this->filterStatus, fn($c) => $c->where('display_status', $this->filterStatus))
            ->when($this->filterMovie, fn($c) => $c->where('movie', $this->filterMovie))
            ->sortBy($this->sortBy, SORT_REGULAR, $this->sortDir === 'desc')
            ->values()
            ->toArray();
    }

    #[Computed]
    public function stats(): array
    {
        $evaluated = collect($this->tickets)
            ->map(fn($t) => array_merge($t, ['display_status' => $this->resolveDisplayStatus($t)]));

        return [
            'total'      => $evaluated->count(),
            'active'     => $evaluated->where('display_status', 'active')->count(),
            'checked_in' => $evaluated->where('display_status', 'checked_in')->count(),
            'completed'  => $evaluated->where('display_status', 'completed')->count(),
            'expired'    => $evaluated->where('display_status', 'expired')->count(),
            'revenue'    => $evaluated->where('display_status', '!=', 'cancelled')->sum('price'),
        ];
    }

    #[Computed]
    public function uniqueMovies(): array
    {
        return collect($this->tickets)->pluck('movie')->unique()->values()->toArray();
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy  = $column;
            $this->sortDir = 'asc';
        }
    }

    public function viewDetails(int $id): void
    {
        $ticket = collect($this->tickets)->firstWhere('id', $id);
        if ($ticket) {
            $ticket['display_status'] = $this->resolveDisplayStatus($ticket);
        }
        $this->selectedTicket   = $ticket;
        $this->showDetailsModal = true;
    }

    public function toggleCheckIn(int $id): void
    {
        $ticket = collect($this->tickets)->firstWhere('id', $id);
        $isCheckingIn = $ticket && $ticket['status'] !== 'checked_in';

        $this->tickets = collect($this->tickets)->map(function ($t) use ($id) {
            if ($t['id'] === $id) {
                $t['status'] = $t['status'] === 'checked_in' ? 'active' : 'checked_in';
            }
            return $t;
        })->toArray();

        Flux::toast(
            $isCheckingIn ? 'Guest has been checked in successfully.' : 'Check-in has been reversed.',
            heading: $isCheckingIn ? 'Checked In' : 'Check-In Reversed',
            variant: $isCheckingIn ? 'success' : 'warning'
        );
    }

    public function cancelTicket(int $id): void
    {
        $this->tickets = collect($this->tickets)->map(function ($t) use ($id) {
            if ($t['id'] === $id) $t['status'] = 'cancelled';
            return $t;
        })->toArray();

        if ($this->selectedTicket && $this->selectedTicket['id'] === $id) {
            $this->selectedTicket['status']         = 'cancelled';
            $this->selectedTicket['display_status'] = 'cancelled';
        }

        Flux::toast('The ticket has been cancelled and can no longer be used for entry.', heading: 'Ticket Cancelled', variant: 'danger');
    }

    public function openCheckInScanner(): void
    {
        $this->scanCode    = '';
        $this->scanMessage = '';
        $this->scanStatus  = '';
        $this->showCheckInModal = true;
    }

    public function processScan(): void
    {
        $code = strtoupper(trim($this->scanCode));
        if (empty($code)) {
            $this->scanStatus  = 'error';
            $this->scanMessage = 'Please enter a booking reference code.';
            return;
        }

        $ticketKey = null;
        foreach ($this->tickets as $key => $t) {
            if ($t['reference'] === $code) { $ticketKey = $key; break; }
        }

        if ($ticketKey === null) {
            $this->scanStatus  = 'error';
            $this->scanMessage = 'Invalid ticket reference code. Ticket not found.';
            return;
        }

        $ticket = $this->tickets[$ticketKey];

        if ($ticket['status'] === 'cancelled') {
            $this->scanStatus  = 'error';
            $this->scanMessage = 'This ticket has been CANCELLED and cannot be used.';
            return;
        }

        if ($ticket['status'] === 'checked_in') {
            $this->scanStatus  = 'error';
            $this->scanMessage = 'Warning: This ticket was ALREADY checked in.';
            return;
        }

        $this->tickets[$ticketKey]['status'] = 'checked_in';
        $this->scanStatus  = 'success';
        $this->scanMessage = "Success! Checked in: {$ticket['customer_name']} ({$ticket['movie']}) - Seats: {$ticket['seats']}";
        $this->scanCode    = '';

        Flux::toast("{$ticket['customer_name']} — {$ticket['movie']}", heading: 'Guest Checked In ✓', variant: 'success');
    }
};
?>

<div class="flex flex-col gap-6 p-6">

    {{-- Page Header --}}
    <x-admin.page-header
        title="Tickets Management"
        description="View, search, cancel and validate booking tickets."
    >
        <x-slot:action>
            <flux:button icon="qr-code" variant="primary" wire:click="openCheckInScanner">
                Check-In Scanner
            </flux:button>
        </x-slot:action>
    </x-admin.page-header>

    {{-- Stats Cards --}}
    <x-admin.stats-grid :cols="6">
        <x-admin.stats-card title="Total Booked"      :value="$this->stats['total']" />
        <x-admin.stats-card title="Active (Upcoming)" :value="$this->stats['active']"     value-color="text-blue-500" />
        <x-admin.stats-card title="Checked-in Today"  :value="$this->stats['checked_in']" value-color="text-teal-500" />
        <x-admin.stats-card title="Completed (Past)"  :value="$this->stats['completed']"  value-color="text-emerald-500" />
        <x-admin.stats-card title="Expired / Missed"  :value="$this->stats['expired']"    value-color="text-amber-500" />
        <x-admin.stats-card title="Revenue"           :value="'£' . number_format($this->stats['revenue'], 2)" />
    </x-admin.stats-grid>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-48">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search Reference, Customer or Movie…"
                icon="magnifying-glass"
                clearable
            />
        </div>

        <flux:select wire:model.live="filterMovie" placeholder="All movies" class="w-48">
            <flux:select.option value="">All movies</flux:select.option>
            @foreach($this->uniqueMovies as $movieName)
                <flux:select.option value="{{ $movieName }}">{{ $movieName }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="filterStatus" placeholder="All statuses" class="w-44">
            <flux:select.option value="">All statuses</flux:select.option>
            <flux:select.option value="active">Active (Upcoming)</flux:select.option>
            <flux:select.option value="checked_in">Checked In (Today)</flux:select.option>
            <flux:select.option value="completed">Completed (Past)</flux:select.option>
            <flux:select.option value="expired">Expired / Missed</flux:select.option>
            <flux:select.option value="cancelled">Cancelled</flux:select.option>
        </flux:select>

        @if($search || $filterMovie || $filterStatus)
            <flux:button variant="ghost" size="sm" wire:click="$set('search', ''); $set('filterMovie', ''); $set('filterStatus', '')">
                Clear filters
            </flux:button>
        @endif
    </div>

    {{-- Tickets Table --}}
    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-800/60">
                <tr>
                    <x-admin.sortable-th column="reference"  :sort-by="$sortBy" :sort-dir="$sortDir">Ref Code</x-admin.sortable-th>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Customer</th>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Movie</th>
                    <x-admin.sortable-th column="showtime"   :sort-by="$sortBy" :sort-dir="$sortDir">Showtime</x-admin.sortable-th>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Seats</th>
                    <x-admin.sortable-th column="price"      :sort-by="$sortBy" :sort-dir="$sortDir">Amount</x-admin.sortable-th>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 bg-white dark:divide-zinc-700/50 dark:bg-zinc-900">
                @forelse($this->filteredTickets as $ticket)
                    <tr class="group transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/40">

                        <td class="px-4 py-3 font-mono font-semibold text-zinc-900 dark:text-white">
                            {{ $ticket['reference'] }}
                        </td>

                        <td class="px-4 py-3">
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-white">{{ $ticket['customer_name'] }}</p>
                                <p class="text-xs text-zinc-400">{{ $ticket['customer_email'] }}</p>
                            </div>
                        </td>

                        <td class="px-4 py-3">
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-white">{{ $ticket['movie'] }}</p>
                                <p class="text-xs text-zinc-400">{{ $ticket['format'] }}</p>
                            </div>
                        </td>

                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300 text-xs">
                            {{ \Carbon\Carbon::parse($ticket['showtime'])->format('d M Y, h:i A') }}
                        </td>

                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">
                            {{ $ticket['seats'] }}
                        </td>

                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">
                            £{{ number_format($ticket['price'], 2) }}
                        </td>

                        <td class="px-4 py-3">
                            <x-admin.status-badge :status="$ticket['display_status']" type="ticket" />
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                <flux:button size="sm" variant="ghost" icon="eye"
                                    wire:click="viewDetails({{ $ticket['id'] }})" title="View Details" />
                                @if($ticket['status'] !== 'cancelled')
                                    <flux:button size="sm" variant="ghost"
                                        icon="{{ $ticket['status'] === 'checked_in' ? 'arrow-uturn-left' : 'check' }}"
                                        wire:click="toggleCheckIn({{ $ticket['id'] }})"
                                        title="{{ $ticket['status'] === 'checked_in' ? 'Uncheck-In' : 'Check-In' }}"
                                        class="{{ $ticket['status'] === 'checked_in' ? 'text-zinc-400' : 'text-emerald-600' }}" />
                                    <flux:button size="sm" variant="ghost" icon="x-mark"
                                        wire:click="cancelTicket({{ $ticket['id'] }})"
                                        title="Cancel Ticket" class="text-red-500 hover:text-red-600" />
                                @endif
                            </div>
                        </td>

                    </tr>
                @empty
                    <x-admin.table-empty icon="ticket" message="No tickets found matching current filters." :colspan="8" />
                @endforelse
            </tbody>
        </table>

        <x-admin.table-footer :showing="count($this->filteredTickets)" :total="count($tickets)" noun="bookings" />
    </div>

    {{-- Details Modal --}}
    <flux:modal wire:model="showDetailsModal" class="w-full max-w-md">
        @if($selectedTicket)
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <flux:heading size="lg">Ticket Reference</flux:heading>
                    <p class="text-lg font-mono font-bold text-zinc-900 dark:text-white mt-1">{{ $selectedTicket['reference'] }}</p>
                </div>
                <x-admin.status-badge :status="$selectedTicket['display_status']" type="ticket" />
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-center p-6 bg-zinc-50 dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
                    {{-- Simulated QR Code --}}
                    <div class="relative p-3 bg-white rounded-lg shadow-sm border border-zinc-200">
                        <svg class="size-36 text-zinc-900" viewBox="0 0 100 100" fill="currentColor">
                            <path d="M0 0h30v30H0zM10 10h10v10H10zM70 0h30v30H70zM80 10h10v10H80zM0 70h30v30H0zM10 80h10v10H10z" />
                            <path d="M40 10h10v10H40zM55 5h10v20H55zM45 40h20v10H45zM35 55h15v20H35zM75 45h20v10H75zM70 70h15v20H70z" />
                            <path d="M90 75h10v10H90zM85 85h15v15H85zM50 80h10v10H50z" />
                        </svg>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 rounded-xl border border-zinc-100 bg-zinc-50/50 p-4 dark:border-zinc-800 dark:bg-zinc-900/50">
                    <div class="col-span-2">
                        <p class="text-xs text-zinc-400">Customer</p>
                        <p class="font-medium text-zinc-900 dark:text-white">{{ $selectedTicket['customer_name'] }}</p>
                        <p class="text-xs text-zinc-500">{{ $selectedTicket['customer_email'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-400">Movie</p>
                        <p class="font-medium text-zinc-900 dark:text-white">{{ $selectedTicket['movie'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-400">Format</p>
                        <p class="font-medium text-zinc-900 dark:text-white">{{ $selectedTicket['format'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-400">Showtime</p>
                        <p class="font-medium text-zinc-900 dark:text-white text-xs mt-0.5">
                            {{ \Carbon\Carbon::parse($selectedTicket['showtime'])->format('d M Y, h:i A') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-400">Seats</p>
                        <p class="font-medium text-zinc-900 dark:text-white">{{ $selectedTicket['seats'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-400">Amount Paid</p>
                        <p class="font-medium text-zinc-900 dark:text-white">£{{ number_format($selectedTicket['price'], 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-400">Purchase Date</p>
                        <p class="font-medium text-zinc-900 dark:text-white text-xs mt-0.5">
                            {{ \Carbon\Carbon::parse($selectedTicket['created_at'])->format('d M Y, h:i A') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2">
                @if($selectedTicket['status'] !== 'cancelled')
                    <flux:button variant="danger" wire:click="cancelTicket({{ $selectedTicket['id'] }})">
                        Cancel Ticket
                    </flux:button>
                @endif
                <flux:button variant="ghost" wire:click="$set('showDetailsModal', false)">Close</flux:button>
            </div>
        @endif
    </flux:modal>

    {{-- Scanner / Check-in Modal --}}
    <flux:modal wire:model="showCheckInModal" class="w-full max-w-md">
        <div class="mb-6">
            <flux:heading size="lg">Check-In Scanner</flux:heading>
            <flux:text class="mt-1">Enter a ticket reference code (e.g. BKL-29471) to instantly validate it.</flux:text>
        </div>

        <form wire:submit.prevent="processScan" class="space-y-4">
            <flux:field>
                <flux:label>Reference Code</flux:label>
                <div class="flex gap-2">
                    <flux:input wire:model="scanCode" placeholder="BKL-XXXXX" class="flex-1 font-mono uppercase" autofocus />
                    <flux:button type="submit" variant="primary">Validate</flux:button>
                </div>
            </flux:field>

            <x-admin.scan-result :status="$scanStatus" :message="$scanMessage" />
        </form>

        <div class="mt-6 flex justify-end">
            <flux:button variant="ghost" wire:click="$set('showCheckInModal', false)">Done</flux:button>
        </div>
    </flux:modal>

</div>
