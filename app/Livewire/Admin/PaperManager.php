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
    public $course_name;
    public $level;
    public $exam_type;
    public $exam_year;
    public $student_type;
    public $semester;
    public $visibility = 'public';
    public $file;
    
    // Edit Mode
    public $editMode = false;
    public $paperId;
    
    // Filters
    public $search = '';
    public $departmentFilter = '';
    public $yearFilter = '';
    public $levelFilter = '';
    public $examTypeFilter = '';
    public $studentTypeFilter = '';
    public $semesterFilter = '';
    
    protected $rules = [
        'title' => 'required|string|max:255',
        'department_id' => 'required|exists:departments,id',
        'course_name' => 'required|string|max:255',
        'level' => 'required|string',
        'exam_type' => 'required|string',
        'exam_year' => 'required|integer|min:2000|max:2099',
        'student_type' => 'required|string',
        'semester' => 'required|integer|min:1|max:2',
        'visibility' => 'required|in:public,restricted',
        'file' => 'required|file|mimes:pdf|max:10240', // Max 10MB
    ];
    
    protected $validationAttributes = [
        'department_id' => 'department',
    ];
    
    public function mount()
    {
        // Initialize any needed data
    }
    
    public function render()
    {
        $query = Paper::query()
            ->when($this->search, function ($q) {
                return $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('course_name', 'like', '%' . $this->search . '%');
            })
            ->when($this->departmentFilter, function ($q) {
                return $q->where('department_id', $this->departmentFilter);
            })
            ->when($this->yearFilter, function ($q) {
                return $q->where('exam_year', $this->yearFilter);
            })
            ->when($this->levelFilter, function ($q) {
                return $q->where('level', $this->levelFilter);
            })
            ->when($this->examTypeFilter, function ($q) {
                return $q->where('exam_type', $this->examTypeFilter);
            })
            ->when($this->studentTypeFilter, function ($q) {
                return $q->where('student_type', $this->studentTypeFilter);
            })
            ->when($this->semesterFilter, function ($q) {
                return $q->where('semester', $this->semesterFilter);
            });
        
        $papers = $query->latest()->paginate(10);
        $departments = Department::orderBy('name')->get();
        
        $years = range(date('Y'), 2000);
        $levels = ['100', '200', '300', '400'];
        $examTypes = ['Final', 'Resit', 'Mid-semester'];
        $studentTypes = ['HND', 'B-Tech', 'Top-up'];
        
        // Add the courses variable that's missing
        $courses = \App\Models\Course::orderBy('name')->get();
        
        return view('livewire.admin.paper-manager', [
            'papers' => $papers,
            'departments' => $departments,
            'years' => $years,
            'levels' => $levels,
            'examTypes' => $examTypes,
            'studentTypes' => $studentTypes,
            'courses' => $courses, // Add this line to fix the error
        ]);
    }
    
    public function savePaper()
    {
        if ($this->editMode) {
            $this->updatePaper();
            return;
        }
        
        $this->validate();
        
        // Process and store the file
        $fileName = time() . '_' . Str::slug($this->title) . '.' . $this->file->getClientOriginalExtension();
        $filePath = $this->file->storeAs('papers', $fileName, 'public');
        
        // Create paper record
        Paper::create([
            'title' => $this->title,
            'department_id' => $this->department_id,
            'course_name' => $this->course_name,
            'level' => $this->level,
            'exam_type' => $this->exam_type,
            'exam_year' => $this->exam_year,
            'student_type' => $this->student_type,
            'semester' => $this->semester,
            'visibility' => $this->visibility,
            'file_path' => $filePath,
            'uploaded_by' => auth()->id(),
        ]);
        
        $this->resetForm();
        session()->flash('message', 'Paper successfully uploaded!');
    }
    
    public function editPaper($id)
    {
        $this->resetValidation();
        $this->editMode = true;
        $this->paperId = $id;
        
        $paper = Paper::findOrFail($id);
        
        $this->title = $paper->title;
        $this->department_id = $paper->department_id;
        $this->course_name = $paper->course_name;
        $this->level = $paper->level;
        $this->exam_type = $paper->exam_type;
        $this->exam_year = $paper->exam_year;
        $this->student_type = $paper->student_type;
        $this->semester = $paper->semester;
        $this->visibility = $paper->visibility;
        // Note: We don't set $this->file since we'll need a new upload
    }
    
    public function updatePaper()
    {
        // Adjust validation rules for update (file might not be required)
        $rules = $this->rules;
        if (!$this->file) {
            unset($rules['file']);
        }
        
        $this->validate($rules);
        
        $paper = Paper::findOrFail($this->paperId);
        
        // Process file if a new one was uploaded
        if ($this->file) {
            // Delete old file
            if (Storage::disk('public')->exists($paper->file_path)) {
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
        $paper->course_name = $this->course_name;
        $paper->level = $this->level;
        $paper->exam_type = $this->exam_type;
        $paper->exam_year = $this->exam_year;
        $paper->student_type = $this->student_type;
        $paper->semester = $this->semester;
        $paper->visibility = $this->visibility;
        $paper->save();
        
        $this->resetForm();
        session()->flash('message', 'Paper successfully updated!');
    }
    
    public function deletePaper($id)
    {
        $paper = Paper::findOrFail($id);
        
        // Delete file from storage
        if (Storage::disk('public')->exists($paper->file_path)) {
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
            'title', 'department_id', 'course_name', 'level', 'exam_type',
            'exam_year', 'student_type', 'semester', 'visibility', 'file',
            'editMode', 'paperId'
        ]);
        $this->resetValidation();
    }
    
    public function resetFilters()
    {
        $this->reset([
            'search', 'departmentFilter', 'yearFilter', 'levelFilter',
            'examTypeFilter', 'studentTypeFilter', 'semesterFilter'
        ]);
    }
}