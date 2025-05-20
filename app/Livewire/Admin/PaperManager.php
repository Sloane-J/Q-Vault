<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use App\Models\Paper;
use App\Models\Department;
use App\Models\Course;
use Illuminate\Pagination\LengthAwarePaginator;

class PaperManager extends Component
{
    use WithFileUploads, WithPagination;

    // Form Properties
    public $paperId, $title, $description, $file, $existingFilePath;
    public $department_id, $semester, $exam_type, $course_name;
    public $exam_year, $student_type, $level, $visibility = 'public';
    public $showForm = false;

    // Filters
    public $search = '';
    public $departmentFilter = '';
    public $yearFilter = '';
    public $levelFilter = '';
    public $examTypeFilter = '';
    public $studentTypeFilter = '';
    public $semesterFilter = '';

    // Modal
    public $confirmingDeletion = false;
    public $paperIdToDelete;

    // Dropdown Data
    public $departments = [];
    public $courses = [];
    public $studentTypes = ['HND', 'B-Tech', 'Top-up'];
    public $levels = ['100', '200', '300', '400'];
    public $examTypes = ['Mid-Term', 'End of Semester', 'Resit'];
    public $years = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'department_id' => 'required|exists:departments,id',
        'semester' => 'required|in:1,2',
        'exam_type' => 'required|string|max:50',
        'course_name' => 'required|string|max:255',
        'exam_year' => 'required|integer|min:1900|max:2100',
        'student_type' => 'required|string|max:50',
        'level' => 'required|string|max:10',
        'visibility' => 'required|in:public,restricted',
        'file' => 'nullable|mimes:pdf|max:10240',
    ];

    protected $validationAttributes = [
        'department_id' => 'department',
        'course_name' => 'course name',
        'exam_type' => 'exam type',
        'exam_year' => 'exam year',
        'student_type' => 'student type',
    ];

    public function mount()
    {
        // Explicitly initialize properties
        $this->showForm = false;
        $this->confirmingDeletion = false;
        $this->visibility = 'public';
        
        // Load dropdown data
        $this->loadDropdownData();
    }

    protected function loadDropdownData()
    {
        // Ideally, fetch from DB
        $this->departments = Department::all();
        $this->courses = Course::all();

        // Fallback static data (if needed)
        if ($this->departments->isEmpty()) {
            $this->departments = collect([
                (object)['id' => 1, 'name' => 'Computer Science'],
                (object)['id' => 2, 'name' => 'Electrical Engineering'],
            ]);
        }

        if ($this->courses->isEmpty()) {
            $this->courses = collect([
                (object)['id' => 1, 'name' => 'Data Structures'],
                (object)['id' => 2, 'name' => 'Circuit Analysis'],
            ]);
        }

        $this->years = range(date('Y'), 2000);
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) {
            $this->resetForm();
        }
    }

    public function savePaper()
    {
        $this->validate();

        $filePath = $this->existingFilePath;

        if ($this->file) {
            if ($this->paperId && $this->existingFilePath) {
                Storage::delete($this->existingFilePath);
            }
            $filePath = $this->file->store('papers', 'public');
        } elseif (!$this->paperId) {
            session()->flash('error', 'PDF file is required for new papers.');
            return;
        }

        if ($this->paperId) {
            $paper = Paper::find($this->paperId);
            if ($paper) {
                $paper->update([
                    'title' => $this->title,
                    'description' => $this->description,
                    'file_path' => $filePath,
                    'department_id' => $this->department_id,
                    'semester' => $this->semester,
                    'exam_type' => $this->exam_type,
                    'course_name' => $this->course_name,
                    'exam_year' => $this->exam_year,
                    'student_type' => $this->student_type,
                    'level' => $this->level,
                    'visibility' => $this->visibility,
                ]);
                session()->flash('message', 'Paper updated successfully.');
            } else {
                session()->flash('error', 'Paper not found.');
            }
        } else {
            // Add user_id to new papers
            Paper::create([
                'title' => $this->title,
                'description' => $this->description,
                'file_path' => $filePath,
                'department_id' => $this->department_id,
                'semester' => $this->semester,
                'exam_type' => $this->exam_type,
                'course_name' => $this->course_name,
                'exam_year' => $this->exam_year,
                'student_type' => $this->student_type,
                'level' => $this->level,
                'visibility' => $this->visibility,
                'user_id' => auth()->id(),
            ]);
            session()->flash('message', 'Paper uploaded successfully.');
        }

        $this->resetForm();
    }

    public function editPaper($paperId)
    {
        $paper = Paper::findOrFail($paperId);

        $this->paperId = $paper->id;
        $this->title = $paper->title;
        $this->description = $paper->description;
        $this->existingFilePath = $paper->file_path;
        $this->department_id = $paper->department_id;
        $this->semester = $paper->semester;
        $this->exam_type = $paper->exam_type;
        $this->course_name = $paper->course_name;
        $this->exam_year = $paper->exam_year;
        $this->student_type = $paper->student_type;
        $this->level = $paper->level;
        $this->visibility = $paper->visibility;
        
        // Ensure this is set to true to show the form
        $this->showForm = true;
    }

    public function resetForm()
    {
        $this->reset([
            'paperId', 'title', 'description', 'file', 'existingFilePath',
            'department_id', 'semester', 'exam_type', 'course_name',
            'exam_year', 'student_type', 'level'
        ]);
        
        // Don't reset showForm here, as it controls UI state
        // Default values
        $this->visibility = 'public';
        
        $this->resetValidation();
    }

    public function confirmDelete($paperId)
    {
        $this->paperIdToDelete = $paperId;
        $this->confirmingDeletion = true;
    }

    public function deletePaper()
    {
        if ($this->confirmingDeletion && $this->paperIdToDelete) {
            $paper = Paper::find($this->paperIdToDelete);
            if ($paper) {
                if ($paper->file_path) {
                    Storage::delete($paper->file_path);
                }
                $paper->delete();

                session()->flash('message', 'Paper deleted successfully.');
            }
            $this->confirmingDeletion = false;
            $this->paperIdToDelete = null;
        }
    }

    public function resetFilters()
    {
        $this->reset([
            'search', 'departmentFilter', 'yearFilter', 'levelFilter',
            'examTypeFilter', 'studentTypeFilter', 'semesterFilter'
        ]);
        $this->resetPage();
    }

    public function render()
    {
        // For development, use dummy data
        // In production, replace with actual DB queries
        $papers = $this->getPapers();

        return view('livewire.admin.paper-manager', [
            'papers' => $papers,
            'departments' => $this->departments,
            'courses' => $this->courses,
            'studentTypes' => $this->studentTypes,
            'levels' => $this->levels,
            'examTypes' => $this->examTypes,
            'years' => $this->years,
        ]);
    }

    protected function getPapers()
    {
        // In production, replace with actual DB query
        // For now, using dummy data for development
        $dummyPapers = collect([
            (object)[
                'id' => 1, 'title' => 'Intro to Algorithms', 'description' => 'Fundamentals',
                'file_path' => 'papers/dummy-algo.pdf', 'department_id' => 1, 'semester' => 1,
                'exam_type' => 'End of Semester', 'course_name' => 'Algorithms', 'exam_year' => 2023,
                'student_type' => 'B-Tech', 'level' => '300', 'visibility' => 'public',
                'department' => (object)['name' => 'Computer Science'], 'course' => (object)['name' => 'Algorithms'],
            ],
            (object)[
                'id' => 2, 'title' => 'Digital Electronics', 'description' => 'Logic gates',
                'file_path' => 'papers/dummy-digital.pdf', 'department_id' => 2, 'semester' => 2,
                'exam_type' => 'Mid-Term', 'course_name' => 'Digital Systems', 'exam_year' => 2022,
                'student_type' => 'HND', 'level' => '200', 'visibility' => 'restricted',
                'department' => (object)['name' => 'Electrical Engineering'], 'course' => (object)['name' => 'Digital Systems'],
            ]
        ]);

        $filtered = $dummyPapers->filter(function ($paper) {
            return (empty($this->search) ||
                        stripos($paper->title, $this->search) !== false ||
                        stripos($paper->description, $this->search) !== false ||
                        stripos($paper->course_name, $this->search) !== false) &&
                   (empty($this->departmentFilter) || $paper->department_id == $this->departmentFilter) &&
                   (empty($this->yearFilter) || $paper->exam_year == $this->yearFilter) &&
                   (empty($this->levelFilter) || $paper->level == $this->levelFilter) &&
                   (empty($this->examTypeFilter) || $paper->exam_type == $this->examTypeFilter) &&
                   (empty($this->studentTypeFilter) || $paper->student_type == $this->studentTypeFilter) &&
                   (empty($this->semesterFilter) || $paper->semester == $this->semesterFilter);
        });

        $perPage = 10;
        $page = $this->page ?? 1;
        $paged = $filtered->slice(($page - 1) * $perPage, $perPage)->values();

        $papers = new LengthAwarePaginator(
            $paged,
            $filtered->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return $papers;
    }
}