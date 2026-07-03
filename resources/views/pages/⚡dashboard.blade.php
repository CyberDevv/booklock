<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

new #[Title('Dashboard'), Layout('layouts.app')] class extends Component {
    // Quick scanner code
    public string $scanCode = '';
    public string $scanMessage = '';
    public string $scanStatus = '';

    public array $recentBookings = [
        [
            'time' => '2 mins ago',
            'customer' => 'John Doe',
            'movie' => 'Neon Horizon',
            'seats' => 1,
            'amount' => 16.00,
            'type' => 'booking'
        ],
        [
            'time' => '15 mins ago',
            'customer' => 'Jane Smith',
            'movie' => 'Moonlight Run',
            'seats' => 2,
            'amount' => 27.50,
            'type' => 'booking'
        ],
        [
            'time' => '1 hour ago',
            'customer' => 'Sarah Connor',
            'movie' => 'Iron Veil',
            'seats' => 2,
            'amount' => 31.50,
            'type' => 'refund'
        ],
        [
            'time' => '3 hours ago',
            'customer' => 'Michael Scott',
            'movie' => 'Neon Horizon',
            'seats' => 1,
            'amount' => 16.00,
            'type' => 'booking'
        ]
    ];

    public array $todayScreenings = [
        [
            'movie' => 'Neon Horizon',
            'time' => '5:15 PM',
            'screen' => 'Grand Screen (VIP)',
            'occupancy' => 82,
            'tickets' => '41/50',
            'status' => 'completed'
        ],
        [
            'movie' => 'Moonlight Run',
            'time' => '8:00 PM',
            'screen' => 'Screen 2 (Dolby)',
            'occupancy' => 68,
            'tickets' => '34/50',
            'status' => 'running'
        ],
        [
            'movie' => 'Golden Hour',
            'time' => '9:15 PM',
            'screen' => 'Screen 3 (Standard)',
            'occupancy' => 89,
            'tickets' => '89/100',
            'status' => 'upcoming'
        ]
    ];

    public function processQuickScan(): void
    {
        $code = strtoupper(trim($this->scanCode));
        if (empty($code)) {
            $this->scanStatus = 'error';
            $this->scanMessage = 'Please enter reference code.';
            return;
        }

        // Mock check-ins
        if ($code === 'BKL-29471') {
            $this->scanStatus = 'success';
            $this->scanMessage = 'Checked in: John Doe (Neon Horizon) - Seat D14';
            $this->scanCode = '';
        } elseif ($code === 'BKL-11111') {
            $this->scanStatus = 'error';
            $this->scanMessage = 'This ticket has expired (show ended).';
        } else {
            $this->scanStatus = 'error';
            $this->scanMessage = 'Invalid ticket reference code.';
        }
    }
}
?>

