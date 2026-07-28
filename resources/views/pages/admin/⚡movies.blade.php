<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\WithPagination;
use App\Models\Movie;

new #[Title('Movies & Shows'), Layout('layouts.app')] class extends Component {
    use WithPagination;
    // Display state
    public bool $showArchived = false;

    // Modal state
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public ?int $editingMovieId = null;

    // Form fields (with validation attributes)
    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('nullable|string')]
    public string $description = '';

    #[Validate('required|string|max:100')]
    public string $genre = '';

    #[Validate('required|integer|min:1')]
    public int $duration_mins = 0;

    #[Validate('required|string|max:10')]
    public string $age_rating = '';

    #[Validate('required|url|max:500')]
    public string $poster_url = '';

    // Filter state
    public string $search = '';
    public string $filterGenre = '';
    public string $filterStatus = ''; // 'now_showing', 'coming_soon', 'ended', 'no_screenings'

    // Reset pagination when any filter changes
    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedFilterGenre(): void { $this->resetPage(); }
    public function updatedFilterStatus(): void { $this->resetPage(); }
    public function updatedShowArchived(): void { $this->resetPage(); }

    // Sorting state
    public string $sortBy = 'title';
    public string $sortDir = 'asc';

    // Computed property for movies
    public function with(): array
    {
        $query = $this->showArchived
            ? Movie::onlyTrashed()
            : Movie::query();

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Apply genre filter
        if ($this->filterGenre) {
            $query->where('genre', $this->filterGenre);
        }

        // Apply status filter based on screening times
        $now = now();
        if ($this->filterStatus === 'now_showing') {
            $query->whereHas('screenings', fn($q) =>
                $q->where('starts_at', '<=', $now)
                  ->where('starts_at', '>=', $now->copy()->subHours(4))
            );
        } elseif ($this->filterStatus === 'coming_soon') {
            $query->whereHas('screenings', fn($q) => $q->where('starts_at', '>', $now));
        } elseif ($this->filterStatus === 'ended') {
            $query->whereDoesntHave('screenings', fn($q) => $q->where('starts_at', '>=', $now->copy()->subHours(4)))
                  ->whereHas('screenings');
        } elseif ($this->filterStatus === 'no_screenings') {
            $query->doesntHave('screenings');
        }

        // Load counts for related data
        $query->withCount('screenings');

        // Efficient status counts (avoids loading screening records into memory)
        $query->withCount([
            'screenings as now_showing_count' => fn($q) => $q
                ->where('starts_at', '<=', now())
                ->where('starts_at', '>=', now()->subHours(4)),
            'screenings as upcoming_count' => fn($q) => $q
                ->where('starts_at', '>', now()),
            'screenings as bookings_count' => fn($q) => $q
                ->join('bookings', 'screenings.id', '=', 'bookings.screening_id')
                ->where('bookings.status', 'confirmed'),
        ]);

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDir);

        return [
            'movies' => $query->paginate(15),
        ];
    }

    // Display methods
    public function toggleArchived(): void
    {
        $this->showArchived = !$this->showArchived;
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

    // Create methods
    public function openCreate(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function save(): void
    {
        $validated = $this->validate();
        Movie::create($validated);
        $this->closeCreateModal();
        session()->flash('success', 'Movie created!');
    }

    // Edit methods
    public function openEdit(int $id): void
    {
        $movie = Movie::findOrFail($id);
        $this->editingMovieId = $id;

        // Load movie data into form
        $this->title = $movie->title;
        $this->description = $movie->description ?? '';
        $this->genre = $movie->genre;
        $this->duration_mins = $movie->duration_mins;
        $this->age_rating = $movie->age_rating;
        $this->poster_url = $movie->poster_url;

        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->resetForm();
        $this->editingMovieId = null;
    }

    public function update(): void
    {
        $validated = $this->validate();
        Movie::findOrFail($this->editingMovieId)->update($validated);
        $this->closeEditModal();
        session()->flash('success', 'Movie updated!');
    }

    // Archive/Restore methods
    public function toggleStatus(int $id): void
    {
        $movie = Movie::withTrashed()->findOrFail($id);

        if ($movie->trashed()) {
            $movie->restore();
            session()->flash('success', 'Movie restored successfully!');
        } else {
            $movie->delete();
            session()->flash('success', 'Movie archived successfully!');
        }
    }

    // 9. Helper method
    private function resetForm(): void
    {
        $this->title = '';
        $this->description = '';
        $this->genre = '';
        $this->duration_mins = 0;
        $this->age_rating = '';
        $this->poster_url = '';
    }
};

?>

<div class="flex flex-col gap-6 p-6">
    {{-- Page header --}}
    <x-admin.page-header title="Movies & Shows" description="Manage your cinema catalogue, showtimes and statuses.">
        <x-slot:action>
            <flux:button icon="plus" variant="primary" wire:click="openCreate">
                Add Movie
            </flux:button>
        </x-slot:action>
    </x-admin.page-header>

    {{-- Filters --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-1 gap-3">
            {{-- Search --}}
            <div class="flex-1 max-w-md">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Search movies..." icon="magnifying-glass" />
            </div>

            {{-- Genre filter --}}
            <flux:select wire:model.live="filterGenre" placeholder="All Genres" class="w-40">
                <option value="">All Genres</option>
                <option value="Action">Action</option>
                <option value="Comedy">Comedy</option>
                <option value="Drama">Drama</option>
                <option value="Horror">Horror</option>
                <option value="Sci-Fi">Sci-Fi</option>
            </flux:select>

            {{-- Status filter --}}
            <flux:select wire:model.live="filterStatus" placeholder="All Status" class="w-44">
                <option value="">All Status</option>
                <option value="now_showing">🎬 Now Showing</option>
                <option value="coming_soon">📅 Coming Soon</option>
                <option value="ended">⏹️ Ended</option>
                <option value="no_screenings">❌ No Screenings</option>
            </flux:select>
        </div>

        {{-- Archived toggle --}}
        <flux:button variant="ghost" icon="archive-box" wire:click="toggleArchived">
            {{ $showArchived ? 'Show Active' : 'Show Archived' }}
        </flux:button>
    </div>

    {{-- Table --}}
    <x-admin.table
    :table-headers="[
        ['label' => 'Movie', 'column' => 'title', 'sortable' => true],
        ['label' => 'Genre', 'column' => 'genre', 'sortable' => true],
        ['label' => 'Duration', 'column' => 'duration_mins', 'sortable' => true],
        ['label' => 'Age Rating', 'column' => 'age_rating', 'sortable' => true],
        ['label' => 'Screenings', 'column' => 'screenings_count', 'sortable' => true],
        ['label' => 'Bookings', 'column' => 'bookings_count', 'sortable' => true],
        ['label' => 'Status', 'column' => 'screenings_count', 'sortable' => true],
        ['label' => 'Created', 'column' => 'created_at', 'sortable' => true],
    ]"
    :data="$movies"
    :sort-by="$sortBy"
    :sort-dir="$sortDir"
    empty-message="No movies match your filters."
    empty-icon="film"
    :show-footer="true"
    :total="$movies->total()"
    footer-noun="titles"
>
    {{-- Custom row rendering --}}
    @foreach($movies as $movie)
        <tr wire:key="movie-{{ $movie->id }}" class="group transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/40">
            {{-- Movie title + poster --}}
            <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                    @if($movie->poster_url)
                        <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}"
                            class="size-10 rounded-lg object-cover"
                            onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2240%22 height=%2260%22%3E%3Crect fill=%22%23e5e7eb%22 width=%2240%22 height=%2260%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 fill=%22%239ca3af%22 font-size=%228%22 font-family=%22system-ui%22%3ENo Image%3C/text%3E%3C/svg%3E';" />
                    @else
                        <div class="size-10 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                            <svg class="size-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                    <div>
                        <p class="font-medium text-zinc-900 dark:text-white">{{ $movie->title }}</p>
                        <p class="text-xs text-zinc-400">{{ \Str::limit($movie->description ?? 'No description', 40) }}</p>
                    </div>
                </div>
            </td>

            {{-- Genre --}}
            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">
                <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                    {{ $movie->genre }}
                </span>
            </td>

            {{-- Duration --}}
            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">
                {{ $movie->duration_mins }} mins
            </td>

            {{-- Age Rating --}}
            <td class="px-4 py-3">
                <span class="inline-flex items-center rounded-md bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                    {{ $movie->age_rating }}
                </span>
            </td>

            {{-- Screenings Count --}}
            <td class="px-4 py-3 text-center font-medium text-zinc-900 dark:text-white">
                {{ number_format($movie->screenings_count ?? 0) }}
            </td>

            {{-- Bookings Count --}}
            <td class="px-4 py-3 text-center font-medium text-emerald-600 dark:text-emerald-400">
                {{ number_format($movie->bookings_count ?? 0) }}
            </td>

            {{-- Status (computed from screenings) --}}
            <td class="px-4 py-3">
                @if($movie->screenings_count === 0)
                    <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                        No Screenings
                    </span>
                @elseif($movie->now_showing_count > 0)
                    <span class="inline-flex items-center rounded-md bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">
                        Now Showing
                    </span>
                @elseif($movie->upcoming_count > 0)
                    <span class="inline-flex items-center rounded-md bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                        Coming Soon
                    </span>
                @else
                    <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                        Ended
                    </span>
                @endif
            </td>

            {{-- Created Date --}}
            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 text-xs">
                {{ $movie->created_at->format('d M Y') }}
            </td>

            {{-- Actions --}}
            <td class="px-4 py-3">
                <div class="flex items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                    <flux:button size="sm" variant="ghost" icon="pencil-square"
                        wire:click="openEdit({{ $movie->id }})" title="Edit" />
                    <flux:button size="sm" variant="ghost"
                        icon="{{ $movie->trashed() ? 'arrow-path' : 'archive-box' }}"
                        wire:click="toggleStatus({{ $movie->id }})"
                        title="{{ $movie->trashed() ? 'Restore' : 'Archive' }}" />
                </div>
            </td>
        </tr>
    @endforeach

    {{-- Custom empty state actions --}}
    <x-slot:emptyActions>
        <flux:button variant="ghost" size="sm"
            wire:click="$set('search', ''); $set('filterGenre', ''); $set('filterStatus', '')">
            Clear filters
        </flux:button>
    </x-slot:emptyActions>
</x-admin.table>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $movies->links() }}
    </div>

    {{-- Create Movie Modal --}}
    <flux:modal wire:model="showCreateModal" class="w-full max-w-2xl">
        <div class="mb-6">
            <flux:heading size="lg">Add New Movie</flux:heading>
            <flux:text class="mt-1">Add a new movie to your cinema catalogue.</flux:text>
        </div>

        <form wire:submit="save">
            <div class="space-y-4">
                <flux:field>
                    <flux:label>Title</flux:label>
                    <flux:input wire:model="title" placeholder="Enter movie title" />
                    <flux:error name="title" />
                </flux:field>

                <flux:field>
                    <flux:label>Description</flux:label>
                    <flux:textarea wire:model="description" placeholder="Brief description of the movie" rows="3" />
                    <flux:error name="description" />
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Genre</flux:label>
                        <flux:input wire:model="genre" placeholder="e.g., Action, Comedy" />
                        <flux:error name="genre" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Duration (minutes)</flux:label>
                        <flux:input wire:model="duration_mins" type="number" placeholder="120" />
                        <flux:error name="duration_mins" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Age Rating</flux:label>
                        <flux:input wire:model="age_rating" placeholder="e.g., PG-13, R" />
                        <flux:error name="age_rating" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Poster URL</flux:label>
                        <flux:input wire:model="poster_url" placeholder="https://..." />
                        <flux:error name="poster_url" />
                    </flux:field>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showCreateModal', false)">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Create Movie</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Edit Movie Modal --}}
    <flux:modal wire:model="showEditModal" class="w-full max-w-2xl">
        <div class="mb-6">
            <flux:heading size="lg">Edit Movie</flux:heading>
            <flux:text class="mt-1">Update movie details and availability.</flux:text>
        </div>

        <form wire:submit="update">
            <div class="space-y-4">
                <flux:field>
                    <flux:label>Title</flux:label>
                    <flux:input wire:model="title" placeholder="Enter movie title" />
                    <flux:error name="title" />
                </flux:field>

                <flux:field>
                    <flux:label>Description</flux:label>
                    <flux:textarea wire:model="description" placeholder="Brief description of the movie" rows="3" />
                    <flux:error name="description" />
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Genre</flux:label>
                        <flux:input wire:model="genre" placeholder="e.g., Action, Comedy" />
                        <flux:error name="genre" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Duration (minutes)</flux:label>
                        <flux:input wire:model="duration_mins" type="number" placeholder="120" />
                        <flux:error name="duration_mins" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Age Rating</flux:label>
                        <flux:input wire:model="age_rating" placeholder="e.g., PG-13, R" />
                        <flux:error name="age_rating" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Poster URL</flux:label>
                        <flux:input wire:model="poster_url" placeholder="https://..." />
                        <flux:error name="poster_url" />
                    </flux:field>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <flux:button type="button" variant="ghost" wire:click="$set('showEditModal', false)">Cancel</flux:button>
                <flux:button type="submit" variant="primary">Update Movie</flux:button>
            </div>
        </form>
    </flux:modal>

</div>
