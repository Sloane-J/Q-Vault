<?php

namespace App\Livewire\Student;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Paper;
use App\Models\Department;
use App\Models\Level;
use App\Models\Course;
use App\Models\Download;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaperBrowser extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedDepartment = '';
    public $selectedLevel = '';
    public $selectedCourse = '';
    public $selectedYear = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedDepartment' => ['except' => ''],
        'selectedLevel' => ['except' => ''],
        'selectedCourse' => ['except' => ''],
        'selectedYear' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedDepartment()
    {
        $this->resetPage();
        $this->selectedLevel = '';
        $this->selectedCourse = '';
    }

    public function updatingSelectedLevel()
    {
        $this->resetPage();
    }

    public function updatingSelectedCourse()
    {
        $this->resetPage();
    }

    public function updatingSelectedYear()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'selectedDepartment',
            'selectedLevel',
            'selectedCourse',
            'selectedYear'
        ]);
        $this->resetPage();
    }

    public function downloadPaper($paperId)
    {
        $paper = Paper::findOrFail($paperId);
        
        Download::create([
            'user_id' => Auth::id(),
            'paper_id' => $paperId,
            'downloaded_at' => now(),
        ]);

        return Storage::disk('public')->download($paper->file_path, $paper->title . '.pdf');
    }

    public function render()
    {
        $papers = Paper::query()
            ->where('is_visible', 'public')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhereHas('course', function ($courseQuery) {
                          $courseQuery->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->selectedDepartment, function ($query) {
                $query->where('department_id', $this->selectedDepartment);
            })
            ->when($this->selectedLevel, function ($query) {
                $query->where('level_id', $this->selectedLevel);
            })
            ->when($this->selectedCourse, function ($query) {
                $query->where('course_id', $this->selectedCourse);
            })
            ->when($this->selectedYear, function ($query) {
                $query->where('exam_year', $this->selectedYear);
            })
            ->with(['department', 'level', 'user', 'course'])
            ->paginate(12);

        $departments = Department::all();
        
        $levels = Level::when($this->selectedDepartment, function ($query) {
            $query->whereHas('papers', function ($q) {
                $q->where('department_id', $this->selectedDepartment);
            });
        })->get();

        $courses = Course::when($this->selectedDepartment, function ($query) {
            $query->where('department_id', $this->selectedDepartment);
        })
        ->when($this->selectedLevel, function ($query) {
            $query->whereHas('papers', function ($q) {
                $q->where('level_id', $this->selectedLevel);
            });
        })->get();

        $availableYears = Paper::when($this->selectedDepartment, function ($query) {
            $query->where('department_id', $this->selectedDepartment);
        })
        ->when($this->selectedLevel, function ($query) {
            $query->where('level_id', $this->selectedLevel);
        })
        ->when($this->selectedCourse, function ($query) {
            $query->where('course_id', $this->selectedCourse);
        })
        ->distinct()
        ->pluck('exam_year')
        ->filter()
        ->values();

        return view('livewire.student.paper-browser', compact(
            'papers',
            'departments',
            'levels',
            'courses',
            'availableYears'
        ));
    }
}