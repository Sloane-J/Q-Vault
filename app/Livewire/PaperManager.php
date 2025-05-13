<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Level;
use App\Models\Paper;
use App\Models\StudentType;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class PaperManager extends Component
{
    use WithPagination;

    // Search and filter properties
    public $search = '';
    public $department = '';
    public $studentType = '';
    public $level = '';
    public $examType = '';
    public $year = '';
    public $semester = '';
    
    // Sort options
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    
    // Visibility filter
    public $visibilityFilter = '';
    
    // Bulk actions
    public $selectedPapers = [];
    public $selectAll = false;
    
    // Reference data
    public $departments = [];
    public $studentTypes = [];
    public $levels = [];
    public $examTypes = ['midterm', 'final', 'quiz', 'assignment'];
    public $years = [];
    public $semesters = [1, 2, 3];
    
    // Modal
    public $showDeleteModal = false;
    public $paperToDelete = null;
    
    protected $listeners = [
        'paperUploaded' => '$refresh',
        'confirmDeletion' => 'showDeleteConfirmation',
    ];
    
    // Prevent old data from persisting when navigating with browser back button
    protected $queryString = [
        'search' => ['except' => ''],
        'department' => ['except' => ''],
        'studentType' => ['except' => ''],
        'level' => ['except' => ''],
        'examType' => ['except' => ''],
        'year' => ['except' => ''],
        'semester' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'visibilityFilter' => ['except' => ''],
    ];

    public function mount()
    {
        // Initialize filter dropdowns
        $this->departments = Department::all();
        $this->studentTypes = StudentType::all();
        $this->levels = Level::all();
        
        // Get unique years from papers
        $allYears = Paper::distinct()->orderBy('exam_year', 'desc')->pluck('exam_year')->toArray();
        $this->years = $allYears;
    }

    public function resetFilters()
    {
        $this->reset([
            'search', 'department', 'studentType', 'level', 
            'examType', 'year', 'semester', 'visibilityFilter'
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function showDeleteConfirmation($id)
    {
        $this->paperToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->paperToDelete = null;
    }

    public function deletePaper()
    {
        $paper = Paper::findOrFail($this->paperToDelete);
        
        // Get all file paths associated with this paper
        $filePaths = [$paper->file_path];
        $versions = $paper->versions()->get();
        foreach ($versions as $version) {
            $filePaths[] = $version->file_path;
        }
        
        // Delete all files
        foreach (array_unique($filePaths) as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
        
        // Delete versions and paper
        $paper->versions()->delete();
        $paper->delete();
        
        $this->showDeleteModal = false;
        $this->paperToDelete = null;
        
        session()->flash('success', 'Paper deleted successfully!');
    }

    public function toggleVisibility($id)
    {
        $paper = Paper::findOrFail($id);
        $paper->visibility = !$paper->visibility;
        $paper->save();
        
        $status = $paper->visibility ? 'visible' : 'hidden';
        session()->flash('success', "Paper is now {$status}");
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedPapers = $this->papers->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedPapers = [];
        }
    }

    public function bulkDelete()
    {
        if (empty($this->selectedPapers)) {
            session()->flash('error', 'No papers selected for deletion');
            return;
        }
        
        $papers = Paper::whereIn('id', $this->selectedPapers)->get();
        
        foreach ($papers as $paper) {
            // Get all file paths associated with this paper
            $filePaths = [$paper->file_path];
            $versions = $paper->versions()->get();
            foreach ($versions as $version) {
                $filePaths[] = $version->file_path;
            }
            
            // Delete all files
            foreach (array_unique($filePaths) as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
            
            // Delete versions and paper
            $paper->versions()->delete();
            $paper->delete();
        }
        
        $this->selectedPapers = [];
        $this->selectAll = false;
        
        session()->flash('success', count($papers) . ' papers deleted successfully');
    }

    public function bulkToggleVisibility($makeVisible)
    {
        if (empty($this->selectedPapers)) {
            session()->flash('error', 'No papers selected');
            return;
        }
        
        Paper::whereIn('id', $this->selectedPapers)->update(['visibility' => $makeVisible]);
        
        $status = $makeVisible ? 'visible' : 'hidden';
        session()->flash('success', count($this->selectedPapers) . " papers are now {$status}");
        
        $this->selectedPapers = [];
        $this->selectAll = false;
    }

    public function getPapersProperty()
    {
        $query = Paper::query()
            ->with(['department', 'studentType', 'level', 'user'])
            ->when($this->search, function ($query) {
                return $query->where(function ($query) {
                    $query->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('course_name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->department, function ($query) {
                return $query->where('department_id', $this->department);
            })
            ->when($this->studentType, function ($query) {
                return $query->where('student_type_id', $this->studentType);
            })
            ->when($this->level, function ($query) {
                return $query->where('level_id', $this->level);
            })
            ->when($this->examType, function ($query) {
                return $query->where('exam_type', $this->examType);
            })
            ->when($this->year, function ($query) {
                return $query->where('exam_year', $this->year);
            })
            ->when($this->semester, function ($query) {
                return $query->where('semester', $this->semester);
            })
            ->when($this->visibilityFilter !== '', function ($query) {
                return $query->where('visibility', $this->visibilityFilter === 'visible');
            })
            ->orderBy($this->sortField, $this->sortDirection);
        
        return $query->get();
    }

    public function render()
    {
        return view('livewire.paper-uploader');
    }
}