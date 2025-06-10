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
    public $department_id = '',
        $course_id = '',
        $semester,
        $exam_type;
    public $exam_year,
        $student_type,
        $level_id,
        $is_visible = 'public';
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
    public $studentTypes = [];
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
        'level_id' => 'required|integer|exists:levels,id', 
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
        $this->studentTypes = array_values(StudentType::getTypes());

        // Load dropdown data
        $this->loadDropdownData();

        // Debug: Log initial data
        \Log::info('PaperManager mounted', [
            'departments_count' => $this->departments->count(),
            'courses_count' => $this->courses->count(),
            'levels_count' => $this->levels->count(),
            'studentTypes' => $this->studentTypes,
        ]);
    }

    public function updatedDepartmentId($value)
    {
        // Debug logging
        \Log::info('Department changed', ['department_id' => $value]);

        $this->course_id = '';
        $this->resetValidation(['course_id']);

        if ($value) {
            try {
                $this->filteredCourses = Course::where('department_id', (int) $value)->orderBy('name')->get();
            } catch (\Exception $e) {
                // Fallback: If Course model doesn't exist or DB error, use dummy data
                \Log::error("Error fetching courses for department ID $value: " . $e->getMessage());
                $this->filteredCourses = collect([
                    (object) ['id' => 1, 'name' => 'Data Structures', 'department_id' => 1],
                    (object) ['id' => 2, 'name' => 'Algorithms', 'department_id' => 1],
                    (object) ['id' => 3, 'name' => 'Circuit Analysis', 'department_id' => 2],
                    (object) ['id' => 4, 'name' => 'Digital Electronics', 'department_id' => 2]
                ])->where('department_id', (int) $value);
            }
        } else {
            $this->filteredCourses = collect();
        }

        // Debug logging
        \Log::info('Filtered courses', [
            'count' => $this->filteredCourses->count(),
            'courses' => $this->filteredCourses->pluck('name')->toArray(),
        ]);
    }

    public function updatedCourseId($value)
    {
        \Log::info('Course changed', ['course_id' => $value]);

        if ($value && !$this->department_id) {
            try {
                $course = Course::find((int) $value);
                if ($course) {
                    $this->department_id = $course->department_id;
                    $this->updatedDepartmentId($course->department_id); 
                }
            } catch (\Exception $e) {
                \Log::error("Error fetching course for ID $value: " . $e->getMessage());
                // Fallback to dummy course data if DB is down
                $course = collect([(object) ['id' => 1, 'name' => 'Data Structures', 'department_id' => 1], (object) ['id' => 2, 'name' => 'Algorithms', 'department_id' => 1], (object) ['id' => 3, 'name' => 'Circuit Analysis', 'department_id' => 2], (object) ['id' => 4, 'name' => 'Digital Electronics', 'department_id' => 2]])->firstWhere('id', (int) $value);
                if ($course && isset($course->department_id)) {
                    $this->department_id = $course->department_id;
                    $this->updatedDepartmentId($course->department_id);
                }
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
            $studentTypeModel = StudentType::where('name', $value)->first();

            if ($studentTypeModel) {
                $this->filteredLevels = Level::where('student_type_id', $studentTypeModel->id)
                                            ->orderBy('level_number') // Order for logical display
                                            ->get();
            } else {
                // If student type not found in DB, clear levels
                $this->filteredLevels = collect();
            }
        } else {
            $this->filteredLevels = collect(); // Clear levels if no student type selected
        }

        \Log::info('Filtered levels', [
            'count' => $this->filteredLevels->count(),
            'levels' => $this->filteredLevels->pluck('name')->toArray(),
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
            \Log::info('Using dummy data for dropdowns due to exception: ' . $e->getMessage());

            $this->departments = collect([(object) ['id' => 1, 'name' => 'Computer Science'], (object) ['id' => 2, 'name' => 'Electrical Engineering'], (object) ['id' => 3, 'name' => 'Mechanical Engineering']]);

            $this->courses = collect([(object) ['id' => 1, 'name' => 'Data Structures', 'department_id' => 1], (object) ['id' => 2, 'name' => 'Algorithms', 'department_id' => 1], (object) ['id' => 3, 'name' => 'Circuit Analysis', 'department_id' => 2], (object) ['id' => 4, 'name' => 'Digital Electronics', 'department_id' => 2], (object) ['id' => 5, 'name' => 'Thermodynamics', 'department_id' => 3], (object) ['id' => 6, 'name' => 'Fluid Mechanics', 'department_id' => 3]]);

            $allLevels = collect();
            $dummyLevelIdCounter = 1; 

            foreach (StudentType::getTypes() as $studentTypeKey => $studentTypeName) {
                $levelsByNumber = StudentType::getLevels($studentTypeKey); // Returns [100, 200, ...]

                foreach ($levelsByNumber as $levelNumber) {
                    $allLevels->push((object) [
                        'id' => $dummyLevelIdCounter++, 
                        'name' => "$studentTypeName Level $levelNumber", 
                        'student_type_id' => $studentTypeKey, 
                        'level_number' => $levelNumber,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            $this->levels = $allLevels->sortBy('id'); 
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
        // Add a dd() here to confirm what `level_id` is before validation
        //dd($this->level_id); 

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
        $course = null;
        try {
            $course = Course::find((int) $this->course_id);
        } catch (\Exception $e) {
            \Log::error("Error fetching course for course_id {$this->course_id} in savePaper: " . $e->getMessage());
            // Fallback for dummy data or if DB is down
            $course = collect([(object) ['id' => 1, 'name' => 'Data Structures'], (object) ['id' => 2, 'name' => 'Algorithms']])->firstWhere('id', (int) $this->course_id);
        }
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
                    'level_id' => $this->level_id,
                    'is_visible' => 'public',
                ]);
                session()->flash('message', 'Paper updated successfully.');
            } else {
                session()->flash('error', 'Paper not found.');
            }
        } else {
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
                'is_visible' => 'public',
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

        if ($this->department_id) {
            $this->updatedDepartmentId($this->department_id);
        }

        if ($this->student_type) {
            $this->updatedStudentType($this->student_type);
        }

        $this->showForm = true;
    }

    public function resetForm()
    {
        $this->reset(['paperId', 'title', 'description', 'file', 'existingFilePath', 'department_id', 'course_id', 'semester', 'exam_type', 'exam_year', 'student_type', 'level_id']);

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
        $this->reset(['search', 'departmentFilter', 'yearFilter', 'levelFilter', 'examTypeFilter', 'studentTypeFilter', 'semesterFilter']);
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

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
        $query = Paper::with(['department', 'course', 'level'])->orderBy('created_at', 'desc');

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
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