<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Livewire\Livewire;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Main dashboard route that will redirect based on role
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

// Role-based dashboard routes
Route::middleware(['auth'])->group(function () {
    // Admin-only routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/dashboard', function () {
            return Livewire::mount('admin.dashboard');
        })->name('admin.dashboard');
    });

    // Student-only routes
    Route::middleware(['student'])->group(function () {
        Route::get('/student/dashboard', function () {
            return Livewire::mount('student.dashboard');
        })->name('student.dashboard');
    });
});

require __DIR__.'/auth.php';