<div class="flex flex-col gap-6 p-6">

    {{-- Welcome header --}}
    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-zinc-900 dark:text-white">Welcome Back, Ibrahim</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Here is what is happening at your cinema today.</p>
        </div>
        <div class="flex items-center gap-2 mt-4 sm:mt-0">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400">
                <span class="size-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Cinema Online
            </span>
        </div>
    </div>

    {{-- Stats Counters Grid --}}
    <x-admin.stats-grid :cols="4">
        <x-admin.stats-card title="Daily Gross Revenue" value="£1,420.50" trend="+12.4%" />
        <x-admin.stats-card title="Tickets Issued Today" value="98"       trend="+8.2%" />
        <x-admin.stats-card title="Active Screenings"   value="3"         value-color="text-teal-500" subtext="running now" />
        <x-admin.stats-card title="Staff Administrators" value="1"         subtext="online session" />
    </x-admin.stats-grid>

    {{-- Interactive Shortcuts & Quick Scan --}}
    <div class="grid gap-6 md:grid-cols-3">

        {{-- Quick Check-in validator shortcut --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 md:col-span-2">
            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-2">Gate Check-in Desk</h3>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-4">Validate tickets as guests arrive at the terminal gate.</p>
            
            <form wire:submit.prevent="processQuickScan" class="space-y-4">
                <div class="flex gap-2">
                    <flux:input wire:model="scanCode" placeholder="Enter Reference (e.g. BKL-29471)" class="flex-1 font-mono uppercase" />
                    <flux:button type="submit" variant="primary">Check In</flux:button>
                </div>

                <x-admin.scan-result :status="$scanStatus" :message="$scanMessage" />
            </form>
        </div>

        {{-- Quick Panel Shortcuts --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-2">Quick Navigation</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-4">Jump directly to management panels.</p>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('movies.index') }}" wire:navigate class="flex flex-col items-center gap-2 rounded-lg border border-zinc-100 p-3 text-center hover:bg-zinc-50 transition dark:border-zinc-800 dark:hover:bg-zinc-800/50">
                    <flux:icon name="plus" class="size-5 text-blue-500" />
                    <span class="text-xs font-medium">Add Movie</span>
                </a>
                <a href="{{ route('admin.tickets') }}" wire:navigate class="flex flex-col items-center gap-2 rounded-lg border border-zinc-100 p-3 text-center hover:bg-zinc-50 transition dark:border-zinc-800 dark:hover:bg-zinc-800/50">
                    <flux:icon name="qr-code" class="size-5 text-teal-500" />
                    <span class="text-xs font-medium">Scan Desk</span>
                </a>
            </div>
        </div>

    </div>

    {{-- Today's listings and recent activity grid --}}
    <div class="grid gap-6 md:grid-cols-2">

        {{-- Today's Screenings status --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Today's Showtimes</h3>
                <span class="text-xs text-zinc-400">Live Occupancy</span>
            </div>
            <div class="space-y-4">
                @foreach($todayScreenings as $show)
                    <div class="flex items-center justify-between border-b border-zinc-50 pb-3 dark:border-zinc-800/50 last:border-none last:pb-0">
                        <div>
                            <p class="font-medium text-sm text-zinc-900 dark:text-white">{{ $show['movie'] }}</p>
                            <p class="text-xs text-zinc-400">{{ $show['time'] }} · {{ $show['screen'] }}</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $show['occupancy'] }}%</p>
                                <p class="text-xs text-zinc-400">{{ $show['tickets'] }} seats</p>
                            </div>
                            @if($show['status'] === 'completed')
                                <span class="size-2 rounded-full bg-zinc-300 dark:bg-zinc-700" title="Ended"></span>
                            @elseif($show['status'] === 'running')
                                <span class="size-2 rounded-full bg-emerald-500 animate-ping" title="Running Now"></span>
                            @else
                                <span class="size-2 rounded-full bg-blue-500" title="Upcoming"></span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Recent bookings live feed --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-4">Live Activity Feed</h3>
            <div class="space-y-4">
                @foreach($recentBookings as $feed)
                    <div class="flex items-start gap-3">
                        <div class="rounded-full p-2 mt-0.5 {{ $feed['type'] === 'booking' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/20 dark:text-emerald-400' : 'bg-blue-50 text-blue-600 dark:bg-blue-950/20 dark:text-blue-400' }}">
                            <flux:icon name="{{ $feed['type'] === 'booking' ? 'ticket' : 'arrow-path-rounded-square' }}" class="size-4" />
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-zinc-800 dark:text-zinc-200">
                                <span class="font-medium text-zinc-900 dark:text-white">{{ $feed['customer'] }}</span> 
                                {{ $feed['type'] === 'booking' ? 'purchased' : 'refunded' }} 
                                <span class="font-medium">{{ $feed['seats'] }} seat{{ $feed['seats'] > 1 ? 's' : '' }}</span> for 
                                <span class="font-medium">{{ $feed['movie'] }}</span>
                            </p>
                            <span class="text-xs text-zinc-400">{{ $feed['time'] }}</span>
                        </div>
                        <div class="text-right text-sm font-bold text-zinc-900 dark:text-white">
                            {{ $feed['type'] === 'booking' ? '+' : '-' }}£{{ number_format($feed['amount'], 2) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
