<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

new #[Title('Manage Bookings'), Layout('layouts.app')] class extends Component {
    public string $search = '';
    public string $filterStatus = '';
    public string $filterMovie = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';

    public bool $showDetailsModal = false;
    public bool $showRefundModal = false;
    public ?array $selectedBooking = null;
    public ?int $refundingId = null;

    public array $bookings = [
        [
            'id'             => 1,
            'transaction_id' => 'TXN-839210',
            'ticket_ref'     => 'BKL-29471',
            'customer_name'  => 'John Doe',
            'customer_email' => 'john@example.com',
            'movie'          => 'Neon Horizon',
            'format'         => 'IMAX',
            'seats_count'    => 1,
            'seats_list'     => 'Row D • Seat 14',
            'subtotal'       => 14.50,
            'fee'            => 1.50,
            'total'          => 16.00,
            'status'         => 'paid',
            'payment_method' => 'Visa ending in 4242',
            'created_at'     => '2026-07-01 10:30:00',
        ],
        [
            'id'             => 2,
            'transaction_id' => 'TXN-748301',
            'ticket_ref'     => 'BKL-83742',
            'customer_name'  => 'Jane Smith',
            'customer_email' => 'jane@example.com',
            'movie'          => 'Moonlight Run',
            'format'         => 'Dolby Atmos',
            'seats_count'    => 2,
            'seats_list'     => 'Row B • Seat 5, Seat 6',
            'subtotal'       => 25.00,
            'fee'            => 2.50,
            'total'          => 27.50,
            'status'         => 'paid',
            'payment_method' => 'MasterCard ending in 9876',
            'created_at'     => '2026-07-01 14:15:00',
        ],
        [
            'id'             => 3,
            'transaction_id' => 'TXN-102948',
            'ticket_ref'     => 'BKL-10485',
            'customer_name'  => 'Alex Johnson',
            'customer_email' => 'alex@example.com',
            'movie'          => 'Golden Hour',
            'format'         => 'Standard',
            'seats_count'    => 1,
            'seats_list'     => 'Row C • Seat 10',
            'subtotal'       => 11.00,
            'fee'            => 1.50,
            'total'          => 12.50,
            'status'         => 'paid',
            'payment_method' => 'Apple Pay',
            'created_at'     => '2026-07-02 09:00:00',
        ],
        [
            'id'             => 4,
            'transaction_id' => 'TXN-902847',
            'ticket_ref'     => 'BKL-92843',
            'customer_name'  => 'Sarah Connor',
            'customer_email' => 'sarah@example.com',
            'movie'          => 'Iron Veil',
            'format'         => 'IMAX',
            'seats_count'    => 2,
            'seats_list'     => 'Row A • Seat 3, Seat 4',
            'subtotal'       => 29.00,
            'fee'            => 2.50,
            'total'          => 31.50,
            'status'         => 'refunded',
            'payment_method' => 'Visa ending in 1111',
            'created_at'     => '2026-06-30 18:22:00',
        ],
        [
            'id'             => 5,
            'transaction_id' => 'TXN-472910',
            'ticket_ref'     => 'BKL-77291',
            'customer_name'  => 'Michael Scott',
            'customer_email' => 'michael@dundermifflin.com',
            'movie'          => 'Neon Horizon',
            'format'         => 'IMAX',
            'seats_count'    => 1,
            'seats_list'     => 'Row E • Seat 9',
            'subtotal'       => 14.50,
            'fee'            => 1.50,
            'total'          => 16.00,
            'status'         => 'paid',
            'payment_method' => 'Amex ending in 2004',
            'created_at'     => '2026-07-02 11:45:00',
        ],
        [
            'id'             => 6,
            'transaction_id' => 'TXN-554102',
            'ticket_ref'     => 'BKL-55410',
            'customer_name'  => 'Pam Beesly',
            'customer_email' => 'pam@dundermifflin.com',
            'movie'          => 'Golden Hour',
            'format'         => 'Standard',
            'seats_count'    => 2,
            'seats_list'     => 'Row F • Seat 1, Seat 2',
            'subtotal'       => 22.00,
            'fee'            => 1.50,
            'total'          => 23.50,
            'status'         => 'paid',
            'payment_method' => 'Visa ending in 5555',
            'created_at'     => '2026-07-02 12:10:00',
        ],
        [
            'id'             => 7,
            'transaction_id' => 'TXN-111110',
            'ticket_ref'     => 'BKL-11111',
            'customer_name'  => 'Jim Halpert',
            'customer_email' => 'jim@dundermifflin.com',
            'movie'          => 'Moonlight Run',
            'format'         => 'Dolby Atmos',
            'seats_count'    => 1,
            'seats_list'     => 'Row C • Seat 4',
            'subtotal'       => 12.50,
            'fee'            => 1.50,
            'total'          => 14.00,
            'status'         => 'failed',
            'payment_method' => 'MasterCard ending in 4321',
            'created_at'     => '2026-07-02 08:30:00',
        ],
    ];

    #[Computed]
    public function filteredBookings(): array
    {
        return collect($this->bookings)
            ->when($this->search, fn($c) => $c->filter(
                fn($b) => str_contains(strtolower($b['transaction_id']), strtolower($this->search))
                       || str_contains(strtolower($b['customer_name']), strtolower($this->search))
                       || str_contains(strtolower($b['ticket_ref']), strtolower($this->search))
            ))
            ->when($this->filterStatus, fn($c) => $c->where('status', $this->filterStatus))
            ->when($this->filterMovie,  fn($c) => $c->where('movie', $this->filterMovie))
            ->sortBy($this->sortBy, SORT_REGULAR, $this->sortDir === 'desc')
            ->values()
            ->toArray();
    }

    #[Computed]
    public function stats(): array
    {
        $all = collect($this->bookings);
        return [
            'total'    => $all->count(),
            'paid'     => $all->where('status', 'paid')->count(),
            'refunded' => $all->where('status', 'refunded')->count(),
            'failed'   => $all->where('status', 'failed')->count(),
            'revenue'  => $all->where('status', 'paid')->sum('total'),
        ];
    }

    #[Computed]
    public function uniqueMovies(): array
    {
        return collect($this->bookings)->pluck('movie')->unique()->values()->toArray();
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
        $this->selectedBooking = collect($this->bookings)->firstWhere('id', $id);
        $this->showDetailsModal = true;
    }

    public function confirmRefund(int $id): void
    {
        $this->refundingId    = $id;
        $this->showRefundModal = true;
    }

    public function processRefund(): void
    {
        $this->bookings = collect($this->bookings)->map(function ($b) {
            if ($b['id'] === $this->refundingId) $b['status'] = 'refunded';
            return $b;
        })->toArray();

        if ($this->selectedBooking && $this->selectedBooking['id'] === $this->refundingId) {
            $this->selectedBooking['status'] = 'refunded';
        }

        $this->showRefundModal = false;
        $this->refundingId     = null;

        Flux::toast('The payment has been refunded to the customer\'s original payment method.', heading: 'Refund Processed', variant: 'warning');
    }
};
?>

