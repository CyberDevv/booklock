<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

new #[Title('Analytics Reports'), Layout('layouts.app')] class extends Component {
    public string $timeRange = '30_days';
    public string $filterGenre = '';

    public array $movieStats = [
        [
            'title'       => 'Neon Horizon',
            'genre'       => 'Sci-Fi',
            'tickets_sold'=> 342,
            'screenings'  => 45,
            'occupancy'   => 82, // percentage
            'revenue'     => 4959.00,
        ],
        [
            'id'          => 2,
            'title'       => 'Moonlight Run',
            'genre'       => 'Thriller',
            'tickets_sold'=> 219,
            'screenings'  => 30,
            'occupancy'   => 68,
            'revenue'     => 2737.50,
        ],
        [
            'id'          => 3,
            'title'       => 'Golden Hour',
            'genre'       => 'Drama',
            'tickets_sold'=> 478,
            'screenings'  => 60,
            'occupancy'   => 89,
            'revenue'     => 5258.00,
        ],
        [
            'id'          => 4,
            'title'       => 'Iron Veil',
            'genre'       => 'Action',
            'tickets_sold'=> 91,
            'screenings'  => 18,
            'occupancy'   => 45,
            'revenue'     => 1365.00,
        ],
    ];

    public array $genreBreakdown = [
        ['name' => 'Sci-Fi',   'sales' => 342, 'percentage' => 30],
        ['name' => 'Thriller', 'sales' => 219, 'percentage' => 19],
        ['name' => 'Drama',    'sales' => 478, 'percentage' => 42],
        ['name' => 'Action',   'sales' => 91,  'percentage' => 9],
    ];

    public array $peakDays = [
        ['day' => 'Mon', 'bookings' => 45,  'height' => 35],
        ['day' => 'Tue', 'bookings' => 60,  'height' => 45],
        ['day' => 'Wed', 'bookings' => 120, 'height' => 90],
        ['day' => 'Thu', 'bookings' => 85,  'height' => 65],
        ['day' => 'Fri', 'bookings' => 150, 'height' => 100],
        ['day' => 'Sat', 'bookings' => 180, 'height' => 120],
        ['day' => 'Sun', 'bookings' => 140, 'height' => 105],
    ];

    #[Computed]
    public function filteredMovieStats(): array
    {
        return collect($this->movieStats)
            ->when($this->filterGenre, fn ($c) => $c->where('genre', $this->filterGenre))
            ->sortByDesc('tickets_sold')
            ->values()
            ->toArray();
    }

    #[Computed]
    public function stats(): array
    {
        $all = collect($this->movieStats);
        return [
            'total_tickets'   => $all->sum('tickets_sold'),
            'total_screenings'=> $all->sum('screenings'),
            'avg_occupancy'   => round($all->avg('occupancy')),
            'total_revenue'   => $all->sum('revenue'),
        ];
    }
}
?>

<div class="flex flex-col gap-6 p-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-zinc-900 dark:text-white">Analytics Reports</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Track box office ticket performance, genre demand levels, and cinema statistics.</p>
        </div>
        <div class="flex gap-2">
            <flux:select wire:model.live="timeRange" class="w-36">
                <flux:select.option value="7_days">7 Days</flux:select.option>
                <flux:select.option value="30_days">30 Days</flux:select.option>
                <flux:select.option value="12_months">12 Months</flux:select.option>
            </flux:select>
            <flux:button icon="arrow-down-tray" variant="ghost">Export PDF</flux:button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Total Tickets Sold</p>
            <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ number_format($this->stats['total_tickets']) }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Total Screenings</p>
            <p class="mt-1 text-2xl font-bold text-teal-500">{{ $this->stats['total_screenings'] }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Avg. Hall Occupancy</p>
            <p class="mt-1 text-2xl font-bold text-emerald-500">{{ $this->stats['avg_occupancy'] }}%</p>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Box Office Gross</p>
            <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">£{{ number_format($this->stats['total_revenue'], 2) }}</p>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="grid gap-6 md:grid-cols-2">

        {{-- Peak Booking Days Bar Chart --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-4">Peak Ticket Bookings by Day</h3>
            <div class="flex h-52 items-end justify-between gap-2 pt-6 border-b border-zinc-100 dark:border-zinc-800">
                @foreach($peakDays as $day)
                    <div class="flex flex-1 flex-col items-center gap-2">
                        <div class="group relative w-full flex justify-center">
                            {{-- Bar --}}
                            <div 
                                style="height: {{ $day['height'] }}px;" 
                                class="w-8 rounded-t bg-blue-500/80 group-hover:bg-blue-500 transition-colors duration-200 dark:bg-blue-600/80 dark:group-hover:bg-blue-600"
                            ></div>
                            {{-- Tooltip --}}
                            <span class="absolute -top-8 scale-0 transition-transform group-hover:scale-100 rounded bg-zinc-800 px-2 py-1 text-xs text-white whitespace-nowrap">
                                {{ $day['bookings'] }} booked
                            </span>
                        </div>
                        <span class="text-xs text-zinc-500 mt-2">{{ $day['day'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Demand by Genre Progress Bars --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-4">Ticket Sales by Genre</h3>
                <div class="space-y-4">
                    @foreach($genreBreakdown as $genre)
                        <div class="space-y-1">
                            <div class="flex justify-between text-xs font-medium text-zinc-700 dark:text-zinc-300">
                                <span>{{ $genre['name'] }}</span>
                                <span>{{ $genre['sales'] }} sales ({{ $genre['percentage'] }}%)</span>
                            </div>
                            <div class="h-2 w-full rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                                <div 
                                    style="width: {{ $genre['percentage'] }}%;" 
                                    class="h-full bg-teal-500 rounded-full dark:bg-teal-600"
                                ></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <p class="text-xs text-zinc-400 mt-4">Drama and Sci-Fi continue to lead weekly ticket distribution shares.</p>
        </div>

    </div>

    {{-- Box Office Titles Performance Table --}}
    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="bg-zinc-50 dark:bg-zinc-800/40 p-4 border-b border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Movie Title Performance</h3>
            <flux:select wire:model.live="filterGenre" placeholder="All Genres" class="w-36">
                <flux:select.option value="">All Genres</flux:select.option>
                <flux:select.option value="Sci-Fi">Sci-Fi</flux:select.option>
                <flux:select.option value="Thriller">Thriller</flux:select.option>
                <flux:select.option value="Drama">Drama</flux:select.option>
                <flux:select.option value="Action">Action</flux:select.option>
            </flux:select>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-800/20">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Movie</th>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Genre</th>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Screenings</th>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Avg. Hall Occupancy</th>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Tickets Sold</th>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Total Gross</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 bg-white dark:divide-zinc-700/50 dark:bg-zinc-900">
                @forelse($this->filteredMovieStats as $movie)
                    <tr>
                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">{{ $movie['title'] }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $movie['genre'] }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $movie['screenings'] }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-16 bg-zinc-100 dark:bg-zinc-800 rounded-full h-1.5 overflow-hidden">
                                    <div 
                                        style="width: {{ $movie['occupancy'] }}%;" 
                                        class="h-full rounded-full {{ $movie['occupancy'] >= 80 ? 'bg-emerald-500' : ($movie['occupancy'] >= 60 ? 'bg-blue-500' : 'bg-amber-500') }}"
                                    ></div>
                                </div>
                                <span class="text-xs font-semibold">{{ $movie['occupancy'] }}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">{{ number_format($movie['tickets_sold']) }}</td>
                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">£{{ number_format($movie['revenue'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-16 text-center text-zinc-400">
                            No stats found for this genre.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
