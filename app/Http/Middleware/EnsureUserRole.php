<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        // Ensure user is authenticated
        if (!auth()->check()) {
            return redirect('login');
        }

        // Check if user has the required role
        if (auth()->user()->role !== $role) {
            // Redirect based on user's actual role
            return match(auth()->user()->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'student' => redirect()->route('student.dashboard'),
                default => redirect()->route('dashboard')
            };
        }

        return $next($request);
    }
}