<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public function redirectToRoleDashboard()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // Default (for students)
        return redirect()->route('student.dashboard');
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}