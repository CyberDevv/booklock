<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

new #[Title('Revenue Reports'), Layout('layouts.app')] class extends Component {
    public string $timeRange = '30_days';

    public array $paymentMethods = [
        ['name' => 'Visa Cards', 'percentage' => 45, 'amount' => 5850.00],
        ['name' => 'MasterCard', 'percentage' => 28, 'amount' => 3640.00],
        ['name' => 'Apple Pay',  'percentage' => 18, 'amount' => 2340.00],
        ['name' => 'Amex Cards', 'percentage' => 9,  'amount' => 1170.00],
    ];

    public array $weeklyRevenue = [
        ['week' => 'W1', 'revenue' => 2100, 'height' => 40],
        ['week' => 'W2', 'revenue' => 3800, 'height' => 75],
        ['week' => 'W3', 'revenue' => 2900, 'height' => 55],
        ['week' => 'W4', 'revenue' => 4200, 'height' => 85],
    ];

    public array $recentReceipts = [
        ['ref' => 'TXN-839210', 'customer' => 'John Doe',      'method' => 'Visa *4242',   'amount' => 16.00, 'status' => 'settled',  'date' => '2026-07-01 10:30'],
        ['ref' => 'TXN-748301', 'customer' => 'Jane Smith',    'method' => 'MC *9876',     'amount' => 27.50, 'status' => 'settled',  'date' => '2026-07-01 14:15'],
        ['ref' => 'TXN-902847', 'customer' => 'Sarah Connor',   'method' => 'Visa *1111',   'amount' => 31.50, 'status' => 'refunded', 'date' => '2026-06-30 18:22'],
        ['ref' => 'TXN-472910', 'customer' => 'Michael Scott', 'method' => 'Amex *2004',   'amount' => 16.00, 'status' => 'settled',  'date' => '2026-07-02 11:45'],
        ['ref' => 'TXN-554102', 'customer' => 'Pam Beesly',    'method' => 'Visa *5555',   'amount' => 23.50, 'status' => 'settled',  'date' => '2026-07-02 12:10'],
    ];

    #[Computed]
    public function stats(): array
    {
        return [
            'gross'    => 13315.00,
            'refunds'  => 31.50,
            'net'      => 13283.50,
            'aov'      => 18.98,
        ];
    }
}
?>

<div class="flex flex-col gap-6 p-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-zinc-900 dark:text-white">Revenue & Finance</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Track net box office revenue, average transaction order pricing, and gateway processing.</p>
        </div>
        <div class="flex gap-2">
            <flux:select wire:model.live="timeRange" class="w-36">
                <flux:select.option value="7_days">7 Days</flux:select.option>
                <flux:select.option value="30_days">30 Days</flux:select.option>
                <flux:select.option value="12_months">12 Months</flux:select.option>
            </flux:select>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Gross Sales</p>
            <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">£{{ number_format($this->stats['gross'], 2) }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Total Refunds</p>
            <p class="mt-1 text-2xl font-bold text-blue-500">£{{ number_format($this->stats['refunds'], 2) }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Net Box Office</p>
            <p class="mt-1 text-2xl font-bold text-emerald-500">£{{ number_format($this->stats['net'], 2) }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Average Order Value (AOV)</p>
            <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">£{{ number_format($this->stats['aov'], 2) }}</p>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="grid gap-6 md:grid-cols-3">

        {{-- Weekly Sales Line/Area Graph (using SVG) --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 col-span-2 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-4">Revenue Trend (This Month)</h3>
                <div class="relative h-48 w-full pt-4">
                    {{-- SVG Line Chart --}}
                    <svg class="w-full h-full" viewBox="0 0 400 100" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="revenueGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.3"></stop>
                                <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.0"></stop>
                            </linearGradient>
                        </defs>
                        {{-- Area path --}}
                        <path d="M 0 100 L 0 80 L 100 45 L 200 65 L 300 35 L 400 100 Z" fill="url(#revenueGrad)"></path>
                        {{-- Stroke path --}}
                        <path d="M 0 80 L 100 45 L 200 65 L 300 35" fill="none" stroke="#3b82f6" stroke-width="2.5" stroke-linecap="round"></path>
                        {{-- Guide Gridlines --}}
                        <line x1="0" y1="20" x2="400" y2="20" stroke="#f4f4f5" stroke-dasharray="3" class="dark:stroke-zinc-800"></line>
                        <line x1="0" y1="50" x2="400" y2="50" stroke="#f4f4f5" stroke-dasharray="3" class="dark:stroke-zinc-800"></line>
                        <line x1="0" y1="80" x2="400" y2="80" stroke="#f4f4f5" stroke-dasharray="3" class="dark:stroke-zinc-800"></line>
                    </svg>
                </div>
            </div>
            <div class="flex justify-between text-xs text-zinc-400 mt-4 border-t border-zinc-100 dark:border-zinc-800 pt-2">
                <span>Week 1 (£2,100)</span>
                <span>Week 2 (£3,800)</span>
                <span>Week 3 (£2,900)</span>
                <span>Week 4 (£4,200)</span>
            </div>
        </div>

        {{-- Payment Method Breakdown --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-4">Revenue by Payment Gateway</h3>
                <div class="space-y-4">
                    @foreach($paymentMethods as $gateway)
                        <div class="space-y-1">
                            <div class="flex justify-between text-xs font-medium text-zinc-700 dark:text-zinc-300">
                                <span>{{ $gateway['name'] }}</span>
                                <span>£{{ number_format($gateway['amount'], 2) }} ({{ $gateway['percentage'] }}%)</span>
                            </div>
                            <div class="h-2 w-full rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                                <div 
                                    style="width: {{ $gateway['percentage'] }}%;" 
                                    class="h-full bg-blue-500 rounded-full dark:bg-blue-600"
                                ></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <p class="text-xs text-zinc-400 mt-4">Card terminals process over 73% of ticketing transactions.</p>
        </div>

    </div>

    {{-- Recent settled finances list --}}
    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="bg-zinc-50 dark:bg-zinc-800/40 p-4 border-b border-zinc-200 dark:border-zinc-700">
            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Recent Transactions Settlement</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-800/20">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Transaction ID</th>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Customer</th>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Payment Gateway</th>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Settled Date</th>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Gateway Status</th>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Payout Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 bg-white dark:divide-zinc-700/50 dark:bg-zinc-900">
                @foreach($recentReceipts as $receipt)
                    <tr>
                        <td class="px-4 py-3 font-mono text-zinc-900 dark:text-white">{{ $receipt['ref'] }}</td>
                        <td class="px-4 py-3 text-zinc-700 dark:text-zinc-200">{{ $receipt['customer'] }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300 font-mono text-xs">{{ $receipt['method'] }}</td>
                        <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 text-xs">
                            {{ \Carbon\Carbon::parse($receipt['date'])->format('d M Y, h:i A') }}
                        </td>
                        <td class="px-4 py-3">
                            @if($receipt['status'] === 'settled')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                    <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                    Settled
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                    <span class="size-1.5 rounded-full bg-blue-500"></span>
                                    Refunded
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">
                            £{{ number_format($receipt['amount'], 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
