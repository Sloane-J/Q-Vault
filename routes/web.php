<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;
use App\Http\Middleware\EnsureUserRole;
use App\Livewire\PaperManager;
use App\Livewire\PaperUploader;

// Public Route
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard with Role Redirection
    Route::get('/dashboard', function () {
        $user = auth()->user();

        \Log::info('Dashboard access', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role
        ]);

        return match($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            default => redirect()->route('home')->with('error', 'Unauthorized access'),
        };
    })->name('dashboard');

    // User Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::redirect('/', 'profile');
        Volt::route('/profile', 'settings.profile')->name('profile');
        Volt::route('/password', 'settings.password')->name('password');
        Volt::route('/appearance', 'settings.appearance')->name('appearance');
    });

    // Admin Routes
    Route::middleware([EnsureUserRole::class . ':admin'])->prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');

        Volt::route('/users', 'admin.manage.users')->name('users');

        Route::get('/department', function () {
            return view('admin.department');
        })->name('department.view');

        // Updated papers route to use your PaperManager component
        Route::get('/papers', PaperManager::class)->name('papers.view');

        // Optional: Add PaperUploader route if needed
        Route::get('/papers/upload', PaperUploader::class)->name('papers.upload');

        Route::get('/department-old', function () {
            return view('admin.department');
        })->name('department.view.old');

        Route::get('/departments', function () {
            return view('admin.departments');
        })->name('departments');

        // Keep Volt route for other paper-related functionality
        Volt::route('/papers/manage', 'admin.papers')->name('papers.manage');

        // Analytics and Logs
        Volt::route('/analytics', 'admin.analytics')->name('analytics');
        Volt::route('/logs', 'admin.logs')->name('logs');
    });

    // Student Routes
    Route::middleware([EnsureUserRole::class . ':student'])->prefix('student')->name('student.')->group(function () {

        Route::get('/dashboard', function () {
            return view('student.dashboard');
        })->name('dashboard');

        Volt::route('/papers', 'student.papers.index')->name('papers.index');
        Volt::route('/papers/search', 'student.papers.search')->name('papers.search');
        Volt::route('/papers/download', 'student.papers.download')->name('papers.download');

        Volt::route('/profile', 'student.profile')->name('profile');
    });

    // Test upload route
    Route::get('/test-upload', function () {
        Storage::disk('public')->put('papers/test.txt', 'Hello World!');
        return "File uploaded to public storage!";
    });
});

// Fallback Route
Route::fallback(function () {
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

require __DIR__.'/auth.php';