<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;
use App\Http\Middleware\EnsureUserRole;
use App\Livewire\Admin\PaperManager;
use App\Livewire\Admin\PaperUploader;
use App\Livewire\Student\DownloadHistory;
use App\Livewire\Admin\PaperVersions;
use App\Models\Paper;
use App\Livewire\Student\PaperBrowser;

// --- Public Routes ---
Route::get('/', function () {
    return view('welcome');
})->name('home');

// --- Authenticated & Verified Routes ---
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        $user = auth()->user();

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            default => redirect()->route('home')->with('error', 'Unauthorized access'),
        };
    })->name('dashboard');

    // User Settings Routes (using Volt)
    Route::prefix('settings')
        ->name('settings.')
        ->group(function () {
            Route::redirect('/', 'profile'); 
            Volt::route('/profile', 'settings.profile')->name('profile');
            Volt::route('/password', 'settings.password')->name('password');
            Volt::route('/appearance', 'settings.appearance')->name('appearance');
        });

    // --- Admin Routes ---
    Route::middleware([EnsureUserRole::class . ':admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
            Route::get('/analytics', fn() => view('admin.analytics'))->name('analytics');
            Route::get('/analytics-details', fn() => view('admin.analytics-details'))->name('analytics-details');
            Route::get('/courses', fn() => view('admin.courses'))->name('courses');
            Route::get('/departments', fn() => view('admin.department'))->name('departments');
            Route::get('/logs', fn() => view('admin.logs'))->name('logs');

            // Paper Management
            Route::prefix('papers')
                ->name('papers.')
                ->group(function () {
                    Route::get('/', PaperManager::class)->name('index');
                    Route::get('/upload', PaperUploader::class)->name('upload');
                    Route::get('/{paper}/versions', PaperVersions::class)->name('versions');
                });
        });

    // --- Student Routes ---
    Route::middleware([EnsureUserRole::class . ':student'])
        ->prefix('student')
        ->name('student.')
        ->group(function () {

            Route::get('/dashboard', function () {
                return view('dashboard');
            })->name('dashboard');

            Route::get('/download-history', DownloadHistory::class)->name('download.history');

            Route::get('/browse-papers', PaperBrowser::class)->name('paper-browser');
        });
});

// --- Test Route (for development/debugging) ---
// Consider removing this in production.
Route::get('/test-upload', function () {
    Storage::disk('public')->put('papers/test.txt', 'Hello World!');
    return 'File uploaded to public storage!';
});

// --- Fallback Route ---
Route::fallback(function () {
    if (auth()->check()) {
        \Log::warning('Unauthorized route access', [
            'user_id' => auth()->id(),
            'email' => auth()->user()->email,
            'role' => auth()->user()->role,
            'attempted_route' => request()->fullUrl(),
        ]);
        return redirect()->route('dashboard')->with('error', 'Unauthorized access');
    }
    // If not authenticated, redirect to login or home
    return redirect()->route('home')->with('error', 'Please log in to access this page.');
});

// --- Authentication Routes ---
require __DIR__ . '/auth.php';