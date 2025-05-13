<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Level;
use App\Models\Paper;
use App\Models\PaperVersion;
use App\Models\StudentType;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class PaperUploader extends Component
{
    use WithFileUploads;

    // Form fields
    public $title;
    public $description;
    public $department_id;
    public $semester = 1;
    public $exam_type = 'final';
    public $course_name;
    public $exam_year;
    public $student_type_id;
    public $level_id;
    public $visibility = true;
    public $paperFile;

    // For dynamic select options
    public $departments = [];
    public $studentTypes = [];
    public $levels = [];
    public $availableLevels = [];
    public $examYears = [];
    public $semesters = [1, 2, 3];
    public $examTypes = ['midterm', 'final', 'quiz', 'assignment'];

    // Loading state
    public $isUploading = false;

    protected $rules = [
        'paperFile' => 'required|file|mimes:pdf,doc,docx|max:10240',
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'department_id' => 'required|exists:departments,id',
        'semester' => 'required|integer|between:1,3',
        'exam_type' => 'required|string|in:midterm,final,quiz,assignment',
        'course_name' => 'required|string|max:255',
        'exam_year' => 'required|integer|min:2000',
        'student_type_id' => 'required|exists:student_types,id',
        'level_id' => 'required|exists:levels,id',
        'visibility' => 'boolean',
    ];

    protected $messages = [
        'paperFile.required' => 'Please select a file to upload.',
        'paperFile.file' => 'The uploaded file is invalid.',
        'paperFile.mimes' => 'The file must be a PDF, DOC, or DOCX file.',
        'paperFile.max' => 'The file size must not exceed 10MB.',
        'title.required' => 'Please enter a title for the paper.',
        'department_id.required' => 'Please select a department.',
        'semester.required' => 'Please select a semester.',
        'exam_type.required' => 'Please select an exam type.',
        'course_name.required' => 'Please enter a course name.',
        'exam_year.required' => 'Please select an exam year.',
        'student_type_id.required' => 'Please select a student type.',
        'level_id.required' => 'Please select a level.',
    ];

    public function mount()
    {
        // Set default exam year to current year
        $this->exam_year = date('Y');
        
        // Initialize available exam years (current year and 10 years back)
        $currentYear = (int) date('Y');
        $this->examYears = range($currentYear, $currentYear - 10);
        
        // Fetch all departments, student types, and levels
        $this->departments = Department::all();
        $this->studentTypes = StudentType::all();
        $this->levels = Level::all();
    }

    public function updatedStudentTypeId($value)
    {
        // Update available levels when student type changes
        $this->availableLevels = Level::where('student_type_id', $value)->get();
        $this->level_id = null; // Reset selected level
    }

    public function save()
    {
        $this->validate();
        
        $this->isUploading = true;
        
        try {
            // Upload the file
            $fileName = time() . '_' . $this->paperFile->getClientOriginalName();
            $path = $this->paperFile->storeAs('papers', $fileName, 'public');
            
            // Create new paper record
            $paper = Paper::create([
                'title' => $this->title,
                'description' => $this->description,
                'file_path' => $path,
                'department_id' => $this->department_id,
                'semester' => $this->semester,
                'exam_type' => $this->exam_type,
                'course_name' => $this->course_name,
                'exam_year' => $this->exam_year,
                'student_type_id' => $this->student_type_id,
                'level_id' => $this->level_id,
                'visibility' => $this->visibility,
                'user_id' => Auth::id(),
            ]);
            
            // Create initial version
            PaperVersion::create([
                'paper_id' => $paper->id,
                'version_number' => 1,
                'file_path' => $path,
                'notes' => 'Initial upload',
            ]);
            
            // Reset form fields
            $this->reset(['title', 'description', 'course_name', 'paperFile']);
            
            // Show success message
            session()->flash('success', 'Paper uploaded successfully!');
            
            // Emit event for parent components
            $this->emit('paperUploaded', $paper->id);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to upload paper: ' . $e->getMessage());
        }
        
        $this->isUploading = false;
    }

    public function render()
    {
        return view('livewire.paper-uploader');
    }
}