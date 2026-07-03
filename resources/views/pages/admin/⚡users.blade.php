<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

new #[Title('Manage Users'), Layout('layouts.app')] class extends Component {
    public string $search = '';
    public string $filterRole = '';
    public string $filterStatus = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';

    public bool $showEditModal = false;
    public bool $showSuspendModal = false;
    public ?array $selectedUser = null;
    public ?int $editingId = null;
    public ?int $suspendingId = null;

    // Form fields
    public string $form_name   = '';
    public string $form_email  = '';
    public string $form_role   = 'customer';
    public string $form_status = 'active';

    public array $users = [
        [
            'id'             => 1,
            'name'           => 'Ibrahim Malik',
            'email'          => 'ibrahim@booklock.dev',
            'role'           => 'admin',
            'status'         => 'active',
            'bookings_count' => 12,
            'created_at'     => '2026-05-01 09:00:00',
        ],
        [
            'id'             => 2,
            'name'           => 'John Doe',
            'email'          => 'john@example.com',
            'role'           => 'customer',
            'status'         => 'active',
            'bookings_count' => 4,
            'created_at'     => '2026-07-01 10:30:00',
        ],
        [
            'id'             => 3,
            'name'           => 'Jane Smith',
            'email'          => 'jane@example.com',
            'role'           => 'customer',
            'status'         => 'active',
            'bookings_count' => 8,
            'created_at'     => '2026-07-01 14:15:00',
        ],
        [
            'id'             => 4,
            'name'           => 'Alex Johnson',
            'email'          => 'alex@example.com',
            'role'           => 'customer',
            'status'         => 'active',
            'bookings_count' => 1,
            'created_at'     => '2026-07-02 09:00:00',
        ],
        [
            'id'             => 5,
            'name'           => 'Sarah Connor',
            'email'          => 'sarah@example.com',
            'role'           => 'customer',
            'status'         => 'suspended',
            'bookings_count' => 2,
            'created_at'     => '2026-06-30 18:22:00',
        ],
        [
            'id'             => 6,
            'name'           => 'Michael Scott',
            'email'          => 'michael@dundermifflin.com',
            'role'           => 'customer',
            'status'         => 'active',
            'bookings_count' => 5,
            'created_at'     => '2026-07-02 11:45:00',
        ],
        [
            'id'             => 7,
            'name'           => 'Pam Beesly',
            'email'          => 'pam@dundermifflin.com',
            'role'           => 'customer',
            'status'         => 'active',
            'bookings_count' => 3,
            'created_at'     => '2026-07-02 12:10:00',
        ],
        [
            'id'             => 8,
            'name'           => 'Jim Halpert',
            'email'          => 'jim@dundermifflin.com',
            'role'           => 'customer',
            'status'         => 'active',
            'bookings_count' => 0,
            'created_at'     => '2026-07-02 08:30:00',
        ],
    ];

    #[Computed]
    public function filteredUsers(): array
    {
        return collect($this->users)
            ->when($this->search, fn($c) => $c->filter(
                fn($u) => str_contains(strtolower($u['name']), strtolower($this->search))
                       || str_contains(strtolower($u['email']), strtolower($this->search))
            ))
            ->when($this->filterRole,   fn($c) => $c->where('role', $this->filterRole))
            ->when($this->filterStatus, fn($c) => $c->where('status', $this->filterStatus))
            ->sortBy($this->sortBy, SORT_REGULAR, $this->sortDir === 'desc')
            ->values()
            ->toArray();
    }

    #[Computed]
    public function stats(): array
    {
        $all = collect($this->users);
        return [
            'total'     => $all->count(),
            'admins'    => $all->where('role', 'admin')->count(),
            'active'    => $all->where('status', 'active')->count(),
            'suspended' => $all->where('status', 'suspended')->count(),
            'bookings'  => $all->sum('bookings_count'),
        ];
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

    public function openEdit(int $id): void
    {
        $user = collect($this->users)->firstWhere('id', $id);
        if (!$user) return;

        $this->editingId   = $id;
        $this->form_name   = $user['name'];
        $this->form_email  = $user['email'];
        $this->form_role   = $user['role'];
        $this->form_status = $user['status'];
        $this->showEditModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'form_name'   => 'required|min:2',
            'form_email'  => 'required|email',
            'form_role'   => 'required',
            'form_status' => 'required',
        ]);

        $this->users = collect($this->users)->map(function ($u) {
            if ($u['id'] === $this->editingId) {
                return array_merge($u, [
                    'name'   => $this->form_name,
                    'email'  => $this->form_email,
                    'role'   => $this->form_role,
                    'status' => $this->form_status,
                ]);
            }
            return $u;
        })->toArray();

        $this->showEditModal = false;
        $this->editingId     = null;

        Flux::toast('User profile information has been saved.', heading: 'Profile Updated', variant: 'success');
    }

    public function confirmSuspend(int $id): void
    {
        $this->suspendingId    = $id;
        $this->showSuspendModal = true;
    }

    public function toggleStatus(): void
    {
        $target = collect($this->users)->firstWhere('id', $this->suspendingId);
        $isSuspending = $target && $target['status'] === 'active';

        $this->users = collect($this->users)->map(function ($u) {
            if ($u['id'] === $this->suspendingId) {
                $u['status'] = $u['status'] === 'active' ? 'suspended' : 'active';
            }
            return $u;
        })->toArray();

        $this->showSuspendModal = false;
        $this->suspendingId     = null;

        Flux::toast(
            $isSuspending
                ? 'The account has been suspended. The user can no longer log in.'
                : 'The account has been reactivated and login access restored.',
            heading: $isSuspending ? 'Account Suspended' : 'Account Activated',
            variant: $isSuspending ? 'danger' : 'success'
        );
    }

    public function getInitials(string $name): string
    {
        return collect(explode(' ', $name))
            ->map(fn($w) => strtoupper($w[0] ?? ''))
            ->implode('');
    }
};
?>

