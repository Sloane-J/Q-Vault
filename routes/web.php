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

        Route::get('/department', function () {
            return view('admin.department');
        })->name('department.view');

        // Paper Management Routes
        Route::prefix('papers')->name('papers.')->group(function () {
            // Main papers view/listing
            Route::get('/', PaperManager::class)->name('index');
            
            // Paper uploader
            Route::get('/upload', PaperUploader::class)->name('upload');
            
            // Paper management
            Route::get('/paper-manager', function () {
                return view('livewire.admin.paper-manager');
            })->name('paper-manager');
            
            // Paper versions management
            Route::get('/versions', function () {
                return view('livewire.admin.papers.paper-versions');
            })->name('versions'); 

            Route::get('/papers/{paper}/versions', function ($paper) {
                return view('papers.papers.versions', ['paperId' => $paper]);
            })->middleware(['auth'])->name('papers.versions');
            
            // Paper viewing with model binding
            Route::get('/{paper}/view', function (Paper $paper) {
                return view('admin.paper-manager', ['paper' => $paper]);
            })->name('view');
        });

        // Course management
        Route::get('/courses', function() {
            return view('admin.courses');
        })->name('courses');

        Route::get('/departments', function () {
            return view('admin.department');
        })->name('departments');

        // Analytics and Logs
        Volt::route('/analytics', 'admin.analytics')->name('analytics');
        Volt::route('/logs', 'admin.logs')->name('logs');
    });

    // Student Routes
    Route::middleware([EnsureUserRole::class . ':student'])->prefix('student')->name('student.')->group(function () {

        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');

        // Student paper routes
        Route::prefix('papers')->name('papers.')->group(function () {
            Volt::route('/', 'student.papers.index')->name('index');
            Volt::route('/search', 'student.papers.search')->name('search');
            Volt::route('/download', 'student.papers.download')->name('download');
            
            Route::get('/browse', function () {
                return view('livewire.student.papers.browse');
            })->name('browse');
        });
        
        // Download history
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
            'attempted_route' => request()->fullUrl()
        ]);
    }

    return redirect()->route('dashboard')->with('error', 'Unauthorized access');
});

require __DIR__.'/auth.php';