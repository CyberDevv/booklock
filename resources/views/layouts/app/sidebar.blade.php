<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">

    @php
        // Mock user data for UI development — replace when auth is wired up
        $mockUser = (object) [
            'name' => 'Ibrahim Malik',
            'email' => 'ibrahim@booklock.dev',
        ];
        $user = auth()->check() ? auth()->user() : $mockUser;

        $initials = auth()->check()
            ? auth()->user()->initials()
            : collect(explode(' ', $user->name))
                ->map(fn($w) => strtoupper($w[0]))
                ->implode('');
    @endphp

    <flux:sidebar sticky collapsible="mobile"
        class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            {{-- Overview --}}
            <flux:sidebar.group heading="Overview" class="grid">
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>
                    Dashboard
                </flux:sidebar.item>
            </flux:sidebar.group>

            {{-- Management --}}
            <flux:sidebar.group heading="Management" class="grid">
                <flux:sidebar.item icon="ticket" :href="route('admin.tickets')" :current="request()->routeIs('admin.tickets')"
                    wire:navigate>
                    Tickets
                </flux:sidebar.item>

                <flux:sidebar.item icon="film" :href="route('movies.index')" :current="request()->routeIs('movies.*')"
                    wire:navigate>
                    Movies & Shows
                </flux:sidebar.item>

                <flux:sidebar.item icon="calendar-days" :href="route('admin.bookings')" :current="request()->routeIs('admin.bookings')"
                    wire:navigate>
                    Bookings
                </flux:sidebar.item>

                <flux:sidebar.item icon="users" :href="route('admin.users')" :current="request()->routeIs('admin.users')" wire:navigate>
                    Users
                </flux:sidebar.item>
            </flux:sidebar.group>

            {{-- Analytics --}}
            <flux:sidebar.group heading="Analytics" class="grid">
                <flux:sidebar.item icon="chart-bar" :href="route('admin.reports')" :current="request()->routeIs('admin.reports')"
                    wire:navigate>
                    Reports
                </flux:sidebar.item>

                <flux:sidebar.item icon="banknotes" :href="route('admin.revenue')" :current="request()->routeIs('admin.revenue')"
                    wire:navigate>
                    Revenue
                </flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:spacer />

        {{-- Bottom nav --}}
        <flux:sidebar.nav>
            <flux:sidebar.item icon="cog-6-tooth" :href="route('profile.edit')"
                :current="request()->routeIs('profile.*')" wire:navigate>
                Settings
            </flux:sidebar.item>
        </flux:sidebar.nav>

        <x-desktop-user-menu class="hidden lg:block" />
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="$initials" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <flux:avatar :name="$user->name" :initials="$initials" />
                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <flux:heading class="truncate">{{ $user->name }}</flux:heading>
                                <flux:text class="truncate">{{ $user->email }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        Settings
                    </flux:menu.item>
                </flux:menu.radio.group>

                @auth
                    <flux:menu.separator />
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer" data-test="logout-button">
                            Log out
                        </flux:menu.item>
                    </form>
                @endauth
            </flux:menu>
        </flux:dropdown>
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
