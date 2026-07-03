<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

new #[Title('Movies & Shows'), Layout('layouts.app')] class extends Component {
    public string $search = '';
    public string $filterGenre = '';
    public string $filterStatus = '';
    public string $sortBy = 'title';
    public string $sortDir = 'asc';

    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    // Form fields
    public string $form_title = '';
    public string $form_genre = '';
    public string $form_duration = '';
    public string $form_rating = '';
    public string $form_format = '';
    public string $form_price = '';
    public string $form_status = 'active';
    public string $form_description = '';
    public string $form_image = '';
    public string $form_release = '';

    public array $movies = [
        [
            'id' => 1,
            'title' => 'Neon Horizon',
            'genre' => 'Sci-Fi',
            'duration' => '2h 08m',
            'rating' => '4.8',
            'format' => 'IMAX',
            'price' => 14.5,
            'status' => 'active',
            'description' => 'A visually stunning sci-fi epic where the boundaries of reality blur with digital consciousness.',
            'image' => 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=400&q=80',
            'release' => '2025-06-15',
            'bookings' => 342,
        ],
        [
            'id' => 2,
            'title' => 'Moonlight Run',
            'genre' => 'Thriller',
            'duration' => '1h 54m',
            'rating' => '4.6',
            'format' => 'Dolby Atmos',
            'price' => 12.5,
            'status' => 'active',
            'description' => 'A heart-pounding thriller following a former agent racing against time through neon-lit city streets.',
            'image' => 'https://images.unsplash.com/photo-1516280440614-37939bbacd81?auto=format&fit=crop&w=400&q=80',
            'release' => '2025-05-20',
            'bookings' => 219,
        ],
        [
            'id' => 3,
            'title' => 'Golden Hour',
            'genre' => 'Drama',
            'duration' => '2h 12m',
            'rating' => '4.9',
            'format' => 'Standard',
            'price' => 11.0,
            'status' => 'active',
            'description' => 'An emotional drama following three strangers whose lives intertwine during one golden hour.',
            'image' => 'https://images.unsplash.com/photo-1478720568477-152d9b164e26?auto=format&fit=crop&w=400&q=80',
            'release' => '2025-04-10',
            'bookings' => 478,
        ],
        [
            'id' => 4,
            'title' => 'Iron Veil',
            'genre' => 'Action',
            'duration' => '2h 24m',
            'rating' => '4.4',
            'format' => 'IMAX',
            'price' => 15.0,
            'status' => 'active',
            'description' => 'An elite operative goes rogue to expose a global conspiracy hidden in plain sight.',
            'image' => 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?auto=format&fit=crop&w=400&q=80',
            'release' => '2025-07-01',
            'bookings' => 91,
        ],
        [
            'id' => 5,
            'title' => 'Whisper Woods',
            'genre' => 'Horror',
            'duration' => '1h 48m',
            'rating' => '4.2',
            'format' => 'Standard',
            'price' => 10.5,
            'status' => 'coming_soon',
            'description' => 'Deep in an ancient forest, a group of friends uncovers a terrifying secret that should have stayed buried.',
            'image' => 'https://images.unsplash.com/photo-1509347528160-9a9e33742cdb?auto=format&fit=crop&w=400&q=80',
            'release' => '2025-08-15',
            'bookings' => 0,
        ],
        [
            'id' => 6,
            'title' => 'The Last Orbit',
            'genre' => 'Sci-Fi',
            'duration' => '2h 35m',
            'rating' => '4.7',
            'format' => 'Dolby Atmos',
            'price' => 13.0,
            'status' => 'archived',
            'description' => "The crew of humanity's last spaceship must decide who deserves a place among the stars.",
            'image' => 'https://images.unsplash.com/photo-1446776877081-d282a0f896e2?auto=format&fit=crop&w=400&q=80',
            'release' => '2024-11-03',
            'bookings' => 612,
        ],
        [
            'id' => 7,
            'title' => 'City of Echoes',
            'genre' => 'Drama',
            'duration' => '1h 59m',
            'rating' => '4.5',
            'format' => 'Standard',
            'price' => 11.5,
            'status' => 'coming_soon',
            'description' => "A journalist uncovers buried stories of a city's forgotten residents, changing lives in the process.",
            'image' => 'https://images.unsplash.com/photo-1477959858617-67f85cf4f1df?auto=format&fit=crop&w=400&q=80',
            'release' => '2025-09-05',
            'bookings' => 0,
        ],
        [
            'id' => 8,
            'title' => 'Blaze Protocol',
            'genre' => 'Action',
            'duration' => '2h 01m',
            'rating' => '4.3',
            'format' => 'IMAX',
            'price' => 14.0,
            'status' => 'archived',
            'description' => "When a rogue AI takes control of a city's infrastructure, one firefighter must save the day.",
            'image' => 'https://images.unsplash.com/photo-1534796636912-3b95b3ab5986?auto=format&fit=crop&w=400&q=80',
            'release' => '2024-09-22',
            'bookings' => 389,
        ],
    ];

    public array $genres = ['Action', 'Drama', 'Horror', 'Sci-Fi', 'Thriller', 'Comedy', 'Romance', 'Animation'];
    public array $formats = ['IMAX', 'Dolby Atmos', 'Standard', '4DX'];

    #[Computed]
    public function filteredMovies(): array
    {
        return collect($this->movies)
            ->when($this->search, fn($c) => $c->filter(fn($m) => str_contains(strtolower($m['title']), strtolower($this->search)) || str_contains(strtolower($m['genre']), strtolower($this->search))))
            ->when($this->filterGenre, fn($c) => $c->where('genre', $this->filterGenre))
            ->when($this->filterStatus, fn($c) => $c->where('status', $this->filterStatus))
            ->sortBy($this->sortBy, SORT_REGULAR, $this->sortDir === 'desc')
            ->values()
            ->toArray();
    }

    #[Computed]
    public function stats(): array
    {
        $all = collect($this->movies);
        return [
            'total'       => $all->count(),
            'active'      => $all->where('status', 'active')->count(),
            'coming_soon' => $all->where('status', 'coming_soon')->count(),
            'archived'    => $all->where('status', 'archived')->count(),
            'bookings'    => $all->sum('bookings'),
        ];
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $movie = collect($this->movies)->firstWhere('id', $id);
        if (!$movie) return;

        $this->editingId        = $id;
        $this->form_title       = $movie['title'];
        $this->form_genre       = $movie['genre'];
        $this->form_duration    = $movie['duration'];
        $this->form_rating      = $movie['rating'];
        $this->form_format      = $movie['format'];
        $this->form_price       = (string) $movie['price'];
        $this->form_status      = $movie['status'];
        $this->form_description = $movie['description'];
        $this->form_image       = $movie['image'];
        $this->form_release     = $movie['release'];
        $this->showModal        = true;
    }

    public function save(): void
    {
        $this->validate([
            'form_title'  => 'required|min:2',
            'form_genre'  => 'required',
            'form_duration' => 'required',
            'form_price'  => 'required|numeric|min:0',
            'form_status' => 'required',
        ]);

        if ($this->editingId) {
            $this->movies = collect($this->movies)
                ->map(function ($m) {
                    if ($m['id'] === $this->editingId) {
                        return array_merge($m, [
                            'title'       => $this->form_title,
                            'genre'       => $this->form_genre,
                            'duration'    => $this->form_duration,
                            'rating'      => $this->form_rating,
                            'format'      => $this->form_format,
                            'price'       => (float) $this->form_price,
                            'status'      => $this->form_status,
                            'description' => $this->form_description,
                            'image'       => $this->form_image,
                            'release'     => $this->form_release,
                        ]);
                    }
                    return $m;
                })
                ->toArray();
        } else {
            $this->movies[] = [
                'id'          => collect($this->movies)->max('id') + 1,
                'title'       => $this->form_title,
                'genre'       => $this->form_genre,
                'duration'    => $this->form_duration,
                'rating'      => $this->form_rating ?: '—',
                'format'      => $this->form_format ?: 'Standard',
                'price'       => (float) $this->form_price,
                'status'      => $this->form_status,
                'description' => $this->form_description,
                'image'       => $this->form_image,
                'release'     => $this->form_release,
                'bookings'    => 0,
            ];
        }

        $this->showModal = false;
        $this->resetForm();

        Flux::toast(
            $this->editingId ? 'Movie updated successfully.' : 'New movie added to the catalogue.',
            heading: $this->editingId ? 'Movie Saved' : 'Movie Added',
            variant: 'success'
        );
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId      = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $this->movies = collect($this->movies)->reject(fn($m) => $m['id'] === $this->deletingId)->values()->toArray();
        $this->showDeleteModal = false;
        $this->deletingId      = null;

        Flux::toast('The movie has been permanently removed.', heading: 'Movie Deleted', variant: 'danger');
    }

    public function toggleStatus(int $id): void
    {
        $movie = collect($this->movies)->firstWhere('id', $id);
        $isArchiving = $movie && $movie['status'] === 'active';

        $this->movies = collect($this->movies)
            ->map(function ($m) use ($id) {
                if ($m['id'] === $id) {
                    $m['status'] = $m['status'] === 'active' ? 'archived' : 'active';
                }
                return $m;
            })
            ->toArray();

        Flux::toast(
            $isArchiving ? 'Movie has been archived and hidden from listings.' : 'Movie restored and now showing.',
            heading: $isArchiving ? 'Movie Archived' : 'Movie Restored',
            variant: $isArchiving ? 'warning' : 'success'
        );
    }

    private function resetForm(): void
    {
        $this->form_title       = '';
        $this->form_genre       = '';
        $this->form_duration    = '';
        $this->form_rating      = '';
        $this->form_format      = '';
        $this->form_price       = '';
        $this->form_status      = 'active';
        $this->form_description = '';
        $this->form_image       = '';
        $this->form_release     = '';
    }
};
?>

