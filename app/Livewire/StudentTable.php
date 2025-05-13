<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class StudentTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'name';
    public $sortDirection = 'asc';

    protected $listeners = ['deleteStudent', 'refreshStudents' => '$refresh'];

    // Computed properties for stats
    public function getActiveTodayCountProperty()
    {
        return User::where('role', 'student')
            ->whereHas('sessions', function($query) {
                $query->where('last_activity', '>=', now()->subDay()->timestamp);
            })
            ->count();
    }

    public function getNewThisWeekCountProperty()
    {
        return User::where('role', 'student')
            ->where('created_at', '>=', now()->subWeek())
            ->count();
    }

    public function render()
    {
        $students = User::where('role', 'student')
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->with('sessions') // Eager load sessions
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.student-table', [
            'students' => $students,
            'activeTodayCount' => $this->activeTodayCount,
            'newThisWeekCount' => $this->newThisWeekCount
        ]);
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function getLastActivity($studentId)
    {
        $student = User::with('sessions')->find($studentId);
        if ($student && $student->sessions->isNotEmpty()) {
            $latestSession = $student->sessions->sortByDesc('last_activity')->first();
            return Carbon::createFromTimestamp($latestSession->last_activity)->diffForHumans();
        }
        return 'Never';
    }

    public function getLastIpAddress($studentId)
    {
        $student = User::with('sessions')->find($studentId);
        if ($student && $student->sessions->isNotEmpty()) {
            return $student->sessions->sortByDesc('last_activity')->first()->ip_address;
        }
        return 'N/A';
    }

    public function editStudent($studentId)
    {
        $this->dispatch('edit-student', studentId: $studentId);
    }

    public function deleteStudent($studentId)
    {
        try {
            User::destroy($studentId);
            $this->dispatch('notify', 
                type: 'success',
                message: 'Student deleted successfully'
            );
        } catch (\Exception $e) {
            $this->dispatch('notify', 
                type: 'error',
                message: 'Error deleting student: ' . $e->getMessage()
            );
        }
    }

    public function confirmDelete($studentId)
    {
        $this->dispatch('swal:confirm', [
            'type' => 'warning',
            'title' => 'Are you sure?',
            'text' => 'You won\'t be able to revert this!',
            'confirmText' => 'Yes, delete it!',
            'method' => 'deleteStudent',
            'params' => $studentId,
        ]);
    }
}