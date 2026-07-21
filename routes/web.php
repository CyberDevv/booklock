<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('tickets', 'tickets')->name('tickets');
Route::livewire('book/{movie}', 'pages::booking')->name('booking');

Route::middleware(['auth'])->group(function () {
    Route::livewire('dashboard', 'pages::dashboard')->name('dashboard');
    Route::livewire('admin/movies', 'pages::admin.movies')->name('movies.index');
    Route::livewire('admin/tickets', 'pages::admin.tickets')->name('admin.tickets');
    Route::livewire('admin/bookings', 'pages::admin.bookings')->name('admin.bookings');
    Route::livewire('admin/users', 'pages::admin.users')->name('admin.users');
    Route::livewire('admin/reports', 'pages::admin.reports')->name('admin.reports');
    Route::livewire('admin/revenue', 'pages::admin.revenue')->name('admin.revenue');
});

Route::middleware(['auth'])->group(function () {
    Route::middleware('role:Admin')->group(function () {
        Route::livewire('dashboard', 'pages::dashboard')->name('dashboard');
        Route::livewire('admin/movies', 'pages::admin.movies')->name('movies.index');
        Route::livewire('admin/tickets', 'pages::admin.tickets')->name('admin.tickets');
        Route::livewire('admin/bookings', 'pages::admin.bookings')->name('admin.bookings');
        Route::livewire('admin/users', 'pages::admin.users')->name('admin.users');
        Route::livewire('admin/reports', 'pages::admin.reports')->name('admin.reports');
        Route::livewire('admin/revenue', 'pages::admin.revenue')->name('admin.revenue');
    });
});

require __DIR__ . '/settings.php';
