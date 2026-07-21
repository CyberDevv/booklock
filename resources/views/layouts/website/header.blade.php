<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white text-neutral-900 dark:bg-zinc-900 dark:text-neutral-100">
    <flux:header container>
        <x-app-logo href="{{ route('home') }}" class="[&>div:last-child]:hidden sm:[&>div:last-child]:block"
            wire:navigate />

        <flux:spacer />

        @if (Route::has('login'))
            <nav class="flex items-center justify-end gap-1.5 sm:gap-2 lg:gap-3">
                <div class="flex items-center gap-0.5 sm:gap-1 rounded-full border border-neutral-300/70 bg-white/80 p-0.5 sm:p-1 shadow-sm backdrop-blur dark:border-neutral-700 dark:bg-neutral-900/80"
                    x-data="{
                        theme: localStorage.getItem('theme') || 'system',
                        setTheme(val) {
                            this.theme = val;
                            localStorage.setItem('theme', val);
                            this.apply(val);
                        },
                        apply(val) {
                            if (val === 'dark' || (val === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                                document.documentElement.classList.add('dark');
                            } else {
                                document.documentElement.classList.remove('dark');
                            }
                        }
                    }" x-init="window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                        if (theme === 'system') apply('system');
                    });">
                    <!-- Light Theme -->
                    <button type="button" @click="setTheme('light')"
                        :class="theme === 'light' ?
                            'bg-neutral-200/60 text-neutral-900 dark:bg-neutral-800 dark:text-neutral-100' :
                            'text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300'"
                        class="rounded-full p-1 sm:p-1.5 transition-all duration-200 focus:outline-none"
                        title="Light theme">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 3v1.5M12 19.5V21M4.22 4.22l1.06 1.06M17.72 17.72l1.06 1.06M3 12h1.5M19.5 12H21M4.22 19.78l1.06-1.06M17.72 6.28l1.06-1.06M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </button>

                    <!-- Dark Theme -->
                    <button type="button" @click="setTheme('dark')"
                        :class="theme === 'dark' ?
                            'bg-neutral-200/60 text-neutral-900 dark:bg-neutral-800 dark:text-neutral-100' :
                            'text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300'"
                        class="rounded-full p-1 sm:p-1.5 transition-all duration-200 focus:outline-none"
                        title="Dark theme">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                        </svg>
                    </button>

                    <!-- System Theme -->
                    <button type="button" @click="setTheme('system')"
                        :class="theme === 'system' ?
                            'bg-neutral-200/60 text-neutral-900 dark:bg-neutral-800 dark:text-neutral-100' :
                            'text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300'"
                        class="rounded-full p-1 sm:p-1.5 transition-all duration-200 focus:outline-none"
                        title="System theme">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />
                        </svg>
                    </button>
                </div>

                @auth
                    <a href="{{ route('tickets') }}"
                        class="inline-block px-2.5 sm:px-3 lg:px-5 py-1 sm:py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-xs lg:text-sm leading-normal">
                        My Ticket
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="inline-block h-auto px-2.5 sm:px-3 lg:px-5 py-1 sm:py-1.5 text-[#EDEDEC] bg-red-600 hover:bg-red-800 rounded-sm text-xs lg:text-sm leading-normal"
                            data-test="logout-button">
                            Log out
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                        class="inline-block px-2.5 sm:px-3 lg:px-5 py-1 sm:py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-xs lg:text-sm leading-normal">
                        Log in
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="hidden sm:inline-block px-2.5 sm:px-3 lg:px-5 py-1 sm:py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-xs lg:text-sm leading-normal">
                            Register
                        </a>
                    @endif
                @endauth
            </nav>
        @endif
    </flux:header>

    {{ $slot }}

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>