<div class="flex flex-col gap-6 p-6">

    {{-- Page Header --}}
    <x-admin.page-header
        title="User Management"
        description="View user registration dates, roles, bookings count, and moderate accounts."
    />

    {{-- Stats Cards --}}
    <x-admin.stats-grid :cols="5">
        <x-admin.stats-card title="Total Accounts"   :value="$this->stats['total']" />
        <x-admin.stats-card title="Administrators"   :value="$this->stats['admins']"    value-color="text-teal-500" />
        <x-admin.stats-card title="Active Accounts"  :value="$this->stats['active']"    value-color="text-emerald-500" />
        <x-admin.stats-card title="Suspended"        :value="$this->stats['suspended']" value-color="text-red-500" />
        <x-admin.stats-card title="Total Bookings"   :value="number_format($this->stats['bookings'])" />
    </x-admin.stats-grid>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-48">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Search Name or Email…"
                icon="magnifying-glass"
                clearable
            />
        </div>

        <flux:select wire:model.live="filterRole" placeholder="All roles" class="w-40">
            <flux:select.option value="">All roles</flux:select.option>
            <flux:select.option value="admin">Administrator</flux:select.option>
            <flux:select.option value="customer">Customer</flux:select.option>
        </flux:select>

        <flux:select wire:model.live="filterStatus" placeholder="All statuses" class="w-44">
            <flux:select.option value="">All statuses</flux:select.option>
            <flux:select.option value="active">Active</flux:select.option>
            <flux:select.option value="suspended">Suspended</flux:select.option>
        </flux:select>

        @if($search || $filterRole || $filterStatus)
            <flux:button variant="ghost" size="sm"
                wire:click="$set('search', ''); $set('filterRole', ''); $set('filterStatus', '')">
                Clear filters
            </flux:button>
        @endif
    </div>

    {{-- Users Table --}}
    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-800/60">
                <tr>
                    <x-admin.sortable-th column="name"           :sort-by="$sortBy" :sort-dir="$sortDir">Name</x-admin.sortable-th>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Email</th>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Role</th>
                    <x-admin.sortable-th column="bookings_count" :sort-by="$sortBy" :sort-dir="$sortDir">Bookings</x-admin.sortable-th>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Status</th>
                    <x-admin.sortable-th column="created_at"     :sort-by="$sortBy" :sort-dir="$sortDir">Joined Date</x-admin.sortable-th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 bg-white dark:divide-zinc-700/50 dark:bg-zinc-900">
                @forelse($this->filteredUsers as $user)
                    <tr class="group transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/40">

                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <flux:avatar :initials="$this->getInitials($user['name'])" size="sm"
                                    class="bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200" />
                                <span class="font-medium text-zinc-900 dark:text-white">{{ $user['name'] }}</span>
                            </div>
                        </td>

                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $user['email'] }}</td>

                        <td class="px-4 py-3">
                            @if($user['role'] === 'admin')
                                <span class="inline-flex items-center rounded-md bg-teal-50 px-2 py-0.5 text-xs font-medium text-teal-700 dark:bg-teal-950/20 dark:text-teal-400">
                                    Admin
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                    Customer
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">
                            {{ $user['bookings_count'] }}
                        </td>

                        <td class="px-4 py-3">
                            <x-admin.status-badge :status="$user['status']" type="user" />
                        </td>

                        <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 text-xs">
                            {{ \Carbon\Carbon::parse($user['created_at'])->format('d M Y') }}
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                <flux:button size="sm" variant="ghost" icon="pencil-square"
                                    wire:click="openEdit({{ $user['id'] }})" title="Edit Profile" />
                                @if($user['id'] !== 1) {{-- Don't allow suspending the primary admin account --}}
                                    <flux:button size="sm" variant="ghost"
                                        icon="{{ $user['status'] === 'active' ? 'user-minus' : 'user-plus' }}"
                                        wire:click="confirmSuspend({{ $user['id'] }})"
                                        title="{{ $user['status'] === 'active' ? 'Suspend Account' : 'Activate Account' }}"
                                        class="{{ $user['status'] === 'active' ? 'text-red-500 hover:text-red-600' : 'text-emerald-600' }}" />
                                @endif
                            </div>
                        </td>

                    </tr>
                @empty
                    <x-admin.table-empty icon="users" message="No users found matching current filters." :colspan="7" />
                @endforelse
            </tbody>
        </table>

        <x-admin.table-footer :showing="count($this->filteredUsers)" :total="count($users)" noun="accounts" />
    </div>

    {{-- Edit User Modal --}}
    <flux:modal wire:model="showEditModal" class="w-full max-w-md">
        <div class="mb-6">
            <flux:heading size="lg">Edit User Profile</flux:heading>
            <flux:text class="mt-1">Update user account profile information, roles, and status levels.</flux:text>
        </div>

        <form wire:submit="save">
            <div class="space-y-4">
                <flux:field>
                    <flux:label>Full Name</flux:label>
                    <flux:input wire:model="form_name" placeholder="e.g. John Doe" />
                    <flux:error name="form_name" />
                </flux:field>

                <flux:field>
                    <flux:label>Email Address</flux:label>
                    <flux:input wire:model="form_email" type="email" placeholder="e.g. john@example.com" />
                    <flux:error name="form_email" />
                </flux:field>

                <flux:field>
                    <flux:label>Account Role</flux:label>
                    <flux:select wire:model="form_role">
                        <flux:select.option value="customer">Customer</flux:select.option>
                        <flux:select.option value="admin">Administrator</flux:select.option>
                    </flux:select>
                    <flux:error name="form_role" />
                </flux:field>

                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model="form_status">
                        <flux:select.option value="active">Active</flux:select.option>
                        <flux:select.option value="suspended">Suspended</flux:select.option>
                    </flux:select>
                    <flux:error name="form_status" />
                </flux:field>
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <flux:button variant="ghost" wire:click="$set('showEditModal', false)" type="button">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Save Changes</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Suspend / Activate Confirmation Modal --}}
    <flux:modal wire:model="showSuspendModal" class="max-w-sm">
        @php
            $suspendTarget = collect($this->users)->firstWhere('id', $this->suspendingId);
            $actionWord    = ($suspendTarget && $suspendTarget['status'] === 'active') ? 'Suspend' : 'Activate';
        @endphp
        <flux:heading size="lg" class="mb-2">{{ $actionWord }} User Account</flux:heading>
        <flux:text class="mb-6">
            Are you sure you want to {{ strtolower($actionWord) }} the account for
            <strong>{{ $suspendTarget['name'] ?? '' }}</strong>?
            @if($actionWord === 'Suspend')
                This will temporarily disable their login credentials and restrict entry access.
            @else
                This will restore login capabilities and fully enable account access.
            @endif
        </flux:text>
        <div class="flex justify-end gap-2">
            <flux:button variant="ghost" wire:click="$set('showSuspendModal', false)">Cancel</flux:button>
            <flux:button variant="{{ $actionWord === 'Suspend' ? 'danger' : 'primary' }}" wire:click="toggleStatus">
                {{ $actionWord }} Account
            </flux:button>
        </div>
    </flux:modal>

</div>
