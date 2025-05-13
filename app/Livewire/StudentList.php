<?php

namespace App\Livewire;

use App\Models\User; // Assuming your user model handles student data
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\WithPagination;

class StudentList extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'name'; // Default sort column
    public $sortDirection = 'asc'; // Default sort direction
    public $perPage = 10; // Number of items per page

    public function mount()
    {
        // No need to fetch all students here anymore, we'll do it in the students() method
    }

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
        //Reset page after sorting
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[\Livewire\Attributes\Computed]
    public function students()
    {
        return User::where('role', 'student')
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function getLastActivity($userId)
    {
        // Assuming you store last activity in the session with a key related to the user ID
        return Session::get('last_activity_' . $userId, 'N/A');
    }

    public function render()
    {
        return view('livewire.student-list');
    }
}
