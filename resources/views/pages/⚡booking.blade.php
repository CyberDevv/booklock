<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;

new #[Title('Book Movie'), Layout('layouts.website')] class extends Component {
    public string $movieSlug = '';

    public int $selectedDate = 0;

    public ?string $selectedTime = null;

    public array $selectedSeats = [];

    public array $dates = [
        ['label' => 'Today',    'short' => 'Jul 2',  'times' => ['2:00 PM', '5:15 PM', '7:45 PM', '10:30 PM']],
        ['label' => 'Tomorrow', 'short' => 'Jul 3',  'times' => ['1:30 PM', '4:00 PM', '8:00 PM']],
        ['label' => 'Sat',      'short' => 'Jul 5',  'times' => ['3:00 PM', '6:30 PM', '9:15 PM']],
    ];

    public array $takenSeats = ['A3','A4','A7','B2','B5','B6','B9','C1','C4','C8','C10','D3','D7','D8','E2','E5','E9','F1','F6','G4','G7','G8','G9'];

    public array $rows = ['A','B','C','D','E','F','G'];

    public array $leftSeats = [1,2,3,4,5];

    public array $rightSeats = [6,7,8,9,10];

    public float $bookingFee = 1.50;

    public string $selectedTheater = 'screen-3';

    public array $theaters = [
        ['id' => 'grand-vip', 'name' => 'Grand Screen (VIP)', 'price_modifier' => 5.00, 'features' => 'Recliner Seats, IMAX'],
        ['id' => 'screen-2', 'name' => 'Screen 2 (Dolby)', 'price_modifier' => 2.50, 'features' => 'Dolby Atmos, Cozy Seats'],
        ['id' => 'screen-3', 'name' => 'Screen 3 (Standard)', 'price_modifier' => 0.00, 'features' => 'Standard Seating'],
    ];

    public int $maxSeats = 8;

    public array $movies = [
        'neon-horizon' => [
            'title'    => 'Neon Horizon',
            'genre'    => 'Sci-Fi',
            'duration' => '2h 08m',
            'rating'   => '4.8',
            'format'   => 'IMAX',
            'price'    => 14.50,
            'desc'     => 'A visually stunning sci-fi epic where the boundaries of reality blur with digital consciousness.',
            'image'    => 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=1400&q=80',
        ],
        'moonlight-run' => [
            'title'    => 'Moonlight Run',
            'genre'    => 'Thriller',
            'duration' => '1h 54m',
            'rating'   => '4.6',
            'format'   => 'Dolby Atmos',
            'price'    => 12.50,
            'desc'     => 'A heart-pounding thriller following a former agent racing against time through neon-lit city streets.',
            'image'    => 'https://images.unsplash.com/photo-1516280440614-37939bbacd81?auto=format&fit=crop&w=1400&q=80',
        ],
        'golden-hour' => [
            'title'    => 'Golden Hour',
            'genre'    => 'Drama',
            'duration' => '2h 12m',
            'rating'   => '4.9',
            'format'   => 'Standard',
            'price'    => 11.00,
            'desc'     => 'An emotional drama following three strangers whose lives intertwine during one golden hour.',
            'image'    => 'https://images.unsplash.com/photo-1478720568477-152d9b164e26?auto=format&fit=crop&w=1400&q=80',
        ],
    ];

    public function mount(string $movie): void
    {
        $this->movieSlug = $movie;
    }

    #[Computed]
    public function movie(): array
    {
        return $this->movies[$this->movieSlug] ?? $this->movies['neon-horizon'];
    }

    #[Computed]
    public function pricePerSeat(): float
    {
        $basePrice = $this->movie()['price'];
        $theater = collect($this->theaters)->firstWhere('id', $this->selectedTheater);
        $modifier = $theater ? $theater['price_modifier'] : 0.00;
        return $basePrice + $modifier;
    }

    public function selectTheater(string $theaterId): void
    {
        $this->selectedTheater = $theaterId;
    }

    #[Computed]
    public function subtotal(): float
    {
        return count($this->selectedSeats) * $this->pricePerSeat();
    }

    #[Computed]
    public function total(): float
    {
        return count($this->selectedSeats) > 0 
            ? $this->subtotal() + $this->bookingFee 
            : 0.0;
    }

    #[Computed]
    public function canConfirm(): bool
    {
        return $this->selectedTime !== null && count($this->selectedSeats) > 0;
    }

    public function selectDate(int $index): void
    {
        $this->selectedDate = $index;
        $this->selectedTime = null;
    }

    public function selectTime(string $time): void
    {
        $this->selectedTime = $time;
    }

    public function toggleSeat(string $seatId): void
    {
        if (in_array($seatId, $this->takenSeats)) {
            return;
        }

        if (in_array($seatId, $this->selectedSeats)) {
            $this->selectedSeats = array_values(array_filter($this->selectedSeats, fn($s) => $s !== $seatId));
        } else {
            if (count($this->selectedSeats) >= $this->maxSeats) {
                return;
            }
            $this->selectedSeats[] = $seatId;
        }
    }

    public function removeSeat(string $seatId): void
    {
        $this->selectedSeats = array_values(array_filter($this->selectedSeats, fn($s) => $s !== $seatId));
    }

    public bool $showConfirmModal = false;

    public string $bookingStep = 'summary'; // 'summary' | 'success'

    public string $bookingRef = '';

    public function confirm(): void
    {
        if ($this->canConfirm()) {
            $this->showConfirmModal = true;
            $this->bookingStep = 'summary';
        }
    }

    public function closeModal(): void
    {
        $this->showConfirmModal = false;
        $this->bookingStep = 'summary';
    }

    public function initiatePayment(): void
    {
        // Generate a mock booking reference
        $this->bookingRef = 'BL-' . strtoupper(substr(md5(uniqid()), 0, 8));

        // Dispatch a browser event to launch Paystack popup
        $this->dispatch('launch-paystack', [
            'amount'    => $this->total(),
            'email'     => 'customer@example.com',
            'reference' => $this->bookingRef,
            'movie'     => $this->movie()['title'],
        ]);
    }

    public function paymentSuccessful(): void
    {
        $this->bookingStep = 'success';
    }
};
?>

