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
    public $file; // Holds the uploaded file

    // Identifies the paper being edited. Null for creation.
    public $paperId = null; 
    
    // UI state
    public $showForm = false; // Controls visibility of the form
    public $confirmingDeletion = false;
    public $paperToDelete = null;

    // Data for dropdowns
    public $departments = [];
    public $courses = []; // Will be dynamically loaded based on department selection
    public $levels = [];
    public $examTypes = [];
    public $years = [];
    public $studentTypes = [];
    
    // Define validation rules - adjusted for nullable file on update
    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'course_id' => 'required|exists:courses,id',
            'level' => 'required|string',
            'exam_type' => 'required|string',
            'exam_year' => 'required|integer|min:2000|max:2099',
            'student_type' => 'required|string',
            'semester' => 'required|integer|min:1|max:2',
            'visibility' => 'required|in:public,restricted',
            // File is required for new papers, nullable for existing ones (if not re-uploading)
            'file' => $this->paperId ? 'nullable|file|mimes:pdf|max:10240' : 'required|file|mimes:pdf|max:10240',
        ];
    }
    
    protected $validationAttributes = [
        'department_id' => 'department',
        'course_id' => 'course',
    ];

    // Lifecycle hook: runs once when component is initialized
    public function mount()
    {
        $this->loadDropdownData();
    }

    // Load static data for dropdowns
    private function loadDropdownData()
    {
        $this->departments = Department::orderBy('name')->get();
        $this->levels = ['100', '200', '300', '400', '500', '600']; // Example levels
        $this->examTypes = ['Mid-term', 'End of Semester', 'Special Resit']; // Example types
        $this->studentTypes = ['Regular', 'Distance', 'Mature']; // Example types
        $this->years = range(date('Y'), 2000); // Generate years from current year down to 2000
    }
    
    // Livewire hook: Called when department_id property is updated
    public function updatedDepartmentId($value)
    {
        if ($value) {
            $this->courses = Course::where('department_id', $value)->orderBy('name')->get();
        } else {
            $this->courses = [];
        }
        $this->course_id = null; // Reset course selection when department changes
    }

    // Reset pagination when search filter changes
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    // Method to open the form for creating a new paper
    public function openCreateForm()
    {
        $this->resetForm(); // Reset all form fields and validation
        $this->showForm = true; // Show the form
    }

    // Method to open the form for editing an existing paper
    public function editPaper($id)
    {
        $this->resetValidation(); // Clear any previous validation errors
        $paper = Paper::findOrFail($id);
        
        $this->paperId = $id; // Set the ID to indicate edit mode
        $this->title = $paper->title;
        $this->department_id = $paper->department_id;
        // Trigger the updatedDepartmentId method to populate courses for the selected department
        $this->updatedDepartmentId($paper->department_id); 
        $this->course_id = $paper->course_id;
        $this->level = $paper->level;
        $this->exam_type = $paper->exam_type;
        $this->exam_year = $paper->exam_year;
        $this->student_type = $paper->student_type;
        $this->semester = $paper->semester;
        $this->visibility = $paper->visibility;
        $this->file = null; // Important: Clear the file input when editing. User re-uploads if needed.
        $this->showForm = true; // Show the form
    }

    // Handles saving new papers and updating existing ones
    public function savePaper()
    {
        $this->validate(); // Validate inputs based on rules() method

        try {
            // Determine if it's an update or create based on $this->paperId
            if ($this->paperId) {
                // UPDATE LOGIC
                $paper = Paper::findOrFail($this->paperId);
                
                // Handle file upload if a new file is provided
                if ($this->file) {
                    // Delete old file if it exists
                    if ($paper->file_path && Storage::disk('public')->exists($paper->file_path)) {
                        Storage::disk('public')->delete($paper->file_path);
                    }
                    
                    // Store new file
                    $fileName = time() . '_' . Str::slug($this->title) . '.' . $this->file->getClientOriginalExtension();
                    $filePath = $this->file->storeAs('papers', $fileName, 'public');
                    $paper->file_path = $filePath;
                }
                
                // Update paper details
                $paper->update([
                    'title' => $this->title,
                    'department_id' => $this->department_id,
                    'course_id' => $this->course_id,
                    'course_name' => Course::find($this->course_id)->name,
                    'level' => $this->level,
                    'exam_type' => $this->exam_type,
                    'exam_year' => $this->exam_year,
                    'student_type' => $this->student_type,
                    'semester' => $this->semester,
                    'visibility' => $this->visibility,
                    // file_path is updated conditionally above
                ]);
                
                session()->flash('message', 'Paper successfully updated!');

            } else {
                // CREATE LOGIC
                // Process and store the file (required for new paper as per rules)
                $fileName = time() . '_' . Str::slug($this->title) . '.' . $this->file->getClientOriginalExtension();
                $filePath = $this->file->storeAs('papers', $fileName, 'public');
                
                // Create new paper record
                Paper::create([
                    'title' => $this->title,
                    'department_id' => $this->department_id,
                    'course_id' => $this->course_id,
                    'course_name' => Course::find($this->course_id)->name,
                    'level' => $this->level,
                    'exam_type' => $this->exam_type,
                    'exam_year' => $this->exam_year,
                    'student_type' => $this->student_type,
                    'semester' => $this->semester,
                    'visibility' => $this->visibility,
                    'file_path' => $filePath,
                    'uploaded_by' => auth()->id(), // Assuming authentication is set up
                ]);
                
                session()->flash('message', 'Paper successfully uploaded!');
            }
            
            $this->resetForm(); // Clear the form and hide it
        } catch (\Exception $e) {
            // Log the error for debugging
            \Illuminate\Support\Facades\Log::error('Paper save error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    
    // Shows delete confirmation modal
    public function confirmDelete($id)
    {
        $this->paperToDelete = $id;
        $this->confirmingDeletion = true;
    }

    // Deletes the paper after confirmation
    public function deletePaper()
    {
        try {
            $paper = Paper::findOrFail($this->paperToDelete);
            
            // Delete file from storage
            if ($paper->file_path && Storage::disk('public')->exists($paper->file_path)) {
                Storage::disk('public')->delete($paper->file_path);
            }
            
            $paper->delete();
            
            session()->flash('message', 'Paper successfully deleted!');
            $this->confirmingDeletion = false;
            $this->paperToDelete = null;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Paper delete error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            session()->flash('error', 'An error occurred during deletion: ' . $e->getMessage());
        }
    }
    
    // Resets all form-related properties and validation errors
    public function resetForm()
    {
        $this->reset([
            'title', 'department_id', 'course_id', 'level', 'exam_type',
            'exam_year', 'student_type', 'semester', 'visibility', 'file',
            'paperId', 'showForm'
        ]);
        $this->resetValidation();
        // Ensure courses are cleared if no department is selected
        $this->courses = []; 
    }

    // Resets all filter-related properties
    public function resetFilters()
    {
        $this->reset([
            'search', 'departmentFilter', 'yearFilter', 'levelFilter',
            'examTypeFilter', 'studentTypeFilter', 'semesterFilter'
        ]);
        $this->resetPage(); // Important: reset pagination when filters change
    }

    // Toggle the form visibility
    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) {
            $this->resetForm(); // Reset form state if closed
        }
    }

    // Render method to fetch data and pass to the view
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
        
        // Ensure courses are loaded for the current department_id if the form is open
        // This handles cases where user might have selected a department, refreshed, or re-entered
        if ($this->showForm && $this->department_id && empty($this->courses)) {
             $this->courses = Course::where('department_id', $this->department_id)->orderBy('name')->get();
        } elseif (!$this->department_id) {
            $this->courses = []; // No department selected, no courses
        }

        return view('livewire.admin.paper-manager', [
            'papers' => $papers,
            'departments' => $this->departments,
            'courses' => $this->courses, 
            'years' => $this->years,
            'levels' => $this->levels,
            'examTypes' => $this->examTypes,
            'studentTypes' => $this->studentTypes,
        ]);
    }
}