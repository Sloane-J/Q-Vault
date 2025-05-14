<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use App\Models\Department;
use App\Models\ExamPaper;
use App\Models\Tag;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaperUploader extends Component
{
    use WithFileUploads;

    // Form inputs
    public $paper;
    public $department_id = '';
    public $course_id = '';
    public $student_type = '';
    public $level = '';
    public $exam_year = '';
    public $semester = '';
    public $exam_type = '';
    public $visibility = 'public';
    public $tags = '';
    public $description = '';

    // For dynamic dropdowns
    public $departments = [];
    public $courses = [];

    protected $rules = [
        'paper' => 'required|file|mimes:pdf|max:10240', // 10MB max
        'department_id' => 'required|exists:departments,id',
        'course_id' => 'required|exists:courses,id',
        'student_type' => 'required|in:HND,B-Tech,Top-up',
        'level' => 'required|numeric|min:100|max:400',
        'exam_year' => 'required|numeric|min:2000|max:2099',
        'semester' => 'required|in:1,2',
        'exam_type' => 'required|in:final,resit,mid-semester,quiz,assignment',
        'visibility' => 'required|in:public,restricted',
        'tags' => 'nullable|string|max:255',
        'description' => 'nullable|string|max:1000',
    ];

    protected $messages = [
        'paper.required' => 'Please select a PDF file.',
        'paper.mimes' => 'The file must be a PDF document.',
        'paper.max' => 'The file size cannot exceed 10MB.',
    ];

    // Initialize component
    public function mount()
    {
        $this->departments = Department::orderBy('name')->get();
        $this->exam_year = date('Y'); // Default to current year
    }

    // Load courses when department changes
    public function updatedDepartmentId()
    {
        $this->courses = Course::where('department_id', $this->department_id)
            ->orderBy('name')
            ->get();
        
        $this->course_id = ''; // Reset course selection
    }

    // Reset all form inputs
    public function resetForm()
    {
        $this->reset(['paper', 'course_id', 'student_type', 'level', 'exam_year', 'semester', 'exam_type', 'visibility', 'tags', 'description']);
        $this->exam_year = date('Y'); // Reset exam year to current year
        $this->visibility = 'public'; // Reset visibility to public
    }

    // Upload paper
    public function uploadPaper()
    {
        $this->validate();

        try {
            // Extract metadata from the form
            $department = Department::findOrFail($this->department_id);
            $course = Course::findOrFail($this->course_id);
            
            // Generate a unique filename
            $filename = Str::slug($course->code) . '-' . 
                $this->student_type . '-' . 
                'level' . $this->level . '-' . 
                $this->exam_type . '-' . 
                $this->exam_year . '-' . 
                'sem' . $this->semester . '-' . 
                Str::random(8) . '.pdf';
            
            // Store the paper
            $path = $this->paper->storeAs(
                'exam_papers/' . $department->slug . '/' . $course->code, 
                $filename, 
                'public'
            );
            
            // Create the exam paper record
            $examPaper = ExamPaper::create([
                'title' => $course->name . ' ' . ucfirst($this->exam_type) . ' Exam ' . $this->exam_year . ' Sem ' . $this->semester,
                'department_id' => $this->department_id,
                'course_id' => $this->course_id,
                'student_type' => $this->student_type,
                'level' => $this->level,
                'exam_year' => $this->exam_year,
                'semester' => $this->semester,
                'exam_type' => $this->exam_type,
                'file_path' => $path,
                'file_size' => Storage::disk('public')->size($path),
                'visibility' => $this->visibility,
                'description' => $this->description,
                'uploaded_by' => auth()->id(),
            ]);
            
            // Process tags
            if (!empty($this->tags)) {
                $tagNames = array_map('trim', explode(',', $this->tags));
                $tagIds = [];
                
                foreach ($tagNames as $tagName) {
                    if (!empty($tagName)) {
                        $tag = Tag::firstOrCreate(['name' => $tagName]);
                        $tagIds[] = $tag->id;
                    }
                }
                
                $examPaper->tags()->sync($tagIds);
            }
            
            // Additional default tags (based on metadata)
            $defaultTags = [
                $department->name,
                $course->name,
                $course->code,
                'Level ' . $this->level,
                $this->student_type,
                $this->exam_year,
                'Semester ' . $this->semester,
                ucfirst($this->exam_type) . ' Exam'
            ];
            
            foreach ($defaultTags as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $examPaper->tags()->syncWithoutDetaching([$tag->id]);
            }
            
            // Show success message
            session()->flash('success', 'Exam paper uploaded successfully!');
            
            // Reset form
            $this->resetForm();
            
        } catch (\Exception $e) {
            // Log the error
            logger()->error('Error uploading exam paper: ' . $e->getMessage());
            
            // Show error message
            session()->flash('error', 'Failed to upload exam paper. Please try again later.');
        }
    }

    public function render()
    {
        return view('livewire.admin.papers.paper-uploader');
    }
}