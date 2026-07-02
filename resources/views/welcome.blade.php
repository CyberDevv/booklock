<x-layouts::website>
    <div class="grid gap-14 lg:gap-20">
        <section class="overflow-hidden rounded-[1.75rem] border border-neutral-200 bg-neutral-50/90 shadow-xl dark:border-neutral-800 dark:bg-neutral-900/90 dark:shadow-[0_20px_50px_-20px_rgba(0,0,0,0.9)]">
            <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr] lg:gap-8">
                <div class="p-6 sm:p-8 lg:p-10">
                    <div class="inline-flex items-center rounded-full border border-neutral-300 bg-neutral-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.28em] text-neutral-600 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300">
                        Book your next show
                    </div>
                    <h1 class="mt-4 text-3xl font-semibold leading-tight text-neutral-900 dark:text-white sm:mt-5 sm:text-4xl lg:text-5xl">
                        Find your next cinematic escape.
                    </h1>
                    <p class="mt-3 max-w-xl text-sm leading-7 text-neutral-600 dark:text-neutral-400 sm:mt-4 sm:text-base lg:text-lg">
                        Discover current releases, premium seating, and a smooth ticketing experience built for effortless movie nights.
                    </p>

                    <div class="mt-6 flex flex-col gap-3 sm:mt-8 sm:flex-row">
                        <label class="flex-1">
                            <span class="sr-only">Search for a movie</span>
                            <input
                                type="text"
                                placeholder="Search movies, genres, or theaters"
                                class="w-full rounded-full border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 placeholder:text-neutral-400 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 dark:placeholder:text-neutral-500"
                            />
                        </label>
                        <button class="rounded-full bg-neutral-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-neutral-800 dark:bg-white dark:text-neutral-950 dark:hover:bg-neutral-200">
                            Search
                        </button>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-2 sm:mt-8">
                        <span class="rounded-full border border-neutral-200 bg-neutral-100 px-3 py-1 text-sm text-neutral-700 dark:border-neutral-800 dark:bg-neutral-800/80 dark:text-neutral-300">Trending</span>
                        <span class="rounded-full border border-neutral-200 bg-neutral-100 px-3 py-1 text-sm text-neutral-700 dark:border-neutral-800 dark:bg-neutral-800/80 dark:text-neutral-300">IMAX</span>
                        <span class="rounded-full border border-neutral-200 bg-neutral-100 px-3 py-1 text-sm text-neutral-700 dark:border-neutral-800 dark:bg-neutral-800/80 dark:text-neutral-300">Family Night</span>
                    </div>
                </div>

                <div class="relative min-h-70 overflow-hidden border-t border-neutral-200 bg-neutral-50/80 sm:min-h-80 lg:border-s lg:border-t-0 dark:border-neutral-800 dark:bg-neutral-900/80">
                    <img src="https://images.unsplash.com/photo-1517602302552-471fe67acf66?auto=format&fit=crop&w=1200&q=80" alt="Featured movie poster" class="absolute inset-0 h-full w-full object-cover" />
                    <div class="absolute inset-0 bg-linear-to-t from-neutral-950 via-neutral-950/35 to-transparent"></div>
                    <div class="relative flex h-full items-end p-6 sm:p-8 lg:p-10">
                        <div class="max-w-[18rem]">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-neutral-200">Tonight's pick</p>
                            <h2 class="mt-2 text-xl font-semibold text-white sm:text-2xl">Midnight Skyline</h2>
                            <p class="mt-2 text-sm text-neutral-200">8:30 PM • IMAX • Dolby Atmos</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-neutral-500 dark:text-neutral-400">Now Showing</p>
                    <h2 class="mt-1 text-xl font-semibold text-neutral-900 dark:text-white sm:text-2xl">Popular this week</h2>
                </div>
                <a href="#" class="text-sm font-medium text-neutral-500 transition hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-white">View all</a>
            </div>

            <div class="mt-5 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                <article class="overflow-hidden rounded-[1.25rem] border border-neutral-200 bg-neutral-50/80 transition hover:border-neutral-300 dark:border-neutral-800 dark:bg-neutral-900/80 dark:hover:border-neutral-700">
                    <img src="https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=900&q=80" alt="Neon Horizon movie poster" class="h-40 w-full object-cover" />
                    <div class="p-5">
                        <div class="flex items-center justify-between gap-3">
                            <h3 class="text-base font-semibold text-neutral-900 dark:text-white">Neon Horizon</h3>
                            <span class="rounded-full bg-neutral-200 px-2.5 py-1 text-xs font-medium text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300">4.8</span>
                        </div>
                        <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">Sci-Fi • 2h 08m</p>
                        <a href="{{ route('booking', ['movie' => 'neon-horizon']) }}" class="mt-4 flex w-full items-center justify-center gap-2 rounded-full bg-neutral-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-neutral-700 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200">
                            Get Tickets
                        </a>
                    </div>
                </article>

                <article class="overflow-hidden rounded-[1.25rem] border border-neutral-200 bg-neutral-50/80 transition hover:border-neutral-300 dark:border-neutral-800 dark:bg-neutral-900/80 dark:hover:border-neutral-700">
                    <img src="https://images.unsplash.com/photo-1516280440614-37939bbacd81?auto=format&fit=crop&w=900&q=80" alt="Moonlight Run movie poster" class="h-40 w-full object-cover" />
                    <div class="p-5">
                        <div class="flex items-center justify-between gap-3">
                            <h3 class="text-base font-semibold text-neutral-900 dark:text-white">Moonlight Run</h3>
                            <span class="rounded-full bg-neutral-200 px-2.5 py-1 text-xs font-medium text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300">4.6</span>
                        </div>
                        <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">Thriller • 1h 54m</p>
                        <a href="{{ route('booking', ['movie' => 'moonlight-run']) }}" class="mt-4 flex w-full items-center justify-center gap-2 rounded-full bg-neutral-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-neutral-700 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200">
                            Get Tickets
                        </a>
                    </div>
                </article>

                <article class="overflow-hidden rounded-[1.25rem] border border-neutral-200 bg-neutral-50/80 transition hover:border-neutral-300 dark:border-neutral-800 dark:bg-neutral-900/80 dark:hover:border-neutral-700">
                    <img src="https://images.unsplash.com/photo-1478720568477-152d9b164e26?auto=format&fit=crop&w=900&q=80" alt="Golden Hour movie poster" class="h-40 w-full object-cover" />
                    <div class="p-5">
                        <div class="flex items-center justify-between gap-3">
                            <h3 class="text-base font-semibold text-neutral-900 dark:text-white">Golden Hour</h3>
                            <span class="rounded-full bg-neutral-200 px-2.5 py-1 text-xs font-medium text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300">4.9</span>
                        </div>
                        <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">Drama • 2h 12m</p>
                        <a href="{{ route('booking', ['movie' => 'golden-hour']) }}" class="mt-4 flex w-full items-center justify-center gap-2 rounded-full bg-neutral-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-neutral-700 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200">
                            Get Tickets
                        </a>
                    </div>
                </article>
            </div>
        </section>

        <section>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-neutral-500 dark:text-neutral-400">Upcoming</p>
                    <h2 class="mt-1 text-xl font-semibold text-neutral-900 dark:text-white sm:text-2xl">Coming soon</h2>
                </div>
            </div>

            <div class="mt-5 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                <article class="flex overflow-hidden rounded-[1.25rem] border border-neutral-200 bg-neutral-50/70 dark:border-neutral-800 dark:bg-neutral-900/70">
                    <img src="https://images.unsplash.com/photo-1509281373149-e957c6296406?auto=format&fit=crop&w=400&q=80" alt="Shadow Parade movie poster" class="h-28 w-20 object-cover shrink-0" />
                    <div class="flex-1 min-w-0 p-4 flex flex-col justify-center">
                        <p class="text-xs font-semibold text-neutral-600 dark:text-neutral-300">July 25</p>
                        <h3 class="mt-1 text-sm font-semibold text-neutral-900 dark:text-white truncate">Shadow Parade</h3>
                        <p class="mt-1 text-xs leading-relaxed text-neutral-500 dark:text-neutral-400 line-clamp-2">A citywide mystery with a brilliant detective and a hidden past.</p>
                    </div>
                </article>

                <article class="flex overflow-hidden rounded-[1.25rem] border border-neutral-200 bg-neutral-50/70 dark:border-neutral-800 dark:bg-neutral-900/70">
                    <img src="https://images.unsplash.com/photo-1547127796-06bb04e4b315?auto=format&fit=crop&w=400&q=80" alt="The Last Lantern movie poster" class="h-28 w-20 object-cover shrink-0" />
                    <div class="flex-1 min-w-0 p-4 flex flex-col justify-center">
                        <p class="text-xs font-semibold text-neutral-600 dark:text-neutral-300">August 1</p>
                        <h3 class="mt-1 text-sm font-semibold text-neutral-900 dark:text-white truncate">The Last Lantern</h3>
                        <p class="mt-1 text-xs leading-relaxed text-neutral-500 dark:text-neutral-400 line-clamp-2">An emotional fantasy that follows a lost traveler through the desert.</p>
                    </div>
                </article>

                <article class="flex overflow-hidden rounded-[1.25rem] border border-neutral-200 bg-neutral-50/70 dark:border-neutral-800 dark:bg-neutral-900/70">
                    <img src="https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=400&q=80" alt="Signal 9 movie poster" class="h-28 w-20 object-cover shrink-0" />
                    <div class="flex-1 min-w-0 p-4 flex flex-col justify-center">
                        <p class="text-xs font-semibold text-neutral-600 dark:text-neutral-300">August 15</p>
                        <h3 class="mt-1 text-sm font-semibold text-neutral-900 dark:text-white truncate">Signal 9</h3>
                        <p class="mt-1 text-xs leading-relaxed text-neutral-500 dark:text-neutral-400 line-clamp-2">A high-stakes thriller about a signal from deep space.</p>
                    </div>
                </article>
            </div>
        </section>
    </div>
</x-layouts::website>