<div class="flex flex-col gap-6 p-6">

    {{-- Page Header --}}
    <x-admin.page-header
        title="Bookings & Transactions"
        description="View payment statuses, seat allocations, transactions and issue refunds."
    />

    {{-- Stats Cards --}}
    <x-admin.stats-grid :cols="5">
        <x-admin.stats-card title="Total Bookings"      :value="$this->stats['total']" />
        <x-admin.stats-card title="Completed Payments"  :value="$this->stats['paid']"     value-color="text-emerald-500" />
        <x-admin.stats-card title="Refunded Orders"     :value="$this->stats['refunded']" value-color="text-blue-500" />
        <x-admin.stats-card title="Failed Attempts"     :value="$this->stats['failed']"   value-color="text-red-500" />
        <x-admin.stats-card title="Net Revenue"         :value="'£' . number_format($this->stats['revenue'], 2)" />
    </x-admin.stats-grid>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-48">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search Transaction ID, Ref or Name…"
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

        <flux:select wire:model.live="filterStatus" placeholder="All payment statuses" class="w-48">
            <flux:select.option value="">All payment statuses</flux:select.option>
            <flux:select.option value="paid">Paid</flux:select.option>
            <flux:select.option value="refunded">Refunded</flux:select.option>
            <flux:select.option value="failed">Failed</flux:select.option>
        </flux:select>

        @if($search || $filterMovie || $filterStatus)
            <flux:button variant="ghost" size="sm"
                wire:click="$set('search', ''); $set('filterMovie', ''); $set('filterStatus', '')">
                Clear filters
            </flux:button>
        @endif
    </div>

    {{-- Bookings Table --}}
    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-800/60">
                <tr>
                    <x-admin.sortable-th column="transaction_id" :sort-by="$sortBy" :sort-dir="$sortDir">Transaction ID</x-admin.sortable-th>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Ref Code</th>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Customer</th>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Movie</th>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Seats</th>
                    <x-admin.sortable-th column="total"      :sort-by="$sortBy" :sort-dir="$sortDir">Total Paid</x-admin.sortable-th>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Payment Status</th>
                    <x-admin.sortable-th column="created_at" :sort-by="$sortBy" :sort-dir="$sortDir">Date</x-admin.sortable-th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 bg-white dark:divide-zinc-700/50 dark:bg-zinc-900">
                @forelse($this->filteredBookings as $booking)
                    <tr class="group transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/40">

                        <td class="px-4 py-3 font-mono text-zinc-900 dark:text-white">
                            {{ $booking['transaction_id'] }}
                        </td>

                        <td class="px-4 py-3 font-mono font-semibold text-zinc-600 dark:text-zinc-300">
                            {{ $booking['ticket_ref'] }}
                        </td>

                        <td class="px-4 py-3">
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-white">{{ $booking['customer_name'] }}</p>
                                <p class="text-xs text-zinc-400">{{ $booking['customer_email'] }}</p>
                            </div>
                        </td>

                        <td class="px-4 py-3 text-zinc-700 dark:text-zinc-200">{{ $booking['movie'] }}</td>

                        <td class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-300">
                            {{ $booking['seats_count'] }}
                        </td>

                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">
                            £{{ number_format($booking['total'], 2) }}
                        </td>

                        <td class="px-4 py-3">
                            <x-admin.status-badge :status="$booking['status']" type="booking" />
                        </td>

                        <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 text-xs">
                            {{ \Carbon\Carbon::parse($booking['created_at'])->format('d M Y, h:i A') }}
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                <flux:button size="sm" variant="ghost" icon="receipt-percent"
                                    wire:click="viewDetails({{ $booking['id'] }})" title="View Receipt" />
                                @if($booking['status'] === 'paid')
                                    <flux:button size="sm" variant="ghost" icon="arrow-path-rounded-square"
                                        wire:click="confirmRefund({{ $booking['id'] }})"
                                        title="Refund Order" class="text-red-500 hover:text-red-600" />
                                @endif
                            </div>
                        </td>

                    </tr>
                @empty
                    <x-admin.table-empty icon="banknotes" message="No bookings found matching current filters." :colspan="9" />
                @endforelse
            </tbody>
        </table>

        <x-admin.table-footer :showing="count($this->filteredBookings)" :total="count($bookings)" noun="records" />
    </div>

    {{-- Details / Receipt Modal --}}
    <flux:modal wire:model="showDetailsModal" class="w-full max-w-md">
        @if($selectedBooking)
            <div class="mb-6 flex items-center justify-between border-b border-zinc-100 pb-4 dark:border-zinc-800">
                <div>
                    <flux:heading size="lg">Receipt Invoice</flux:heading>
                    <p class="text-xs text-zinc-500 mt-0.5">ID: {{ $selectedBooking['transaction_id'] }}</p>
                </div>
                <x-admin.status-badge :status="$selectedBooking['status']" type="booking" />
            </div>

            <div class="space-y-6">
                {{-- Payment Details --}}
                <div class="space-y-2">
                    <p class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Customer & Payment</p>
                    <div class="rounded-xl border border-zinc-100 bg-zinc-50/50 p-4 dark:border-zinc-800 dark:bg-zinc-900/50 space-y-2">
                        <div>
                            <p class="text-xs text-zinc-400">Billing to</p>
                            <p class="font-medium text-zinc-900 dark:text-white">{{ $selectedBooking['customer_name'] }}</p>
                            <p class="text-xs text-zinc-500">{{ $selectedBooking['customer_email'] }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                            <div>
                                <p class="text-xs text-zinc-400">Payment Method</p>
                                <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ $selectedBooking['payment_method'] }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-zinc-400">Date Paid</p>
                                <p class="text-sm font-medium text-zinc-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($selectedBooking['created_at'])->format('d M Y, h:i A') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Order details --}}
                <div class="space-y-2">
                    <p class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Order Items</p>
                    <div class="rounded-xl border border-zinc-100 bg-zinc-50/50 p-4 dark:border-zinc-800 dark:bg-zinc-900/50 space-y-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-white">{{ $selectedBooking['movie'] }} ({{ $selectedBooking['format'] }})</p>
                                <p class="text-xs text-zinc-500">Seats: {{ $selectedBooking['seats_list'] }}</p>
                            </div>
                            <p class="font-medium text-zinc-900 dark:text-white">£{{ number_format($selectedBooking['subtotal'], 2) }}</p>
                        </div>
                        <div class="flex justify-between text-sm text-zinc-500 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                            <p>Booking Fee</p>
                            <p>£{{ number_format($selectedBooking['fee'], 2) }}</p>
                        </div>
                        <div class="flex justify-between font-bold text-zinc-900 dark:text-white pt-2 border-t border-zinc-100 dark:border-zinc-800">
                            <p>Total</p>
                            <p>£{{ number_format($selectedBooking['total'], 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2">
                @if($selectedBooking['status'] === 'paid')
                    <flux:button variant="danger" wire:click="confirmRefund({{ $selectedBooking['id'] }})">
                        Refund Booking
                    </flux:button>
                @endif
                <flux:button variant="ghost" wire:click="$set('showDetailsModal', false)">Close</flux:button>
            </div>
        @endif
    </flux:modal>

    {{-- Refund Confirmation Modal --}}
    <x-admin.confirm-modal
        wire="showRefundModal"
        title="Refund Transaction"
        message="This will refund the amount paid back to the customer's payment method and mark the booking as Refunded. This action cannot be undone."
        confirm-label="Refund"
        confirm-action="processRefund"
    />

</div>
