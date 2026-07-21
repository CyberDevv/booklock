# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**BookLock** is a cinema seat booking platform built with Laravel 13.17, Livewire 4, and Flux UI. Uses SQLite for local development. Portfolio-quality project emphasizing security, best practices, and modern Laravel patterns.

## Commands

### Development
```bash
# First-time setup (installs deps, generates key, migrates, builds assets)
composer setup

# Start dev server with queue worker and Vite (runs concurrently)
composer dev

# Run migrations
php artisan migrate

# Seed database (creates admin user + roles/permissions)
php artisan db:seed
```

### Testing & Quality
```bash
# Run full test suite (config:clear → pint check → phpstan → pest)
composer test

# Run tests only
php artisan test

# Run specific test
php artisan test --filter=TestName

# Format code (rector + pint)
composer format

# Lint only (parallel)
composer lint

# Type check only
composer types:check
```

### Livewire
```bash
# List Livewire routes
php artisan route:list --name=livewire

# Clear Livewire component cache
php artisan livewire:flush
```

## Architecture

### Core Booking Flow

**Movie → Screening → Screening_Seat → Booking**

1. **Movies** have poster, genre, duration, age rating, is_active flag
2. **Halls** define seating capacity (rows × seats_per_row)
3. **Screenings** link a Movie to a Hall at a specific `starts_at` time with `price_kobo`
4. **Screening_Seats** represent individual bookable seats per screening
   - Unique constraint: `(screening_id, row_label, seat_number)`
   - `is_booked` flag tracks availability
   - Seeds generate seats automatically when screening is created

### Pricing Pattern

**All prices stored as integers in kobo (smallest currency unit).**

- Database column: `price_kobo` (integer)
- Display: divide by 100 and format with currency symbol
- Input: multiply by 100 before saving

Example: ₦2,500.00 → stored as `250000` kobo.

### Authentication & Authorization

**Stack:** Laravel Fortify + Spatie Laravel Permission

**Features enabled:**
- Passkey authentication (WebAuthn) via `PasskeyUser` interface
- Two-factor authentication via `TwoFactorAuthenticatable`
- Role-based access control

**Roles:**
- `Admin`: full access to admin panel (movies, tickets, bookings, users, reports, revenue)
- `Customer`: can view movies, book tickets

**Default admin credentials** (seeded):
- Email: `admin_book_lock@booklock.booking`
- Password: `#123456789#BookLock`

**Middleware:**
- `auth` → requires authenticated user
- `role:Admin` → requires Admin role (stacks on top of `auth`)

### Livewire 4 Patterns

**Page components** use `Route::livewire()` syntax:
```php
Route::livewire('book/{movie}', 'pages::booking')->name('booking');
Route::livewire('admin/movies', 'pages::admin.movies')->name('movies.index');
```

Components live under `app/Livewire/` but are not currently organized into Pages/ subfolders. Use Livewire 4 features (synthesizers, computed properties, lazy loading) throughout.

### Database Schema Highlights

**Cascading deletes:**
- Deleting a Movie cascades to Screenings
- Deleting a Screening cascades to Screening_Seats
- Deleting a Hall cascades to Screenings

**Indexes:**
- `screenings`: `(movie_id, starts_at)`, `(hall_id, starts_at)` for efficient scheduling queries
- `screening_seats`: `(screening_id, is_booked)` for fast availability checks

**Timestamps:**
- `screenings.starts_at` is `timestampTz` (timezone-aware)
- All models use standard Laravel `created_at`/`updated_at`

### Blade Components

**Custom components** under `resources/views/components/`:
- `admin/*` → admin panel UI patterns (stats-card, sortable-th, confirm-modal, etc.)
- `auth-*` → authentication flow components
- `passkey-*` → WebAuthn passkey registration/verification

**Flux components** → Livewire's official UI library, extended with custom icons in `resources/views/flux/icon/`.

## Security Patterns

**Password defaults** (AppServiceProvider):
- Production: 12+ chars, mixed case, letters, numbers, symbols, uncompromised
- Local: no requirements

**Database safety:**
- Destructive commands prohibited in production via `DB::prohibitDestructiveCommands()`

**Date handling:**
- Uses `CarbonImmutable` globally for immutability

## File Locations

- Livewire components: `app/Livewire/`
- Routes: `routes/web.php` (main), `routes/settings.php` (settings area)
- Seeders: `database/seeders/` (start with `DatabaseSeeder.php`)
- Factories: `database/factories/` (one per model)
- Tests: `tests/Feature/` (Pest PHP)

## Notes

- **Duplicate route definitions** exist in `routes/web.php` (lines 11-19 and 21-30) — the second block adds `role:Admin` middleware but re-declares the same routes. This should be consolidated.
- **SQLite in use** — check `database/database.sqlite` exists before running migrations.
- **Admin panel requires Admin role** — test users created without role assignment can't access `/admin/*` routes.
