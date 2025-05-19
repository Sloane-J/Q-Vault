<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Paper;
use App\Models\Course;
use App\Models\Department;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaperManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    // Form Inputs
    public $title;
    public $department_id;
    public $course_id;
    public $level;
    public $exam_type;
    public $exam_year;
    public $student_type;
    public $semester;
    public $visibility = 'public';
    public $file;
    
    // Edit Mode
    public $showForm = false; // Controls visibility of the form
    public $editMode = false; // Indicates if we are in edit mode
    public $paperId; // Stores the ID of the paper being edited
    
    // Filters
    public $search = '';
    public $departmentFilter = '';
    public $yearFilter = '';
    public $levelFilter = '';
    public $examTypeFilter = '';
    public $studentTypeFilter = '';
    public $semesterFilter = '';

    // Data for dropdowns (make these public to be accessible in the view if directly used,
    // otherwise, pass them from the render method)
    public $departments = [];
    public $courses = [];
    public $levels = [];
    public $examTypes = [];
    public $years = [];
    public $studentTypes = [];
    
    protected $rules = [
        'title' => 'required|string|max:255',
        'department_id' => 'required|exists:departments,id',
        'course_id' => 'required|exists:courses,id',
        'level' => 'required|string',
        'exam_type' => 'required|string',
        'exam_year' => 'required|integer|min:2000|max:2099',
        'student_type' => 'required|string',
        'semester' => 'required|integer|min:1|max:2',
        'visibility' => 'required|in:public,restricted',
        'file' => 'nullable|file|mimes:pdf|max:10240', // File is nullable for edit mode
    ];
    
    protected $validationAttributes = [
        'department_id' => 'department',
        'course_id' => 'course',
    ];
    
    // Listen for changes on department_id to update courses
    public function updatedDepartmentId($value)
    {
        $this->courses = Course::where('department_id', $value)->orderBy('name')->get();
        $this->course_id = null; // Reset course selection when department changes
    }

    public function mount()
    {
        $this->loadDropdownData();
    }

    private function loadDropdownData()
    {
        $this->departments = Department::orderBy('name')->get();
        // Courses are loaded based on department_id via updatedDepartmentId
        $this->levels = ['100', '200', '300', '400', '500', '600']; // Example levels
        $this->examTypes = ['Mid-term', 'End of Semester', 'Special Resit']; // Example types
        $this->studentTypes = ['Regular', 'Distance', 'Mature']; // Example types
        $this->years = range(date('Y'), 2000); // Generate years from current year down to 2000
    }
    
    public function render()
    {
        $query = Paper::query()
            ->when($this->search, function ($q) {
                return $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhereHas('course', function($query) {
                        $query->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('department', function($query) {
                        $query->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->departmentFilter, fn ($q) => $q->where('department_id', $this->departmentFilter))
            ->when($this->yearFilter, fn ($q) => $q->where('exam_year', $this->yearFilter))
            ->when($this->levelFilter, fn ($q) => $q->where('level', $this->levelFilter))
            ->when($this->examTypeFilter, fn ($q) => $q->where('exam_type', $this->examTypeFilter))
            ->when($this->studentTypeFilter, fn ($q) => $q->where('student_type', $this->studentTypeFilter))
            ->when($this->semesterFilter, fn ($q) => $q->where('semester', $this->semesterFilter));
            
        $papers = $query->with(['department', 'course'])->latest()->paginate(10);
        
        return view('livewire.admin.paper-manager', [
            'papers' => $papers,
            // Pass all necessary dropdown data to the view
            'departments' => $this->departments,
            'courses' => $this->courses, // This will be dynamic based on department_id
            'years' => $this->years,
            'levels' => $this->levels,
            'examTypes' => $this->examTypes,
            'studentTypes' => $this->studentTypes,
        ]);
    }
    
    public function savePaper()
    {
        // Adjust rules for creation vs. update
        $rules = $this->rules;
        if (!$this->editMode) {
            $rules['file'] = 'required|file|mimes:pdf|max:10240'; // File is required on creation
        }

        $this->validate($rules);
        
        if ($this->editMode) {
            $paper = Paper::findOrFail($this->paperId);
            
            // Process file if a new one was uploaded
            if ($this->file) {
                // Delete old file
                if ($paper->file_path && Storage::disk('public')->exists($paper->file_path)) {
                    Storage::disk('public')->delete($paper->file_path);
                }
                
                // Store new file
                $fileName = time() . '_' . Str::slug($this->title) . '.' . $this->file->getClientOriginalExtension();
                $filePath = $this->file->storeAs('papers', $fileName, 'public');
                $paper->file_path = $filePath;
            }
            
            // Update paper details
            $paper->title = $this->title;
            $paper->department_id = $this->department_id;
            $paper->course_id = $this->course_id;
            // Ensure course_name is updated if course_id changes, or remove if not needed in DB
            $paper->course_name = Course::find($this->course_id)->name;
            $paper->level = $this->level;
            $paper->exam_type = $this->exam_type;
            $paper->exam_year = $this->exam_year;
            $paper->student_type = $this->student_type;
            $paper->semester = $this->semester;
            $paper->visibility = $this->visibility;
            $paper->save();
            
            session()->flash('message', 'Paper successfully updated!');

        } else {
            // Process and store the file (required for new paper)
            $fileName = time() . '_' . Str::slug($this->title) . '.' . $this->file->getClientOriginalExtension();
            $filePath = $this->file->storeAs('papers', $fileName, 'public');
            
            // Create paper record
            Paper::create([
                'title' => $this->title,
                'department_id' => $this->department_id,
                'course_id' => $this->course_id,
                'course_name' => Course::find($this->course_id)->name, // Ensure course_name is captured
                'level' => $this->level,
                'exam_type' => $this->exam_type,
                'exam_year' => $this->exam_year,
                'student_type' => $this->student_type,
                'semester' => $this->semester,
                'visibility' => $this->visibility,
                'file_path' => $filePath,
                'uploaded_by' => auth()->id(),
            ]);
            
            session()->flash('message', 'Paper successfully uploaded!');
        }
        
        $this->resetForm();
    }
    
    public function editPaper($id)
    {
        $this->resetValidation();
        $this->editMode = true;
        $this->showForm = true; // Show the form when editing
        $this->paperId = $id;
        
        $paper = Paper::findOrFail($id);
        
        $this->title = $paper->title;
        $this->department_id = $paper->department_id;
        // Trigger the updatedDepartmentId method to populate courses
        $this->updatedDepartmentId($paper->department_id); 
        $this->course_id = $paper->course_id;
        $this->level = $paper->level;
        $this->exam_type = $paper->exam_type;
        $this->exam_year = $paper->exam_year;
        $this->student_type = $paper->student_type;
        $this->semester = $paper->semester;
        $this->visibility = $paper->visibility;
        $this->file = null; // Clear the file input in edit mode
    }
    
    public function deletePaper($id)
    {
        $paper = Paper::findOrFail($id);
        
        // Delete file from storage
        if ($paper->file_path && Storage::disk('public')->exists($paper->file_path)) {
            Storage::disk('public')->delete($paper->file_path);
        }
        
        // Delete record
        $paper->delete();
        
        session()->flash('message', 'Paper successfully deleted!');
    }
    
    public function cancelEdit()
    {
        $this->resetForm();
    }
    
    public function resetForm()
    {
        $this->reset([
            'title', 'department_id', 'course_id', 'level', 'exam_type',
            'exam_year', 'student_type', 'semester', 'visibility', 'file',
            'editMode', 'paperId', 'showForm' // Reset showForm as well
        ]);
        $this->resetValidation();
        // Reload dropdown data to ensure fresh state, especially for courses
        $this->loadDropdownData();
    }
    
    public function resetFilters()
    {
        $this->reset([
            'search', 'departmentFilter', 'yearFilter', 'levelFilter',
            'examTypeFilter', 'studentTypeFilter', 'semesterFilter'
        ]);
        $this->resetPage(); // Reset pagination when filters are reset
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) {
            $this->resetForm(); // Reset form when hiding it
        }
    }
}