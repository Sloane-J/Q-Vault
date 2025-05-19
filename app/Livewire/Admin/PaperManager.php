<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads; // For handling file uploads
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage; // For file deletion/management
// use App\Models\Paper; // Uncomment these and other models as needed
// use App\Models\Department;
// use App\Models\Course;
// use App\Models\StudentType;
// use App\Models\Level;


class PaperManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    // --- Form Properties ---
    public $paperId;
    public $title;
    public $description; // Added based on your database design (Papers table)
    public $file; // For new file uploads
    public $existingFilePath; // To store path of existing file when editing
    public $department_id;
    public $semester;
    public $exam_type;
    public $course_name; // Corresponds to course_name in Papers table
    public $exam_year;
    public $student_type;
    public $level;
    public $visibility = 'public'; // Default to public
    public $showForm = false;

    // --- Filter Properties ---
    public $search = '';
    public $departmentFilter = '';
    public $yearFilter = '';
    public $levelFilter = '';
    public $examTypeFilter = '';
    public $studentTypeFilter = '';
    public $semesterFilter = '';

    // --- Modal Properties ---
    public $confirmingDeletion = false;
    public $paperIdToDelete;

    // --- Data for Select Dropdowns (replace with actual database queries) ---
    public $departments = []; // e.g., Department::all()
    public $courses = [];     // e.g., Course::all()
    public $studentTypes = ['HND', 'B-Tech', 'Top-up']; // Example data
    public $levels = ['100', '200', '300', '400']; // Example data
    public $examTypes = ['Mid-Term', 'End of Semester', 'Resit']; // Example data
    public $years = []; // Dynamically generate, e.g., range(date('Y'), 2000)

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000', // Added validation for description
        'department_id' => 'required|exists:departments,id', // Assuming departments table exists
        'semester' => 'required|in:1,2',
        'exam_type' => 'required|string|max:50',
        'course_name' => 'required|string|max:255', // Change to course_id if you use foreign key
        'exam_year' => 'required|integer|min:1900|max:2100',
        'student_type' => 'required|string|max:50',
        'level' => 'required|string|max:10',
        'visibility' => 'required|in:public,restricted',
        'file' => 'nullable|mimes:pdf|max:10240', // 10MB Max, nullable for edit
    ];

    // Real-time validation
    protected $validationAttributes = [
        'department_id' => 'department',
        'course_name' => 'course name',
        'exam_type' => 'exam type',
        'exam_year' => 'exam year',
        'student_type' => 'student type',
    ];

    public function mount()
    {
        // Populate dropdowns - replace with actual database queries
        // $this->departments = Department::all();
        // $this->courses = Course::all();

        // Placeholder data for demonstration
        $this->departments = collect([
            (object)['id' => 1, 'name' => 'Computer Science'],
            (object)['id' => 2, 'name' => 'Electrical Engineering'],
        ]);
        $this->courses = collect([
            (object)['id' => 1, 'name' => 'Data Structures'],
            (object)['id' => 2, 'name' => 'Circuit Analysis'],
        ]);

        $this->years = range(date('Y'), 2000); // Years from current to 2000
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) {
            $this->resetForm(); // Reset form if hiding
        }
    }

    public function savePaper()
    {
        $this->validate();

        $filePath = $this->existingFilePath; // Default to existing path

        // Handle file upload if a new file is provided
        if ($this->file) {
            // Delete old file if updating and a new file is provided
            if ($this->paperId && $this->existingFilePath) {
                Storage::delete($this->existingFilePath);
            }
            $filePath = $this->file->store('papers', 'public'); // Store in 'storage/app/public/papers'
        } elseif (!$this->paperId) {
            // If it's a new paper and no file is provided, it's an error
            session()->flash('error', 'PDF file is required for new papers.');
            return;
        }

        if ($this->paperId) {
            // Update existing paper
            // $paper = Paper::find($this->paperId);
            // $paper->update([
            //     'title' => $this->title,
            //     'description' => $this->description,
            //     'file_path' => $filePath,
            //     'department_id' => $this->department_id,
            //     'semester' => $this->semester,
            //     'exam_type' => $this->exam_type,
            //     'course_name' => $this->course_name,
            //     'exam_year' => $this->exam_year,
            //     'student_type' => $this->student_type,
            //     'level' => $this->level,
            //     'visibility' => $this->visibility,
            //     // 'user_id' => auth()->id(), // Assuming the uploader is the current user
            // ]);
            session()->flash('message', 'Paper updated successfully.');
        } else {
            // Create new paper
            // Paper::create([
            //     'title' => $this->title,
            //     'description' => $this->description,
            //     'file_path' => $filePath,
            //     'department_id' => $this->department_id,
            //     'semester' => $this->semester,
            //     'exam_type' => $this->exam_type,
            //     'course_name' => $this->course_name,
            //     'exam_year' => $this->exam_year,
            //     'student_type' => $this->student_type,
            //     'level' => $this->level,
            //     'visibility' => $this->visibility,
            //     // 'user_id' => auth()->id(),
            // ]);
            session()->flash('message', 'Paper uploaded successfully.');
        }

        $this->resetForm();
    }

    public function editPaper($paperId)
    {
        // $paper = Paper::findOrFail($paperId);
        // $this->paperId = $paper->id;
        // $this->title = $paper->title;
        // $this->description = $paper->description;
        // $this->existingFilePath = $paper->file_path; // Store current file path
        // $this->department_id = $paper->department_id;
        // $this->semester = $paper->semester;
        // $this->exam_type = $paper->exam_type;
        // $this->course_name = $paper->course_name;
        // $this->exam_year = $paper->exam_year;
        // $this->student_type = $paper->student_type;
        // $this->level = $paper->level;
        // $this->visibility = $paper->visibility;

        // Placeholder for demonstration
        $this->paperId = $paperId;
        $this->title = 'Sample Paper ' . $paperId;
        $this->description = 'Description for Sample Paper ' . $paperId;
        $this->existingFilePath = 'papers/sample_paper_' . $paperId . '.pdf'; // Placeholder path
        $this->department_id = 1;
        $this->semester = 1;
        $this->exam_type = 'End of Semester';
        $this->course_name = 'Data Structures';
        $this->exam_year = 2023;
        $this->student_type = 'HND';
        $this->level = '300';
        $this->visibility = 'public';

        $this->showForm = true; // Show the form for editing
    }

    public function resetForm()
    {
        $this->reset([
            'paperId', 'title', 'description', 'file', 'existingFilePath',
            'department_id', 'semester', 'exam_type', 'course_name',
            'exam_year', 'student_type', 'level', 'visibility', 'showForm'
        ]);
        $this->resetValidation(); // Clear any validation errors
        $this->visibility = 'public'; // Reset default visibility
    }

    public function confirmDelete($paperId)
    {
        $this->paperIdToDelete = $paperId;
        $this->confirmingDeletion = true; // Show the modal
    }

    public function deletePaper()
    {
        if ($this->confirmingDeletion && $this->paperIdToDelete) {
            // Find paper and delete file
            // $paper = Paper::find($this->paperIdToDelete);
            // if ($paper && $paper->file_path) {
            //     Storage::delete($paper->file_path);
            // }
            // $paper->delete();

            // Placeholder for demonstration:
            session()->flash('message', 'Paper ' . $this->paperIdToDelete . ' deleted successfully.');

            $this->confirmingDeletion = false; // Hide the modal
            $this->paperIdToDelete = null; // Clear the ID
        }
    }

    public function resetFilters()
    {
        $this->reset([
            'search', 'departmentFilter', 'yearFilter', 'levelFilter',
            'examTypeFilter', 'studentTypeFilter', 'semesterFilter'
        ]);
        $this->resetPage(); // Reset pagination when filters are cleared
    }

    public function render()
    {
        // This is where you'd query your 'Papers' based on filters and pagination
        // For demonstration, I'll use dummy data

        // $papers = Paper::query()
        //     ->when($this->search, function ($query) {
        //         $query->where('title', 'like', '%' . $this->search . '%')
        //               ->orWhere('description', 'like', '%' . $this->search . '%')
        //               ->orWhere('course_name', 'like', '%' . $this->search . '%');
        //     })
        //     ->when($this->departmentFilter, fn($query) => $query->where('department_id', $this->departmentFilter))
        //     ->when($this->yearFilter, fn($query) => $query->where('exam_year', $this->yearFilter))
        //     ->when($this->levelFilter, fn($query) => $query->where('level', $this->levelFilter))
        //     ->when($this->examTypeFilter, fn($query) => $query->where('exam_type', $this->examTypeFilter))
        //     ->when($this->studentTypeFilter, fn($query) => $query->where('student_type', $this->studentTypeFilter))
        //     ->when($this->semesterFilter, fn($query) => $query->where('semester', $this->semesterFilter))
        //     ->with(['department', 'course']) // Assuming relations exist
        //     ->orderBy('created_at', 'desc')
        //     ->paginate(10);

        // Dummy data for testing the UI
        $dummyPapers = collect([
            (object)[
                'id' => 1, 'title' => 'Introduction to Algorithms', 'description' => 'Fundamental algorithms', 'file_path' => 'papers/dummy-algo.pdf',
                'department_id' => 1, 'semester' => 1, 'exam_type' => 'End of Semester', 'course_name' => 'Algorithms',
                'exam_year' => 2023, 'student_type' => 'B-Tech', 'level' => '300', 'visibility' => 'public',
                'department' => (object)['name' => 'Computer Science'], 'course' => (object)['name' => 'Algorithms']
            ],
            (object)[
                'id' => 2, 'title' => 'Digital Electronics', 'description' => 'Logic gates and circuits', 'file_path' => 'papers/dummy-digital.pdf',
                'department_id' => 2, 'semester' => 2, 'exam_type' => 'Mid-Term', 'course_name' => 'Digital Systems',
                'exam_year' => 2022, 'student_type' => 'HND', 'level' => '200', 'visibility' => 'restricted',
                'department' => (object)['name' => 'Electrical Engineering'], 'course' => (object)['name' => 'Digital Systems']
            ],
            // Add more dummy papers as needed for pagination and filtering
        ]);

        // Basic filtering of dummy data
        $filteredPapers = $dummyPapers->filter(function($paper) {
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

        // Manually paginate dummy data
        $perPage = 10;
        $currentPage = $this->page ?: 1;
        $pagedData = $filteredPapers->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $papers = new \Illuminate\Pagination\LengthAwarePaginator($pagedData, $filteredPapers->count(), $perPage, $currentPage, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);


        return view('livewire.paper-manager', [
            'papers' => $papers,
            // 'departments' will be available from mount()
        ]);
    }
}