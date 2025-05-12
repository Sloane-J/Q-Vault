<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Middleware\EnsureUserRole;

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
            default => redirect()->route('home')->with('error', 'Unauthorized access')
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
    Route::middleware([EnsureUserRole::class . ':admin'])->group(function () {

        Route::get('/admin/dashboard', function () {
            return view('dashboard'); // Admin dashboard view
        })->name('admin.dashboard');

        Volt::route('/admin/users', 'admin.manage.users')->name('admin.users');
        Volt::route('/admin/departments', 'admin.manage.departments')->name('admin.departments');
        Volt::route('/admin/papers', 'admin.manage.papers')->name('admin.papers');

        // View-Based Department Page (not Livewire)
        Route::get('/admin/department', function () {
            return view('admin.department');
        })->name('admin.department.view');

        // Analytics and Logs
        Volt::route('/admin/analytics', 'admin.analytics')->name('admin.analytics');
        Volt::route('/admin/logs', 'admin.logs')->name('admin.logs');
    });

    // Student Routes
    Route::middleware([EnsureUserRole::class . ':student'])->group(function () {

        Route::get('/student/dashboard', function () {
            return view('student.dashboard');
        })->name('student.dashboard');

        Volt::route('/student/papers', 'student.papers.index')->name('student.papers.index');
        Volt::route('/student/papers/search', 'student.papers.search')->name('student.papers.search');
        Volt::route('/student/papers/download', 'student.papers.download')->name('student.papers.download');

        Volt::route('/student/profile', 'student.profile')->name('student.profile');
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
