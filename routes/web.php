<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Livewire\Livewire;
use App\Http\Middleware\EnsureUserRole;

// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Centralized Dashboard Route with Role-Based Redirection
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        // Log dashboard access attempt
        \Log::info('Dashboard access', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role
        ]);

        // Role-based redirection
        return match($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            default => redirect()->route('home')->with('error', 'Unauthorized access')
        };
    })->name('dashboard');

    // Settings Routes (Accessible to all authenticated users)
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::redirect('/', 'profile');

        Volt::route('/profile', 'settings.profile')->name('profile');
        Volt::route('/password', 'settings.password')->name('password');
        Volt::route('/appearance', 'settings.appearance')->name('appearance');
    });

    // Admin-Only Routes
    Route::middleware([EnsureUserRole::class . ':admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            // Existing dashboard route
            Route::get('/dashboard', function () {
                return view('dashboard');
            })->name('dashboard');

            // Additional Admin Routes
            Route::group([
                'prefix' => 'manage',
                'name' => 'manage.'
            ], function () {
                Volt::route('/users', 'admin.manage.users')->name('users');
                Volt::route('/departments', 'admin.manage.departments')->name('departments');
                Volt::route('/papers', 'admin.manage.papers')->name('papers');
            });

            // Analytics and Reporting
            Volt::route('/analytics', 'admin.analytics')->name('analytics');
            Volt::route('/logs', 'admin.logs')->name('logs');
        });

    // Student-Only Routes
    Route::middleware([EnsureUserRole::class . ':student'])
        ->prefix('student')
        ->name('student.')
        ->group(function () {
            // Existing dashboard route
            Route::get('/dashboard', function () {
                return view('dashboard');
            })->name('dashboard');

            // Paper Management for Students
            Route::group([
                'prefix' => 'papers',
                'name' => 'papers.'
            ], function () {
                Volt::route('/', 'student.papers.index')->name('index');
                Volt::route('/search', 'student.papers.search')->name('search');
                Volt::route('/download', 'student.papers.download')->name('download');
            });

            // Student Profile
            Volt::route('/profile', 'student.profile')->name('profile');
        });
});

// Fallback Route for Unauthorized Access
Route::fallback(function () {
    // Log unauthorized access attempts
    if (auth()->check()) {
        \Log::warning('Unauthorized route access', [
            'user_id' => auth()->id(),
            'email' => auth()->user()->email,
            'role' => auth()->user()->role,
            'attempted_route' => request()->fullUrl()
        ]);
    }

    return redirect()->route('dashboard')->with('error', 'Unauthorized access');
});

// Include default authentication routes
require __DIR__.'/auth.php';