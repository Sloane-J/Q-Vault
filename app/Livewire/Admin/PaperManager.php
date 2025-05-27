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
    public $department_id = '', $course_id = '', $semester, $exam_type;
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
    public $filteredCourses = [];
    public $studentTypes = ['HND', 'B-Tech', 'Top-up'];
    public $levels = ['100', '200', '300', '400'];
    public $examTypes = ['End of Semester', 'Resit'];
    public $years = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'department_id' => 'required',
        'course_id' => 'required',
        'semester' => 'required|in:1,2',
        'exam_type' => 'required|string|max:50',
        'exam_year' => 'required|integer|min:1900|max:2100',
        'student_type' => 'required|string|max:50',
        'level' => 'required|string|max:10',
        'visibility' => 'required|in:public,restricted',
        'file' => 'nullable|mimes:pdf|max:10240',
    ];

    protected $validationAttributes = [
        'department_id' => 'department',
        'course_id' => 'course',
        'exam_type' => 'exam type',
        'exam_year' => 'exam year',
        'student_type' => 'student type',
    ];

    public function mount()
    {
        // Initialize properties
        $this->showForm = false;
        $this->confirmingDeletion = false;
        $this->visibility = 'public';
        $this->department_id = '';
        $this->course_id = '';
        $this->filteredCourses = collect();
        
        // Load dropdown data
        $this->loadDropdownData();
        
        // Debug: Log initial data
        \Log::info('PaperManager mounted', [
            'departments_count' => $this->departments->count(),
            'courses_count' => $this->courses->count()
        ]);
    }

    public function updatedDepartmentId($value)
    {
        // Debug logging
        \Log::info('Department changed', ['department_id' => $value]);
        
        // Reset course selection
        $this->course_id = '';
        $this->resetValidation(['course_id']);
        
        if ($value) {
            // Filter courses by department
            if ($this->courses->isNotEmpty()) {
                $this->filteredCourses = $this->courses->where('department_id', (int)$value);
            } else {
                // Fallback: Try to load from database
                try {
                    $this->filteredCourses = Course::where('department_id', $value)
                        ->orderBy('name')
                        ->get();
                } catch (\Exception $e) {
                    // If Course model doesn't exist, use dummy data
                    $this->filteredCourses = collect([
                        (object)['id' => 1, 'name' => 'Data Structures', 'department_id' => 1],
                        (object)['id' => 2, 'name' => 'Algorithms', 'department_id' => 1],
                        (object)['id' => 3, 'name' => 'Circuit Analysis', 'department_id' => 2],
                        (object)['id' => 4, 'name' => 'Digital Electronics', 'department_id' => 2],
                    ])->where('department_id', (int)$value);
                }
            }
        } else {
            $this->filteredCourses = collect();
        }
        
        // Debug logging
        \Log::info('Filtered courses', [
            'count' => $this->filteredCourses->count(),
            'courses' => $this->filteredCourses->pluck('name')->toArray()
        ]);
    }

    public function updatedCourseId($value)
    {
        \Log::info('Course changed', ['course_id' => $value]);
        
        // If a course is selected and no department is selected,
        // automatically select the department of that course
        if ($value && !$this->department_id) {
            $course = $this->courses->firstWhere('id', (int)$value);
            if ($course && isset($course->department_id)) {
                $this->department_id = $course->department_id;
                $this->updatedDepartmentId($course->department_id);
            }
        }
    }

    protected function loadDropdownData()
    {
        try {
            // Try to load from database
            $this->departments = Department::orderBy('name')->get();
            $this->courses = Course::orderBy('name')->get();
        } catch (\Exception $e) {
            // Fallback to dummy data for development
            \Log::info('Using dummy data for dropdowns');
            
            $this->departments = collect([
                (object)['id' => 1, 'name' => 'Computer Science'],
                (object)['id' => 2, 'name' => 'Electrical Engineering'],
                (object)['id' => 3, 'name' => 'Mechanical Engineering'],
            ]);

            $this->courses = collect([
                (object)['id' => 1, 'name' => 'Data Structures', 'department_id' => 1],
                (object)['id' => 2, 'name' => 'Algorithms', 'department_id' => 1],
                (object)['id' => 3, 'name' => 'Circuit Analysis', 'department_id' => 2],
                (object)['id' => 4, 'name' => 'Digital Electronics', 'department_id' => 2],
                (object)['id' => 5, 'name' => 'Thermodynamics', 'department_id' => 3],
                (object)['id' => 6, 'name' => 'Fluid Mechanics', 'department_id' => 3],
            ]);
        }

        // Generate years range
        $this->years = range(date('Y'), 2000);
        
        \Log::info('Dropdown data loaded', [
            'departments' => $this->departments->count(),
            'courses' => $this->courses->count()
        ]);
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

        // Get course name for storage
        $course = $this->courses->firstWhere('id', (int)$this->course_id);
        $courseName = $course ? $course->name : '';

        if ($this->paperId) {
            $paper = Paper::find($this->paperId);
            if ($paper) {
                $paper->update([
                    'title' => $this->title,
                    'description' => $this->description,
                    'file_path' => $filePath,
                    'department_id' => $this->department_id,
                    'course_id' => $this->course_id,
                    'course_name' => $courseName,
                    'semester' => $this->semester,
                    'exam_type' => $this->exam_type,
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
            // Create new paper
            Paper::create([
                'title' => $this->title,
                'description' => $this->description,
                'file_path' => $filePath,
                'department_id' => $this->department_id,
                'course_id' => $this->course_id,
                'course_name' => $courseName,
                'semester' => $this->semester,
                'exam_type' => $this->exam_type,
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
        $this->course_id = $paper->course_id ?? null;
        $this->semester = $paper->semester;
        $this->exam_type = $paper->exam_type;
        $this->exam_year = $paper->exam_year;
        $this->student_type = $paper->student_type;
        $this->level = $paper->level;
        $this->visibility = $paper->visibility;
        
        // Load filtered courses for the selected department
        if ($this->department_id) {
            $this->updatedDepartmentId($this->department_id);
        }
        
        $this->showForm = true;
    }

    public function resetForm()
    {
        $this->reset([
            'paperId', 'title', 'description', 'file', 'existingFilePath',
            'department_id', 'course_id', 'semester', 'exam_type',
            'exam_year', 'student_type', 'level'
        ]);
        
        $this->filteredCourses = collect();
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
        $papers = $this->getPapers();

        return view('livewire.admin.paper-manager', [
            'papers' => $papers,
            'departments' => $this->departments,
            'courses' => $this->courses,
            'filteredCourses' => $this->filteredCourses,
            'studentTypes' => $this->studentTypes,
            'levels' => $this->levels,
            'examTypes' => $this->examTypes,
            'years' => $this->years,
        ]);
    }

    protected function getPapers()
    {
        // Dummy data for development
        $dummyPapers = collect([
            (object)[
                'id' => 1, 'title' => 'Intro to Algorithms', 'description' => 'Fundamentals',
                'file_path' => 'papers/dummy-algo.pdf', 'department_id' => 1, 'course_id' => 2,
                'semester' => 1, 'exam_type' => 'End of Semester', 'course_name' => 'Algorithms', 
                'exam_year' => 2023, 'student_type' => 'B-Tech', 'level' => '300', 'visibility' => 'public',
                'department' => (object)['name' => 'Computer Science'], 
                'course' => (object)['name' => 'Algorithms'],
            ],
            (object)[
                'id' => 2, 'title' => 'Digital Electronics', 'description' => 'Logic gates',
                'file_path' => 'papers/dummy-digital.pdf', 'department_id' => 2, 'course_id' => 4,
                'semester' => 2, 'exam_type' => 'Resit', 'course_name' => 'Digital Electronics', 
                'exam_year' => 2022, 'student_type' => 'HND', 'level' => '200', 'visibility' => 'restricted',
                'department' => (object)['name' => 'Electrical Engineering'], 
                'course' => (object)['name' => 'Digital Electronics'],
            ],
            (object)[
                'id' => 3, 'title' => 'Data Structures Final', 'description' => 'Trees and Graphs',
                'file_path' => 'papers/dummy-ds.pdf', 'department_id' => 1, 'course_id' => 1,
                'semester' => 1, 'exam_type' => 'End of Semester', 'course_name' => 'Data Structures', 
                'exam_year' => 2023, 'student_type' => 'B-Tech', 'level' => '200', 'visibility' => 'public',
                'department' => (object)['name' => 'Computer Science'], 
                'course' => (object)['name' => 'Data Structures'],
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

        return new LengthAwarePaginator(
            $paged,
            $filtered->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }
}