<div class="flex flex-col gap-6 p-6">

    {{-- Page header --}}
    <x-admin.page-header
        title="Movies & Shows"
        description="Manage your cinema catalogue, showtimes and statuses."
    >
        <x-slot:action>
            <flux:button icon="plus" variant="primary" wire:click="openCreate">
                Add Movie
            </flux:button>
        </x-slot:action>
    </x-admin.page-header>

    {{-- Stats row --}}
    <x-admin.stats-grid :cols="4">
        <x-admin.stats-card title="Total Titles"   :value="$this->stats['total']" />
        <x-admin.stats-card title="Now Showing"    :value="$this->stats['active']"      value-color="text-emerald-500" />
        <x-admin.stats-card title="Coming Soon"    :value="$this->stats['coming_soon']" value-color="text-amber-500" />
        <x-admin.stats-card title="Total Bookings" :value="number_format($this->stats['bookings'])" />
    </x-admin.stats-grid>

    {{-- Filters + Search --}}
    <div class="flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-48">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search title or genre…"
                icon="magnifying-glass" clearable />
        </div>

        <flux:select wire:model.live="filterGenre" placeholder="All genres" class="w-40">
            <flux:select.option value="">All genres</flux:select.option>
            @foreach ($genres as $genre)
                <flux:select.option value="{{ $genre }}">{{ $genre }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="filterStatus" placeholder="All statuses" class="w-44">
            <flux:select.option value="">All statuses</flux:select.option>
            <flux:select.option value="active">Now Showing</flux:select.option>
            <flux:select.option value="coming_soon">Coming Soon</flux:select.option>
            <flux:select.option value="archived">Archived</flux:select.option>
        </flux:select>

        @if ($search || $filterGenre || $filterStatus)
            <flux:button variant="ghost" size="sm"
                wire:click="$set('search', ''); $set('filterGenre', ''); $set('filterStatus', '')">
                Clear filters
            </flux:button>
        @endif
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-800/60">
                <tr>
                    <x-admin.sortable-th column="title"    :sort-by="$sortBy" :sort-dir="$sortDir">Movie</x-admin.sortable-th>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Genre</th>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Format</th>
                    <x-admin.sortable-th column="price"    :sort-by="$sortBy" :sort-dir="$sortDir">Price</x-admin.sortable-th>
                    <x-admin.sortable-th column="bookings" :sort-by="$sortBy" :sort-dir="$sortDir">Bookings</x-admin.sortable-th>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Status</th>
                    <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Release</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 bg-white dark:divide-zinc-700/50 dark:bg-zinc-900">
                @forelse($this->filteredMovies as $movie)
                    <tr class="group transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/40">

                        {{-- Movie title + thumbnail --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $movie['image'] }}" alt="{{ $movie['title'] }}"
                                    class="size-10 rounded-lg object-cover" />
                                <div>
                                    <p class="font-medium text-zinc-900 dark:text-white">{{ $movie['title'] }}</p>
                                    <p class="text-xs text-zinc-400">{{ $movie['duration'] }} &nbsp;·&nbsp; ⭐ {{ $movie['rating'] }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $movie['genre'] }}</td>

                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                {{ $movie['format'] }}
                            </span>
                        </td>

                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">
                            £{{ number_format($movie['price'], 2) }}
                        </td>

                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">
                            {{ number_format($movie['bookings']) }}
                        </td>

                        <td class="px-4 py-3">
                            <x-admin.status-badge :status="$movie['status']" type="movie" />
                        </td>

                        <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 text-xs">
                            {{ \Carbon\Carbon::parse($movie['release'])->format('d M Y') }}
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                <flux:button size="sm" variant="ghost" icon="pencil-square"
                                    wire:click="openEdit({{ $movie['id'] }})" title="Edit" />
                                <flux:button size="sm" variant="ghost"
                                    icon="{{ $movie['status'] === 'active' ? 'archive-box' : 'arrow-path' }}"
                                    wire:click="toggleStatus({{ $movie['id'] }})"
                                    title="{{ $movie['status'] === 'active' ? 'Archive' : 'Restore' }}" />
                                <flux:button size="sm" variant="ghost" icon="trash"
                                    wire:click="confirmDelete({{ $movie['id'] }})" title="Delete"
                                    class="text-red-500 hover:text-red-600" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-admin.table-empty icon="film" message="No movies match your filters." :colspan="8">
                        <x-slot:actions>
                            <flux:button variant="ghost" size="sm"
                                wire:click="$set('search', ''); $set('filterGenre', ''); $set('filterStatus', '')">
                                Clear filters
                            </flux:button>
                        </x-slot:actions>
                    </x-admin.table-empty>
                @endforelse
            </tbody>
        </table>

        <x-admin.table-footer :showing="count($this->filteredMovies)" :total="count($movies)" noun="titles" />
    </div>

    {{-- Add / Edit Modal --}}
    <flux:modal wire:model="showModal" class="w-full max-w-2xl">
        <div class="mb-6">
            <flux:heading size="lg">{{ $editingId ? 'Edit Movie' : 'Add New Movie' }}</flux:heading>
            <flux:text class="mt-1">
                {{ $editingId ? 'Update the details for this title.' : 'Fill in the details to add a new title to the catalogue.' }}
            </flux:text>
        </div>

        <form wire:submit="save">
            <div class="grid grid-cols-2 gap-4">

                <flux:field class="col-span-2">
                    <flux:label>Title</flux:label>
                    <flux:input wire:model="form_title" placeholder="e.g. Neon Horizon" />
                    <flux:error name="form_title" />
                </flux:field>

                <flux:field>
                    <flux:label>Genre</flux:label>
                    <flux:select wire:model="form_genre">
                        <flux:select.option value="">Select genre</flux:select.option>
                        @foreach ($genres as $genre)
                            <flux:select.option value="{{ $genre }}">{{ $genre }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="form_genre" />
                </flux:field>

                <flux:field>
                    <flux:label>Format</flux:label>
                    <flux:select wire:model="form_format">
                        <flux:select.option value="">Select format</flux:select.option>
                        @foreach ($formats as $format)
                            <flux:select.option value="{{ $format }}">{{ $format }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:label>Duration</flux:label>
                    <flux:input wire:model="form_duration" placeholder="e.g. 2h 08m" />
                </flux:field>

                <flux:field>
                    <flux:label>Price (£)</flux:label>
                    <flux:input wire:model="form_price" type="number" step="0.50" min="0" placeholder="0.00" />
                    <flux:error name="form_price" />
                </flux:field>

                <flux:field>
                    <flux:label>Rating</flux:label>
                    <flux:input wire:model="form_rating" placeholder="e.g. 4.8" />
                </flux:field>

                <flux:field>
                    <flux:label>Release Date</flux:label>
                    <flux:input wire:model="form_release" type="date" />
                </flux:field>

                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model="form_status">
                        <flux:select.option value="active">Now Showing</flux:select.option>
                        <flux:select.option value="coming_soon">Coming Soon</flux:select.option>
                        <flux:select.option value="archived">Archived</flux:select.option>
                    </flux:select>
                    <flux:error name="form_status" />
                </flux:field>

                <flux:field class="col-span-2">
                    <flux:label>Poster URL</flux:label>
                    <flux:input wire:model="form_image" placeholder="https://…" />
                </flux:field>

                <flux:field class="col-span-2">
                    <flux:label>Description</flux:label>
                    <flux:textarea wire:model="form_description" rows="3" placeholder="Short synopsis…" />
                </flux:field>

            </div>

            <div class="mt-6 flex justify-end gap-2">
                <flux:button variant="ghost" wire:click="$set('showModal', false)" type="button">Cancel</flux:button>
                <flux:button type="submit" variant="primary">
                    {{ $editingId ? 'Save Changes' : 'Add Movie' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Delete Confirmation Modal --}}
    <x-admin.confirm-modal
        wire="showDeleteModal"
        title="Delete Movie"
        message="This will permanently remove the movie from the catalogue. This action cannot be undone."
        confirm-label="Delete"
        confirm-action="delete"
    />

</div>
