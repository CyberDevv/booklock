<x-layouts::website :title="__('My Tickets')">
    <div class="grid gap-8 lg:gap-10" x-data="{ tab: 'active' }">

        {{-- Page Header --}}
        <div class="flex flex-col gap-1">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-neutral-500 dark:text-neutral-400">Your Bookings</p>
            <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white sm:text-3xl">My Tickets</h1>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">View all your upcoming shows and ticket history.</p>
        </div>

        {{-- Tabs --}}
        <div>
            <div class="flex gap-1 rounded-[1rem] border border-neutral-200 bg-neutral-100/80 p-1 dark:border-neutral-800 dark:bg-neutral-900/60 sm:w-fit">
                <button
                    type="button"
                    @click="tab = 'active'"
                    :class="tab === 'active'
                        ? 'bg-white text-neutral-900 shadow-sm dark:bg-neutral-800 dark:text-white'
                        : 'text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200'"
                    class="flex items-center gap-2 rounded-[0.65rem] px-4 py-2 text-sm font-medium transition-all duration-200 focus:outline-none"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a3 3 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                    </svg>
                    Active Tickets
                    <span
                        :class="tab === 'active' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-400' : 'bg-neutral-200 text-neutral-600 dark:bg-neutral-700 dark:text-neutral-400'"
                        class="rounded-full px-2 py-0.5 text-xs font-semibold transition-colors"
                    >2</span>
                </button>

                <button
                    type="button"
                    @click="tab = 'history'"
                    :class="tab === 'history'
                        ? 'bg-white text-neutral-900 shadow-sm dark:bg-neutral-800 dark:text-white'
                        : 'text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200'"
                    class="flex items-center gap-2 rounded-[0.65rem] px-4 py-2 text-sm font-medium transition-all duration-200 focus:outline-none"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    History
                    <span
                        :class="tab === 'history' ? 'bg-neutral-200 text-neutral-700 dark:bg-neutral-700 dark:text-neutral-300' : 'bg-neutral-200 text-neutral-600 dark:bg-neutral-700 dark:text-neutral-400'"
                        class="rounded-full px-2 py-0.5 text-xs font-semibold transition-colors"
                    >4</span>
                </button>
            </div>

            {{-- Active Tickets Panel --}}
            <div x-show="tab === 'active'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="mt-6 grid gap-4 sm:grid-cols-2">

                {{-- Active Ticket 1 --}}
                <article class="overflow-hidden rounded-[1.25rem] border border-neutral-200 bg-neutral-50/80 dark:border-neutral-800 dark:bg-neutral-900/80">
                    <div class="relative h-36 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=900&q=80" alt="Neon Horizon" class="h-full w-full object-cover" />
                        <div class="absolute inset-0 bg-gradient-to-t from-neutral-950/80 to-transparent"></div>
                        <div class="absolute bottom-3 left-4 right-4 flex items-end justify-between">
                            <h3 class="text-base font-semibold text-white">Neon Horizon</h3>
                            <span class="rounded-full bg-emerald-500 px-2.5 py-0.5 text-xs font-semibold text-white">Active</span>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Date</p>
                                <p class="mt-0.5 font-medium text-neutral-900 dark:text-white">July 15, 2026</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Time</p>
                                <p class="mt-0.5 font-medium text-neutral-900 dark:text-white">7:15 PM</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Seat</p>
                                <p class="mt-0.5 font-medium text-neutral-900 dark:text-white">Row D • Seat 14</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Format</p>
                                <p class="mt-0.5 font-medium text-neutral-900 dark:text-white">IMAX</p>
                            </div>
                        </div>

                        <div class="relative my-4 flex items-center">
                            <div class="h-5 w-5 -ml-7 shrink-0 rounded-full bg-white dark:bg-zinc-900 border border-neutral-200 dark:border-neutral-700"></div>
                            <div class="flex-1 border-t border-dashed border-neutral-300 dark:border-neutral-700"></div>
                            <div class="h-5 w-5 -mr-7 shrink-0 rounded-full bg-white dark:bg-zinc-900 border border-neutral-200 dark:border-neutral-700"></div>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Booking Ref</p>
                                <p class="mt-0.5 font-mono text-sm font-semibold tracking-wider text-neutral-900 dark:text-white">BKL-29471</p>
                            </div>
                            <button class="rounded-full bg-neutral-900 px-4 py-1.5 text-xs font-semibold text-white transition hover:bg-neutral-700 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200">
                                View QR
                            </button>
                        </div>
                    </div>
                </article>

                {{-- Active Ticket 2 --}}
                <article class="overflow-hidden rounded-[1.25rem] border border-neutral-200 bg-neutral-50/80 dark:border-neutral-800 dark:bg-neutral-900/80">
                    <div class="relative h-36 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1516280440614-37939bbacd81?auto=format&fit=crop&w=900&q=80" alt="Moonlight Run" class="h-full w-full object-cover" />
                        <div class="absolute inset-0 bg-gradient-to-t from-neutral-950/80 to-transparent"></div>
                        <div class="absolute bottom-3 left-4 right-4 flex items-end justify-between">
                            <h3 class="text-base font-semibold text-white">Moonlight Run</h3>
                            <span class="rounded-full bg-emerald-500 px-2.5 py-0.5 text-xs font-semibold text-white">Active</span>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Date</p>
                                <p class="mt-0.5 font-medium text-neutral-900 dark:text-white">July 20, 2026</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Time</p>
                                <p class="mt-0.5 font-medium text-neutral-900 dark:text-white">9:30 PM</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Seat</p>
                                <p class="mt-0.5 font-medium text-neutral-900 dark:text-white">Row B • Seat 7</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Format</p>
                                <p class="mt-0.5 font-medium text-neutral-900 dark:text-white">Dolby Atmos</p>
                            </div>
                        </div>

                        <div class="relative my-4 flex items-center">
                            <div class="h-5 w-5 -ml-7 shrink-0 rounded-full bg-white dark:bg-zinc-900 border border-neutral-200 dark:border-neutral-700"></div>
                            <div class="flex-1 border-t border-dashed border-neutral-300 dark:border-neutral-700"></div>
                            <div class="h-5 w-5 -mr-7 shrink-0 rounded-full bg-white dark:bg-zinc-900 border border-neutral-200 dark:border-neutral-700"></div>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-neutral-500 dark:text-neutral-400">Booking Ref</p>
                                <p class="mt-0.5 font-mono text-sm font-semibold tracking-wider text-neutral-900 dark:text-white">BKL-38812</p>
                            </div>
                            <button class="rounded-full bg-neutral-900 px-4 py-1.5 text-xs font-semibold text-white transition hover:bg-neutral-700 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200">
                                View QR
                            </button>
                        </div>
                    </div>
                </article>

            </div>

            {{-- Ticket History Panel --}}
            <div x-show="tab === 'history'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="mt-6 overflow-hidden rounded-[1.25rem] border border-neutral-200 dark:border-neutral-800">

                {{-- History Item 1 --}}
                <div class="flex items-center gap-4 border-b border-neutral-200 bg-neutral-50/60 p-4 dark:border-neutral-800 dark:bg-neutral-900/60">
                    <img src="https://images.unsplash.com/photo-1478720568477-152d9b164e26?auto=format&fit=crop&w=200&q=80" alt="Golden Hour" class="h-16 w-12 rounded-lg object-cover shrink-0" />
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <h3 class="truncate text-sm font-semibold text-neutral-900 dark:text-white">Golden Hour</h3>
                                <p class="mt-0.5 text-xs text-neutral-500 dark:text-neutral-400">June 28, 2026 • 6:45 PM • Row F, Seat 3</p>
                            </div>
                            <span class="shrink-0 rounded-full bg-neutral-200 px-2.5 py-0.5 text-xs font-medium text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400">Used</span>
                        </div>
                        <div class="mt-2 flex items-center gap-3">
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">Drama • 2h 12m</span>
                            <span class="text-neutral-300 dark:text-neutral-700">·</span>
                            <span class="font-mono text-xs font-medium text-neutral-500 dark:text-neutral-400">BKL-11203</span>
                        </div>
                    </div>
                </div>

                {{-- History Item 2 --}}
                <div class="flex items-center gap-4 border-b border-neutral-200 bg-neutral-50/60 p-4 dark:border-neutral-800 dark:bg-neutral-900/60">
                    <img src="https://images.unsplash.com/photo-1509281373149-e957c6296406?auto=format&fit=crop&w=200&q=80" alt="Shadow Parade" class="h-16 w-12 rounded-lg object-cover shrink-0" />
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <h3 class="truncate text-sm font-semibold text-neutral-900 dark:text-white">Shadow Parade</h3>
                                <p class="mt-0.5 text-xs text-neutral-500 dark:text-neutral-400">June 14, 2026 • 8:00 PM • Row A, Seat 22</p>
                            </div>
                            <span class="shrink-0 rounded-full bg-neutral-200 px-2.5 py-0.5 text-xs font-medium text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400">Used</span>
                        </div>
                        <div class="mt-2 flex items-center gap-3">
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">Mystery • 1h 58m</span>
                            <span class="text-neutral-300 dark:text-neutral-700">·</span>
                            <span class="font-mono text-xs font-medium text-neutral-500 dark:text-neutral-400">BKL-09774</span>
                        </div>
                    </div>
                </div>

                {{-- History Item 3 --}}
                <div class="flex items-center gap-4 border-b border-neutral-200 bg-neutral-50/60 p-4 dark:border-neutral-800 dark:bg-neutral-900/60">
                    <img src="https://images.unsplash.com/photo-1547127796-06bb04e4b315?auto=format&fit=crop&w=200&q=80" alt="The Last Lantern" class="h-16 w-12 rounded-lg object-cover shrink-0" />
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <h3 class="truncate text-sm font-semibold text-neutral-900 dark:text-white">The Last Lantern</h3>
                                <p class="mt-0.5 text-xs text-neutral-500 dark:text-neutral-400">May 30, 2026 • 5:30 PM • Row C, Seat 11</p>
                            </div>
                            <span class="shrink-0 rounded-full bg-neutral-200 px-2.5 py-0.5 text-xs font-medium text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400">Used</span>
                        </div>
                        <div class="mt-2 flex items-center gap-3">
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">Fantasy • 2h 05m</span>
                            <span class="text-neutral-300 dark:text-neutral-700">·</span>
                            <span class="font-mono text-xs font-medium text-neutral-500 dark:text-neutral-400">BKL-07654</span>
                        </div>
                    </div>
                </div>

                {{-- History Item 4 --}}
                <div class="flex items-center gap-4 bg-neutral-50/60 p-4 dark:bg-neutral-900/60">
                    <img src="https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=200&q=80" alt="Signal 9" class="h-16 w-12 rounded-lg object-cover shrink-0" />
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <h3 class="truncate text-sm font-semibold text-neutral-900 dark:text-white">Signal 9</h3>
                                <p class="mt-0.5 text-xs text-neutral-500 dark:text-neutral-400">May 10, 2026 • 9:00 PM • Row G, Seat 5</p>
                            </div>
                            <span class="shrink-0 rounded-full bg-neutral-200 px-2.5 py-0.5 text-xs font-medium text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400">Used</span>
                        </div>
                        <div class="mt-2 flex items-center gap-3">
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">Thriller • 2h 20m</span>
                            <span class="text-neutral-300 dark:text-neutral-700">·</span>
                            <span class="font-mono text-xs font-medium text-neutral-500 dark:text-neutral-400">BKL-05310</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</x-layouts::website>


        {{-- Page Header --}}
