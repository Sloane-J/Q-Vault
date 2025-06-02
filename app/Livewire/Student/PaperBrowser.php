<?php

namespace App\Livewire\Student;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Paper;
use App\Models\Department;
use App\Models\StudentType;
use App\Models\Level;
use App\Models\Download;
use Illuminate\Support\Facades\Auth;

class PaperBrowser extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedDepartment = '';
    public $selectedStudentType = '';
    public $selectedLevel = '';
    public $selectedSemester = '';
    public $selectedExamType = '';
    public $selectedYear = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedDepartment' => ['except' => ''],
        'selectedStudentType' => ['except' => ''],
        'selectedLevel' => ['except' => ''],
        'selectedSemester' => ['except' => ''],
        'selectedExamType' => ['except' => ''],
        'selectedYear' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedDepartment()
    {
        $this->resetPage();
        $this->selectedLevel = ''; // Reset level when department changes
    }

    public function updatingSelectedStudentType()
    {
        $this->resetPage();
        $this->selectedLevel = ''; // Reset level when student type changes
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'selectedDepartment',
            'selectedStudentType',
            'selectedLevel',
            'selectedSemester',
            'selectedExamType',
            'selectedYear'
        ]);
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function downloadPaper($paperId)
    {
        $paper = Paper::findOrFail($paperId);
        
        // Record download
        Download::create([
            'user_id' => Auth::id(),
            'paper_id' => $paperId,
            'downloaded_at' => now(),
        ]);

        // Return download response
        return response()->download(storage_path('app/' . $paper->file_path));
    }

    public function render()
    {
        $papers = Paper::query()
            ->where('visibility', true) // Only visible papers
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('course_name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedDepartment, function ($query) {
                $query->where('department_id', $this->selectedDepartment);
            })
            ->when($this->selectedStudentType, function ($query) {
                $query->where('student_type_id', $this->selectedStudentType);
            })
            ->when($this->selectedLevel, function ($query) {
                $query->where('level_id', $this->selectedLevel);
            })
            ->when($this->selectedSemester, function ($query) {
                $query->where('semester', $this->selectedSemester);
            })
            ->when($this->selectedExamType, function ($query) {
                $query->where('exam_type', $this->selectedExamType);
            })
            ->when($this->selectedYear, function ($query) {
                $query->where('exam_year', $this->selectedYear);
            })
            ->with(['department', 'studentType', 'level', 'user'])
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(12);

        $departments = Department::all();
        $studentTypes = StudentType::all();
        $levels = Level::when($this->selectedStudentType, function ($query) {
            $query->where('student_type_id', $this->selectedStudentType);
        })->get();

        $availableYears = Paper::distinct()->pluck('exam_year')->filter()->sort()->values();
        $semesters = ['First Semester', 'Second Semester'];
        $examTypes = ['Mid-Semester', 'End-of-Semester', 'Resit', 'Supplementary'];

        return view('livewire.student.papers.paper-browser', compact(
            'papers',
            'departments',
            'studentTypes',
            'levels',
            'availableYears',
            'semesters',
            'examTypes'
        ));
    }
}