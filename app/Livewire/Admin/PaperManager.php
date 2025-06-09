<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use App\Models\Paper;
use App\Models\Department;
use App\Models\Course;
use App\Models\Level;
use App\Models\StudentType;

class PaperManager extends Component
{
    use WithFileUploads, WithPagination;

    // Form Properties
    public $paperId, $title, $description, $file, $existingFilePath;
    public $department_id = '', $course_id = '', $semester, $exam_type;
    public $exam_year, $student_type, $level_id, $is_visible = 'public';
    public $filteredLevels = [];
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
    public $studentTypes = ['HND', 'B-Tech', 'Top-up', 'DBS', 'MTech'];
    public $levels = [];
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
        'level_id' => 'required|string|max:10',
        'file' => 'nullable|mimes:pdf|max:10240',
    ];

    protected $validationAttributes = [
        'department_id' => 'department',
        'course_id' => 'course',
        'exam_type' => 'exam type',
        'exam_year' => 'exam year',
        'student_type' => 'student type',
        'level_id' => 'level',
    ];

    public function mount()
    {
        // Initialize properties - all papers are public by default
        $this->showForm = false;
        $this->confirmingDeletion = false;
        $this->is_visible = 'public';
        $this->department_id = '';
        $this->course_id = '';
        $this->filteredCourses = collect();
        $this->filteredLevels = collect();
        
        // Load dropdown data
        $this->loadDropdownData();
        
        // Debug: Log initial data
        \Log::info('PaperManager mounted', [
            'departments_count' => $this->departments->count(),
            'courses_count' => $this->courses->count(),
            'levels_count' => $this->levels->count(),
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

    public function updatedStudentType($value)
    {
        \Log::info('Student type changed', ['student_type' => $value]);
        
        // Reset level selection
        $this->level_id = '';
        $this->resetValidation(['level_id']);
        
        if ($value) {
            // Filter levels by student type
            try {
                $studentType = StudentType::where('name', $value)->first();
                if ($studentType) {
                    $this->filteredLevels = Level::where('student_type_id', $studentType->id)
                        ->orderBy('level_number')
                        ->get();
                } else {
                    $this->filteredLevels = collect();
                }
            } catch (\Exception $e) {
                // Fallback: filter from loaded levels if database query fails
                $this->filteredLevels = $this->levels->filter(function($level) use ($value) {
                    return str_contains($level->name, $value);
                });
            }
        } else {
            $this->filteredLevels = collect();
        }
        
        \Log::info('Filtered levels', [
            'count' => $this->filteredLevels->count(),
            'levels' => $this->filteredLevels->pluck('name')->toArray()
        ]);
    }

    protected function loadDropdownData()
    {
        try {
            // Try to load from database
            $this->departments = Department::orderBy('name')->get();
            $this->courses = Course::orderBy('name')->get();
            $this->levels = Level::orderBy('name')->get();
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

            $this->levels = collect([
                (object)['id' => '100', 'name' => 'Level 100'],
                (object)['id' => '200', 'name' => 'Level 200'],
                (object)['id' => '300', 'name' => 'Level 300'],
                (object)['id' => '400', 'name' => 'Level 400'],
            ]);
        }

        // Generate years range
        $this->years = range(date('Y'), 2010);
        
        \Log::info('Dropdown data loaded', [
            'departments' => $this->departments->count(),
            'courses' => $this->courses->count(),
            'levels' => $this->levels->count(),
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
                    'level_id' => $this->level_id, // Fixed: was $this->level
                    'is_visible' => 'public', // Always set to public
                ]);
                session()->flash('message', 'Paper updated successfully.');
            } else {
                session()->flash('error', 'Paper not found.');
            }
        } else {
            // Create new paper - always public
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
                'level_id' => $this->level_id,
                'is_visible' => 'public', // Always set to public
                'uploaded_by' => auth()->id(),
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
        $this->level_id = $paper->level_id;
        $this->is_visible = 'public'; // Always set to public
        
        // Load filtered courses for the selected department
        if ($this->department_id) {
            $this->updatedDepartmentId($this->department_id);
        }
        
        // Load filtered levels for the selected student type
        if ($this->student_type) {
            $this->updatedStudentType($this->student_type);
        }
        
        $this->showForm = true;
    }

    public function resetForm()
    {
        $this->reset([
            'paperId', 'title', 'description', 'file', 'existingFilePath',
            'department_id', 'course_id', 'semester', 'exam_type',
            'exam_year', 'student_type', 'level_id'
        ]);
        
        $this->filteredCourses = collect();
        $this->filteredLevels = collect();
        $this->is_visible = 'public'; // Always reset to public
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

    // Method to update search and reset pagination
    public function updatedSearch()
    {
        $this->resetPage();
    }

    // Methods to handle filter updates and reset pagination
    public function updatedDepartmentFilter()
    {
        $this->resetPage();
    }

    public function updatedYearFilter()
    {
        $this->resetPage();
    }

    public function updatedLevelFilter()
    {
        $this->resetPage();
    }

    public function updatedExamTypeFilter()
    {
        $this->resetPage();
    }

    public function updatedStudentTypeFilter()
    {
        $this->resetPage();
    }

    public function updatedSemesterFilter()
    {
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
            'levels' => $this->filteredLevels,
            'examTypes' => $this->examTypes,
            'years' => $this->years,
        ]);
    }

    protected function getPapers()
    {
        $query = Paper::with(['department', 'course', 'level'])
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  // Removed the problematic course_name search
                  ->orWhereHas('course', function ($courseQuery) {
                      $courseQuery->where('name', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('department', function ($departmentQuery) {
                      $departmentQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Apply filters
        if (!empty($this->departmentFilter)) {
            $query->where('department_id', $this->departmentFilter);
        }

        if (!empty($this->yearFilter)) {
            $query->where('exam_year', $this->yearFilter);
        }

        if (!empty($this->levelFilter)) {
            $query->where('level_id', $this->levelFilter);
        }

        if (!empty($this->examTypeFilter)) {
            $query->where('exam_type', $this->examTypeFilter);
        }

        if (!empty($this->studentTypeFilter)) {
            $query->where('student_type', $this->studentTypeFilter);
        }

        if (!empty($this->semesterFilter)) {
            $query->where('semester', $this->semesterFilter);
        }

        return $query->paginate(10);
    }
}