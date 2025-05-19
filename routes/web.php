<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;
use App\Http\Middleware\EnsureUserRole;
use App\Livewire\Admin\PaperManager;
use App\Livewire\Admin\PaperUploader;
use App\Livewire\Student\DownloadHistory;
use App\Models\Paper;

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
            'role' => $user->role,
        ]);

        return match ($user->role) {
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

        Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

        Route::get('/department', fn () => view('admin.department'))->name('department.view');
        Route::get('/departments', fn () => view('admin.department'))->name('departments');

        Route::get('/courses', fn () => view('admin.courses'))->name('courses');

            // Paper Management
            Route::prefix('papers')->name('papers.')->group(function () {
            Route::get('/', PaperManager::class)->name('index');
            Route::get('/paper-manager', fn () => view('livewire.admin.paper-manager'))->name('paper-manager');
            Route::get('/versions', fn () => view('livewire.admin.papers.paper-versions'))->name('versions');

            Route::get('/{paper}/versions', function ($paper) {
                return view('livewire.admin.papers.versions', ['paperId' => $paper]);
            })->name('paper.versions');

            // Route for showing the paper upload form (create)
            Route::get('/paper-uploader', [PaperController::class, 'create'])->name('paper-uploader');

            // Route for storing the uploaded paper (store)
            Route::post('/paper-uploader', [PaperController::class, 'store'])->name('paper-uploader.store');

            Route::get('/{paper}/view', function (Paper $paper) {
                return view('admin.paper-manager', ['paper' => $paper]);
            })->name('view');
        });

        Volt::route('/analytics', 'admin.analytics')->name('analytics');
        Volt::route('/logs', 'admin.logs')->name('logs');
    });

    // Student Routes
    Route::middleware([EnsureUserRole::class . ':student'])->prefix('student')->name('student.')->group(function () {

        Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

        Route::prefix('papers')->name('papers.')->group(function () {
            Volt::route('/', 'student.papers.index')->name('index');
            Volt::route('/search', 'student.papers.search')->name('search');
            Volt::route('/download', 'student.papers.download')->name('download');

            Route::get('/browse', fn () => view('livewire.student.papers.browse'))->name('browse');
        });

        Route::get('/download-history', DownloadHistory::class)->name('download.history');
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
            'attempted_route' => request()->fullUrl(),
        ]);
    }

    return redirect()->route('dashboard')->with('error', 'Unauthorized access');
});

require __DIR__.'/auth.php';
