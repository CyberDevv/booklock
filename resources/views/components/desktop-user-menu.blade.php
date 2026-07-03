@php
    // Mock user data for UI development — replace with auth()->user() calls when auth is wired up
    $mockUser = (object) [
        'name'    => 'Ibrahim Malik',
        'email'   => 'ibrahim@booklock.dev',
    ];
    $user = auth()->check() ? auth()->user() : $mockUser;

    // initials() helper — replicate what the User model provides
    if (! method_exists($user, 'initials')) {
        $user->initials = collect(explode(' ', $user->name))
            ->map(fn ($word) => strtoupper($word[0]))
            ->implode('');
    } else {
        $user->initials = $user->initials();
    }
@endphp

<flux:dropdown position="bottom" align="start">
    <flux:sidebar.profile
        :name="$user->name"
        :initials="$user->initials"
        icon:trailing="chevrons-up-down"
        data-test="sidebar-menu-button"
    />

    <flux:menu>
        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
            <flux:avatar
                :name="$user->name"
                :initials="$user->initials"
            />
            <div class="grid flex-1 text-start text-sm leading-tight">
                <flux:heading class="truncate">{{ $user->name }}</flux:heading>
                <flux:text class="truncate">{{ $user->email }}</flux:text>
            </div>
        </div>
        <flux:menu.separator />
        <flux:menu.radio.group>
            <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                Settings
            </flux:menu.item>
            @auth
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:menu.item
                    as="button"
                    type="submit"
                    icon="arrow-right-start-on-rectangle"
                    class="w-full cursor-pointer"
                    data-test="logout-button"
                >
                    Log out
                </flux:menu.item>
            </form>
            @endauth
        </flux:menu.radio.group>
    </flux:menu>
</flux:dropdown>