<div class="relative grid gap-6 pb-28 lg:pb-0">

    {{-- ══════════════════════════════════════
         MOVIE HERO — Immersive Backdrop
    ══════════════════════════════════════ --}}
    <div class="relative overflow-hidden rounded-[1.5rem] min-h-[280px] sm:min-h-[380px] flex flex-col justify-end">
        {{-- Blurred backdrop --}}
        <div class="absolute inset-0">
            <img src="{{ $this->movie['image'] }}" alt="" class="h-full w-full object-cover scale-105 blur-[2px]" />
            <div class="absolute inset-0 bg-neutral-950/65"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-neutral-950 via-neutral-950/20 to-transparent"></div>
        </div>

        {{-- Content --}}
        <div class="relative w-full px-5 pt-8 pb-7 sm:px-8 sm:pt-12 sm:pb-8">

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-xs text-white/50">
                <a href="{{ route('home') }}" class="flex items-center gap-1 hover:text-white transition" wire:navigate>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-3.5"><path fill-rule="evenodd" d="M9.293 2.293a1 1 0 0 1 1.414 0l7 7A1 1 0 0 1 17 11h-1v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-3a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6H3a1 1 0 0 1-.707-1.707l7-7Z" clip-rule="evenodd" /></svg>
                    Home
                </a>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-3 opacity-40"><path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
                <span class="text-white/80">{{ $this->movie['title'] }}</span>
            </nav>

            {{-- Movie info --}}
            <div class="mt-5 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full border border-white/20 bg-white/10 px-2.5 py-0.5 text-[11px] font-semibold uppercase tracking-wide text-white backdrop-blur-sm">{{ $this->movie['genre'] }}</span>
                        <span class="rounded-full border border-white/20 bg-white/10 px-2.5 py-0.5 text-[11px] font-semibold uppercase tracking-wide text-white backdrop-blur-sm">{{ $this->movie['format'] }}</span>
                        <span class="rounded-full border border-white/20 bg-white/10 px-2.5 py-0.5 text-[11px] font-medium text-white/70 backdrop-blur-sm">{{ $this->movie['duration'] }}</span>
                    </div>
                    <h1 class="mt-3 text-2xl font-bold leading-tight text-white sm:text-3xl lg:text-4xl">{{ $this->movie['title'] }}</h1>
                    <p class="mt-2 max-w-lg text-sm leading-relaxed text-white/60">{{ $this->movie['desc'] }}</p>
                </div>

                {{-- Rating badge --}}
                <div class="shrink-0 flex items-center gap-2 rounded-2xl border border-white/15 bg-white/10 px-4 py-2.5 backdrop-blur-sm self-start sm:self-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 text-amber-400">
                        <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401Z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <p class="text-xl font-bold leading-none text-white">{{ $this->movie['rating'] }}</p>
                        <p class="mt-0.5 text-[10px] text-white/50">Rating</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         MAIN CONTENT GRID
    ══════════════════════════════════════ --}}
    <div class="grid gap-5 lg:grid-cols-[1fr_350px] lg:items-start">

        {{-- ───────── LEFT COLUMN ───────── --}}
        <div class="grid gap-5">

            {{-- THEATER SELECTOR --}}
            <div class="overflow-hidden rounded-[1.25rem] border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
                <div class="border-b border-neutral-100 px-5 py-4 dark:border-neutral-800">
                    <h2 class="text-xs font-bold uppercase tracking-[0.2em] text-neutral-400 dark:text-neutral-500">Select Theater</h2>
                </div>
                <div class="p-5">
                    <div class="grid gap-3 sm:grid-cols-3">
                        @foreach ($theaters as $theater)
                            @php
                                $isSelected = $selectedTheater === $theater['id'];
                            @endphp
                            <button
                                type="button"
                                wire:click="selectTheater('{{ $theater['id'] }}')"
                                class="flex flex-col text-left rounded-xl p-4 transition-all duration-200 border {{ $isSelected ? 'border-neutral-900 bg-neutral-900/5 dark:border-white dark:bg-white/5 ring-1 ring-neutral-900 dark:ring-white' : 'border-neutral-200 bg-neutral-50 hover:border-neutral-300 hover:bg-neutral-100/50 dark:border-neutral-800 dark:bg-neutral-800/40 dark:hover:border-neutral-700 dark:hover:bg-neutral-800/80' }}"
                            >
                                <span class="text-xs font-bold text-neutral-900 dark:text-white">{{ $theater['name'] }}</span>
                                <span class="mt-1 text-[10px] text-neutral-500 dark:text-neutral-400">{{ $theater['features'] }}</span>
                                <span class="mt-3 text-xs font-semibold text-neutral-800 dark:text-neutral-200">
                                    @if ($theater['price_modifier'] > 0)
                                        +£{{ number_format($theater['price_modifier'], 2) }}
                                    @else
                                        Standard Price
                                    @endif
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- SHOWTIME PICKER --}}
            <div class="overflow-hidden rounded-[1.25rem] border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
                <div class="border-b border-neutral-100 px-5 py-4 dark:border-neutral-800">
                    <h2 class="text-xs font-bold uppercase tracking-[0.2em] text-neutral-400 dark:text-neutral-500">Select Showtime</h2>
                </div>
                <div class="p-5">
                    {{-- Date Pills --}}
                    <div class="flex gap-2 overflow-x-auto pb-1">
                        @foreach ($dates as $i => $d)
                            <button
                                type="button"
                                wire:click="selectDate({{ $i }})"
                                class="shrink-0 rounded-xl px-5 py-2.5 text-center transition-all duration-200 focus:outline-none {{ $selectedDate === $i ? 'bg-neutral-900 text-white shadow-md dark:bg-white dark:text-neutral-900' : 'bg-neutral-100 text-neutral-700 hover:bg-neutral-200 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700' }}"
                            >
                                <p class="text-xs font-bold">{{ $d['label'] }}</p>
                                <p class="mt-0.5 text-[11px] opacity-60">{{ $d['short'] }}</p>
                            </button>
                        @endforeach
                    </div>

                    {{-- Time Slots --}}
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($dates[$selectedDate]['times'] as $t)
                            <button
                                type="button"
                                wire:click="selectTime('{{ $t }}')"
                                class="rounded-full px-4 py-2 text-sm font-medium transition-all duration-200 focus:outline-none {{ $selectedTime === $t ? 'bg-neutral-900 text-white shadow-md dark:bg-white dark:text-neutral-900' : 'border border-neutral-200 bg-neutral-50 text-neutral-700 hover:border-neutral-400 hover:bg-neutral-100 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700' }}"
                            >
                                {{ $t }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Selected showtime hint --}}
                    @if ($selectedTime)
                        <div class="mt-4 flex items-center gap-2 rounded-xl bg-neutral-50 px-3 py-2 dark:bg-neutral-800">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0 text-emerald-500">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                            </svg>
                            <p class="text-xs font-medium text-neutral-700 dark:text-neutral-300">
                                <span>{{ $dates[$selectedDate]['label'] }}, {{ $dates[$selectedDate]['short'] }} at {{ $selectedTime }}</span>
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- SEAT MAP --}}
            <div class="overflow-hidden rounded-[1.25rem] border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex items-center justify-between border-b border-neutral-100 px-5 py-4 dark:border-neutral-800">
                    <h2 class="text-xs font-bold uppercase tracking-[0.2em] text-neutral-400 dark:text-neutral-500">Pick Your Seats</h2>
                    <span
                        class="rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors {{ count($selectedSeats) > 0 ? 'bg-neutral-900 text-white dark:bg-white dark:text-neutral-900' : 'bg-neutral-100 text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400' }}"
                    >
                        {{ count($selectedSeats) }} / {{ $maxSeats }} seats
                    </span>
                </div>

                <div class="p-5">
                    {{-- Screen Indicator --}}
                    <div class="mb-7 text-center">
                        <div class="relative mx-auto max-w-xs">
                            <div class="h-px w-full rounded-full bg-gradient-to-r from-transparent via-neutral-400 to-transparent dark:via-neutral-500"></div>
                            <div class="absolute inset-x-0 -top-2 h-4 rounded-b-[100%] bg-gradient-to-b from-neutral-300/30 to-transparent dark:from-neutral-500/20 blur-sm"></div>
                        </div>
                        <p class="mt-2 text-[9px] font-bold uppercase tracking-[0.4em] text-neutral-400 dark:text-neutral-600">Screen</p>
                    </div>

                    {{-- Seat Grid --}}
                    <div class="overflow-x-auto">
                        <div class="mx-auto w-fit space-y-2 pb-1">
                            @foreach ($rows as $row)
                                <div class="flex items-center gap-2">
                                    {{-- Row label left --}}
                                    <span class="w-5 shrink-0 text-center text-[11px] font-bold text-neutral-400 dark:text-neutral-600">{{ $row }}</span>

                                    {{-- Left block --}}
                                    <div class="flex gap-1 sm:gap-1.5">
                                        @foreach ($leftSeats as $num)
                                            @php
                                                $seatId = $row . $num;
                                                $isTaken = in_array($seatId, $takenSeats);
                                                $isSelected = in_array($seatId, $selectedSeats);
                                                
                                                $seatClass = 'relative flex h-7 w-7 shrink-0 items-center justify-center rounded-t-lg rounded-b-sm border text-[10px] font-semibold transition-all duration-150 sm:h-8 sm:w-8';
                                                if ($isTaken) {
                                                    $seatClass .= ' bg-neutral-200/60 border-neutral-200 dark:bg-neutral-800/40 dark:border-neutral-800 cursor-not-allowed opacity-40';
                                                } elseif ($isSelected) {
                                                    $seatClass .= ' bg-neutral-900 border-neutral-900 text-white scale-110 shadow-lg ring-2 ring-neutral-900/30 dark:bg-white dark:border-white dark:text-neutral-900 dark:ring-white/30';
                                                } else {
                                                    $seatClass .= ' bg-white border-neutral-300 text-neutral-600 hover:border-neutral-500 hover:bg-neutral-50 hover:scale-105 cursor-pointer dark:bg-neutral-800/80 dark:border-neutral-700 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:border-neutral-500';
                                                }
                                            @endphp
                                            <button
                                                type="button"
                                                wire:click="toggleSeat('{{ $seatId }}')"
                                                @disabled($isTaken)
                                                class="{{ $seatClass }}"
                                                title="Seat {{ $seatId }}"
                                            >
                                                <span class="leading-none">{{ $num }}</span>
                                                {{-- Seat base --}}
                                                <span class="absolute -bottom-1 left-1 right-1 h-0.5 rounded-full bg-current opacity-20"></span>
                                            </button>
                                        @endforeach
                                    </div>

                                    {{-- Aisle --}}
                                    <div class="w-5 sm:w-6 shrink-0 flex items-center justify-center">
                                        <span class="text-[8px] font-bold uppercase tracking-widest text-neutral-300 dark:text-neutral-700 [writing-mode:vertical-lr]">aisle</span>
                                    </div>

                                    {{-- Right block --}}
                                    <div class="flex gap-1 sm:gap-1.5">
                                        @foreach ($rightSeats as $num)
                                            @php
                                                $seatId = $row . $num;
                                                $isTaken = in_array($seatId, $takenSeats);
                                                $isSelected = in_array($seatId, $selectedSeats);
                                                
                                                $seatClass = 'relative flex h-7 w-7 shrink-0 items-center justify-center rounded-t-lg rounded-b-sm border text-[10px] font-semibold transition-all duration-150 sm:h-8 sm:w-8';
                                                if ($isTaken) {
                                                    $seatClass .= ' bg-neutral-200/60 border-neutral-200 dark:bg-neutral-800/40 dark:border-neutral-800 cursor-not-allowed opacity-40';
                                                } elseif ($isSelected) {
                                                    $seatClass .= ' bg-neutral-900 border-neutral-900 text-white scale-110 shadow-lg ring-2 ring-neutral-900/30 dark:bg-white dark:border-white dark:text-neutral-900 dark:ring-white/30';
                                                } else {
                                                    $seatClass .= ' bg-white border-neutral-300 text-neutral-600 hover:border-neutral-500 hover:bg-neutral-50 hover:scale-105 cursor-pointer dark:bg-neutral-800/80 dark:border-neutral-700 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:border-neutral-500';
                                                }
                                            @endphp
                                            <button
                                                type="button"
                                                wire:click="toggleSeat('{{ $seatId }}')"
                                                @disabled($isTaken)
                                                class="{{ $seatClass }}"
                                                title="Seat {{ $seatId }}"
                                            >
                                                <span class="leading-none">{{ $num }}</span>
                                                <span class="absolute -bottom-1 left-1 right-1 h-0.5 rounded-full bg-current opacity-20"></span>
                                            </button>
                                        @endforeach
                                    </div>

                                    {{-- Row label right --}}
                                    <span class="w-5 shrink-0 text-center text-[11px] font-bold text-neutral-400 dark:text-neutral-600">{{ $row }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Legend --}}
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-4 border-t border-neutral-100 pt-5 dark:border-neutral-800">
                        <div class="flex items-center gap-2">
                            <div class="h-5 w-5 rounded-t-lg rounded-b-sm border border-neutral-300 bg-white dark:border-neutral-700 dark:bg-neutral-800"></div>
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">Available</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="h-5 w-5 rounded-t-lg rounded-b-sm bg-neutral-900 dark:bg-white ring-2 ring-neutral-900/20 dark:ring-white/20"></div>
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">Selected</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="h-5 w-5 rounded-t-lg rounded-b-sm border border-neutral-200 bg-neutral-200/60 opacity-50 dark:border-neutral-800 dark:bg-neutral-800/40"></div>
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">Taken</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ───────── RIGHT COLUMN — Desktop Summary ───────── --}}
        <div class="hidden lg:block lg:sticky lg:top-5">
            <div class="overflow-hidden rounded-[1.25rem] border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">

                <div class="border-b border-neutral-100 px-5 py-4 dark:border-neutral-800">
                    <h2 class="text-xs font-bold uppercase tracking-[0.2em] text-neutral-400 dark:text-neutral-500">Booking Summary</h2>
                </div>

                <div class="p-5">
                    {{-- Movie thumb + info --}}
                    <div class="flex gap-3">
                        <img src="{{ $this->movie['image'] }}" alt="{{ $this->movie['title'] }}" class="h-16 w-12 shrink-0 rounded-xl object-cover" />
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-neutral-900 dark:text-white">{{ $this->movie['title'] }}</p>
                            <p class="mt-0.5 text-xs text-neutral-500 dark:text-neutral-400">{{ $this->movie['genre'] }} · {{ $this->movie['duration'] }}</p>
                            @php
                                $theaterName = collect($theaters)->firstWhere('id', $selectedTheater)['name'] ?? 'Standard Screen';
                            @endphp
                            <p class="mt-1 text-xs font-medium text-neutral-800 dark:text-neutral-200">
                                {{ $theaterName }}
                            </p>
                            <p class="mt-0.5 text-xs font-medium {{ $selectedTime ? 'text-emerald-600 dark:text-emerald-400' : 'text-neutral-400 dark:text-neutral-500' }}">
                                {{ $selectedTime ? $dates[$selectedDate]['short'] . ' · ' . $selectedTime : 'No showtime selected' }}
                            </p>
                        </div>
                    </div>

                    {{-- Seats --}}
                    <div class="mt-5">
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-neutral-400 dark:text-neutral-500">Selected Seats</p>
                        @if (empty($selectedSeats))
                            <div class="rounded-xl border border-dashed border-neutral-200 p-4 text-center dark:border-neutral-700">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto size-6 text-neutral-300 dark:text-neutral-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                </svg>
                                <p class="mt-2 text-xs text-neutral-400 dark:text-neutral-500">Click on the seat map to select</p>
                            </div>
                        @else
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($selectedSeats as $seat)
                                    <button
                                        type="button"
                                        wire:click="removeSeat('{{ $seat }}')"
                                        class="group inline-flex items-center gap-1 rounded-full bg-neutral-900 py-0.5 pl-2.5 pr-1.5 text-xs font-semibold text-white transition hover:bg-red-600 dark:bg-white dark:text-neutral-900 dark:hover:bg-red-500 dark:hover:text-white"
                                    >
                                        <span>{{ $seat }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-3 opacity-50 group-hover:opacity-100">
                                            <path d="M5.28 4.22a.75.75 0 0 0-1.06 1.06L6.94 8l-2.72 2.72a.75.75 0 1 0 1.06 1.06L8 9.06l2.72 2.72a.75.75 0 1 0 1.06-1.06L9.06 8l2.72-2.72a.75.75 0 0 0-1.06-1.06L8 6.94 5.28 4.22Z" />
                                        </svg>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Price breakdown --}}
                    <div class="mt-5 space-y-2 border-t border-neutral-100 pt-4 text-sm dark:border-neutral-800">
                        <div class="flex justify-between">
                            <span class="text-neutral-500 dark:text-neutral-400">{{ count($selectedSeats) }} seat(s) × £{{ number_format($this->pricePerSeat, 2) }}</span>
                            <span class="font-medium text-neutral-900 dark:text-white">£{{ number_format($this->subtotal, 2) }}</span>
                        </div>
                        @if (count($selectedSeats) > 0)
                            <div class="flex justify-between text-neutral-500 dark:text-neutral-400">
                                <span>Booking fee</span>
                                <span class="font-medium text-neutral-900 dark:text-white">£{{ number_format($bookingFee, 2) }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Total --}}
                    <div class="mt-3 flex items-center justify-between rounded-xl bg-neutral-50 px-4 py-3 dark:bg-neutral-800">
                        <span class="font-semibold text-neutral-900 dark:text-white">Total</span>
                        <span class="text-xl font-bold text-neutral-900 dark:text-white">£{{ number_format($this->total, 2) }}</span>
                    </div>
                </div>

                {{-- Tear line --}}
                <div class="relative flex items-center">
                    <div class="h-5 w-5 -ml-2.5 shrink-0 rounded-full bg-neutral-100 border border-neutral-200 dark:bg-zinc-950 dark:border-neutral-800"></div>
                    <div class="flex-1 border-t border-dashed border-neutral-200 dark:border-neutral-800"></div>
                    <div class="h-5 w-5 -mr-2.5 shrink-0 rounded-full bg-neutral-100 border border-neutral-200 dark:bg-zinc-950 dark:border-neutral-800"></div>
                </div>

                <div class="p-5 space-y-3">
                    {{-- Validation hints --}}
                    @if (!$selectedTime)
                        <div class="flex items-start gap-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2.5 dark:border-amber-900/40 dark:bg-amber-900/20">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0 text-amber-500">
                                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                            </svg>
                            <p class="text-xs text-amber-700 dark:text-amber-400">Pick a showtime to continue.</p>
                        </div>
                    @elseif (count($selectedSeats) === 0)
                        <div class="flex items-start gap-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2.5 dark:border-amber-900/40 dark:bg-amber-900/20">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0 text-amber-500">
                                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                            </svg>
                            <p class="text-xs text-amber-700 dark:text-amber-400">Select at least one seat on the map.</p>
                        </div>
                    @endif

                    {{-- Confirm CTA --}}
                    <button
                        type="button"
                        wire:click="confirm"
                        @disabled(!$this->canConfirm)
                        class="flex w-full items-center justify-center gap-2 rounded-full py-3 text-sm font-semibold transition-all duration-200 {{ $this->canConfirm ? 'bg-neutral-900 text-white hover:bg-neutral-700 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200 cursor-pointer' : 'bg-neutral-100 text-neutral-400 cursor-not-allowed dark:bg-neutral-800 dark:text-neutral-600' }}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a3 3 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                        </svg>
                        Confirm Booking
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════
         MOBILE STICKY BOTTOM BAR
    ══════════════════════════════════════ --}}
    <div class="fixed bottom-0 inset-x-0 z-50 lg:hidden">
        <div class="border-t border-neutral-200 bg-white/90 px-4 py-3 backdrop-blur-xl dark:border-neutral-800 dark:bg-neutral-900/90">
            <div class="flex items-center gap-3">
                {{-- Summary info --}}
                <div class="min-w-0 flex-1">
                    @php
                        $theaterNameShort = collect($theaters)->firstWhere('id', $selectedTheater)['name'] ?? 'Standard Screen';
                    @endphp
                    <p class="truncate text-xs text-neutral-500 dark:text-neutral-400">
                        @if (empty($selectedSeats))
                            No seats selected
                        @else
                            {{ $theaterNameShort }} · {{ count($selectedSeats) }} seat(s) {{ $selectedTime ? ' · ' . $selectedTime : '' }}
                        @endif
                    </p>
                    <p class="text-base font-bold text-neutral-900 dark:text-white">£{{ number_format($this->total, 2) }}</p>
                </div>

                {{-- Confirm button --}}
                <button
                    type="button"
                    wire:click="confirm"
                    @disabled(!$this->canConfirm)
                    class="shrink-0 rounded-full px-5 py-2.5 text-sm font-semibold transition-all duration-200 {{ $this->canConfirm ? 'bg-neutral-900 text-white active:bg-neutral-700 dark:bg-white dark:text-neutral-900' : 'bg-neutral-200 text-neutral-400 cursor-not-allowed dark:bg-neutral-800 dark:text-neutral-600' }}"
                >
                    Confirm Booking
                </button>
            </div>
        </div>
        {{-- Safe area spacer for iOS --}}
        <div class="h-safe-area-inset-bottom bg-white/90 dark:bg-neutral-900/90 backdrop-blur-xl"></div>
    </div>

    {{-- ══════════════════════════════════════
         BOOKING CONFIRMATION MODAL
    ══════════════════════════════════════ --}}
    @if ($showConfirmModal)
        <div
            class="fixed inset-0 z-[100] flex items-end justify-center sm:items-center p-0 sm:p-4"
            x-data
            x-init="document.body.classList.add('overflow-hidden')"
            x-destroy="document.body.classList.remove('overflow-hidden')"
        >
            {{-- Backdrop --}}
            <div
                class="absolute inset-0 bg-neutral-950/60 backdrop-blur-sm"
                wire:click="closeModal"
            ></div>

            {{-- Modal Panel --}}
            <div class="relative z-10 w-full sm:max-w-md bg-white dark:bg-neutral-900 rounded-t-[2rem] sm:rounded-[1.5rem] shadow-2xl overflow-hidden">

                @if ($bookingStep === 'summary')
                    {{-- ─── STEP 1: Booking Summary ─── --}}
                    <div class="flex items-center justify-between border-b border-neutral-100 dark:border-neutral-800 px-6 py-4">
                        <h2 class="font-semibold text-neutral-900 dark:text-white">Confirm Booking</h2>
                        <button wire:click="closeModal" class="rounded-full p-1.5 text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" /></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-5">
                        {{-- Movie info row --}}
                        <div class="flex gap-3">
                            <img src="{{ $this->movie['image'] }}" class="h-16 w-12 shrink-0 rounded-xl object-cover" />
                            <div class="min-w-0">
                                <p class="font-semibold text-neutral-900 dark:text-white">{{ $this->movie['title'] }}</p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">{{ $this->movie['genre'] }} · {{ $this->movie['duration'] }} · {{ $this->movie['format'] }}</p>
                            </div>
                        </div>

                        {{-- Details grid --}}
                        <div class="grid grid-cols-2 gap-3">
                            @php
                                $theaterLabel = collect($theaters)->firstWhere('id', $selectedTheater)['name'] ?? 'Standard Screen';
                            @endphp
                            <div class="rounded-xl bg-neutral-50 dark:bg-neutral-800 px-4 py-3">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 dark:text-neutral-500">Theater</p>
                                <p class="mt-1 text-sm font-semibold text-neutral-900 dark:text-white leading-tight">{{ $theaterLabel }}</p>
                            </div>
                            <div class="rounded-xl bg-neutral-50 dark:bg-neutral-800 px-4 py-3">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 dark:text-neutral-500">Showtime</p>
                                <p class="mt-1 text-sm font-semibold text-neutral-900 dark:text-white leading-tight">{{ $dates[$selectedDate]['short'] }} · {{ $selectedTime }}</p>
                            </div>
                            <div class="rounded-xl bg-neutral-50 dark:bg-neutral-800 px-4 py-3">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 dark:text-neutral-500">Seats</p>
                                <p class="mt-1 text-sm font-semibold text-neutral-900 dark:text-white leading-tight">{{ implode(', ', $selectedSeats) }}</p>
                            </div>
                            <div class="rounded-xl bg-neutral-50 dark:bg-neutral-800 px-4 py-3">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 dark:text-neutral-500">Price/Seat</p>
                                <p class="mt-1 text-sm font-semibold text-neutral-900 dark:text-white leading-tight">£{{ number_format($this->pricePerSeat, 2) }}</p>
                            </div>
                        </div>

                        {{-- Price breakdown --}}
                        <div class="space-y-1.5 border-t border-neutral-100 dark:border-neutral-800 pt-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-neutral-500 dark:text-neutral-400">{{ count($selectedSeats) }} seat(s)</span>
                                <span class="text-neutral-900 dark:text-white">£{{ number_format($this->subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-neutral-500 dark:text-neutral-400">Booking fee</span>
                                <span class="text-neutral-900 dark:text-white">£{{ number_format($bookingFee, 2) }}</span>
                            </div>
                            <div class="flex justify-between font-bold text-base pt-1 border-t border-neutral-100 dark:border-neutral-800">
                                <span class="text-neutral-900 dark:text-white">Total</span>
                                <span class="text-neutral-900 dark:text-white">£{{ number_format($this->total, 2) }}</span>
                            </div>
                        </div>

                        {{-- Paystack CTA --}}
                        <button
                            type="button"
                            wire:click="initiatePayment"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-70 cursor-not-allowed"
                            class="relative flex w-full items-center justify-center gap-2.5 rounded-full bg-[#0BA4DB] py-3.5 text-sm font-bold text-white hover:bg-[#0993c5] transition-all duration-200 shadow-lg shadow-[#0BA4DB]/25"
                        >
                            {{-- Spinner: only visible while initiatePayment is running --}}
                            <svg wire:loading wire:target="initiatePayment" class="size-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            {{-- Lock icon: visible when idle --}}
                            <svg wire:loading.remove wire:target="initiatePayment" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd" />
                            </svg>
                            Proceed to Payment
                        </button>

                        <p class="text-center text-[11px] text-neutral-400 dark:text-neutral-500">
                            🔒 Secured by Paystack · Test Mode
                        </p>
                    </div>

                @elseif ($bookingStep === 'success')
                    {{-- ─── STEP 2: Ticket Success ─── --}}
                    <div id="ticket-card" class="relative overflow-hidden">
                        {{-- Confetti-like gradient top --}}
                        <div class="h-1.5 w-full bg-gradient-to-r from-emerald-400 via-teal-400 to-cyan-500"></div>

                        <div class="px-6 pt-6 pb-4 text-center">
                            {{-- Success icon --}}
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/40">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-7 text-emerald-600 dark:text-emerald-400">
                                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <h2 class="mt-3 text-lg font-bold text-neutral-900 dark:text-white">Booking Confirmed!</h2>
                            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Your tickets are ready. Ref: <span class="font-semibold text-neutral-700 dark:text-neutral-200">{{ $bookingRef }}</span></p>
                        </div>

                        {{-- Tear line --}}
                        <div class="relative flex items-center px-2">
                            <div class="h-5 w-5 -ml-2.5 shrink-0 rounded-full bg-neutral-100 dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800"></div>
                            <div class="flex-1 border-t border-dashed border-neutral-200 dark:border-neutral-700"></div>
                            <div class="h-5 w-5 -mr-2.5 shrink-0 rounded-full bg-neutral-100 dark:bg-neutral-950 border border-neutral-200 dark:border-neutral-800"></div>
                        </div>

                        {{-- Ticket body --}}
                        <div class="px-6 py-5 space-y-4">

                            {{-- QR Code --}}
                            <div class="flex flex-col items-center gap-2">
                                <div class="rounded-2xl border-2 border-neutral-100 dark:border-neutral-800 bg-white p-3 shadow-sm">
                                    <img
                                        src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode('BOOKLOCK:' . $bookingRef . ':' . implode(',', $selectedSeats)) }}&format=png&margin=0"
                                        alt="Ticket QR Code"
                                        class="h-40 w-40 rounded-lg"
                                    />
                                </div>
                                <p class="text-[11px] text-neutral-400 dark:text-neutral-500">Scan this at the cinema entrance</p>
                            </div>

                            {{-- Details --}}
                            <div class="rounded-2xl bg-neutral-50 dark:bg-neutral-800 p-4 space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-neutral-500 dark:text-neutral-400">Movie</span>
                                    <span class="font-semibold text-neutral-900 dark:text-white text-right max-w-[60%] truncate">{{ $this->movie['title'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-neutral-500 dark:text-neutral-400">Theater</span>
                                    <span class="font-semibold text-neutral-900 dark:text-white text-right max-w-[60%] truncate">{{ collect($theaters)->firstWhere('id', $selectedTheater)['name'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-neutral-500 dark:text-neutral-400">Showtime</span>
                                    <span class="font-semibold text-neutral-900 dark:text-white">{{ $dates[$selectedDate]['short'] }} · {{ $selectedTime }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-neutral-500 dark:text-neutral-400">Seats</span>
                                    <span class="font-semibold text-neutral-900 dark:text-white">{{ implode(', ', $selectedSeats) }}</span>
                                </div>
                            </div>

                            {{-- Action buttons --}}
                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <button
                                    type="button"
                                    onclick="downloadTicket()"
                                    class="flex items-center justify-center gap-1.5 rounded-full border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 py-2.5 text-sm font-semibold text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-700 transition"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                        <path fill-rule="evenodd" d="M4.5 2A1.5 1.5 0 0 0 3 3.5v13A1.5 1.5 0 0 0 4.5 18h11a1.5 1.5 0 0 0 1.5-1.5V7.621a1.5 1.5 0 0 0-.44-1.06l-4.12-4.122A1.5 1.5 0 0 0 11.378 2H4.5Zm2.25 8.5a.75.75 0 0 0 0 1.5h6.5a.75.75 0 0 0 0-1.5h-6.5Zm0 3a.75.75 0 0 0 0 1.5h6.5a.75.75 0 0 0 0-1.5h-6.5Z" clip-rule="evenodd" />
                                    </svg>
                                    Download
                                </button>
                                <a
                                    href="{{ route('tickets') }}"
                                    wire:navigate
                                    class="flex items-center justify-center gap-1.5 rounded-full bg-neutral-900 dark:bg-white py-2.5 text-sm font-semibold text-white dark:text-neutral-900 hover:bg-neutral-700 dark:hover:bg-neutral-200 transition"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                        <path fill-rule="evenodd" d="M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a3 3 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" clip-rule="evenodd" />
                                    </svg>
                                    My Tickets
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════
         TICKET DOWNLOAD + PAYSTACK
    ══════════════════════════════════════ --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('launch-paystack', (data) => {
                const payload = Array.isArray(data) ? data[0] : data;

                const handler = PaystackPop.setup({
                    key: 'pk_test_efce88f96e211ca13a45fc0338646da1b6c5a736',
                    email: payload.email || 'customer@example.com',
                    amount: Math.round(payload.amount * 100),
                    // currency defaults to your Paystack account currency (NGN for test accounts)
                    ref: payload.reference,
                    metadata: { movie: payload.movie },
                    callback: function (response) {
                        // Payment successful — tell the Livewire component
                        // (response.reference is the Paystack transaction reference)
                        @this.call('paymentSuccessful');
                    },
                    onClose: function () {
                        // User dismissed the popup — do nothing
                    },
                });

                handler.openIframe();
            });
        });

        function downloadTicket() {
            const card = document.getElementById('ticket-card');
            if (!card) return;

            // Temporarily force a white background so the PNG looks clean on any OS
            const originalBg = card.style.background;
            card.style.background = '#ffffff';

            html2canvas(card, {
                scale: 3,           // high-DPI / retina quality
                useCORS: true,      // needed so the QR code image from qrserver.com is captured
                backgroundColor: '#ffffff',
            }).then(function (canvas) {
                card.style.background = originalBg;

                const link = document.createElement('a');
                link.download = 'booklock-ticket.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            }).catch(function () {
                card.style.background = originalBg;
            });
        }
    </script>

</div>
