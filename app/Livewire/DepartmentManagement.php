<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Department;
use Illuminate\Support\Facades\Log;

class DepartmentManagement extends Component
{
    use WithPagination;

    // Search and filtering properties
    public $search = '';
    
    // Form inputs
    public $departmentId = null;
    public $name = '';
    public $code = '';
    public $description = '';
    public $is_active = true;
    
    // UI state
    public $showModal = false;
    public $confirmingDeletion = false;
    public $departmentToDelete = null;

    // Define validation rules
    protected function rules()
    {
        return [
            'name' => 'required|min:3|max:255',
            'code' => 'required|min:2|max:10|unique:departments,code,' . $this->departmentId,
            'description' => 'nullable|max:1000',
            'is_active' => 'boolean',
        ];
    }

    // Reset search pagination
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Open create modal
    public function openCreateModal()
    {
        $this->reset(['departmentId', 'name', 'code', 'description', 'is_active']);
        $this->is_active = true; // Default to active
        $this->showModal = true;
    }

        // Add these computed properties
    public function getActiveTodayCountProperty()
    {
        return \App\Models\User::role('student')
            ->whereHas('sessions', function($query) {
                $query->where('last_activity', '>=', now()->subDay()->timestamp);
            })
            ->count();
    }

    public function getNewThisWeekCountProperty()
    {
        return \App\Models\User::role('student')
            ->where('created_at', '>=', now()->subWeek())
            ->count();
    }

    // Open edit modal with data
    public function openEditModal($id)
    {
        $department = Department::findOrFail($id);
        $this->departmentId = $id;
        $this->name = $department->name;
        $this->code = $department->code;
        $this->description = $department->description;
        $this->is_active = $department->is_active;
        
        $this->showModal = true;
    }

    // Save department (create or update)
    public function saveDepartment()
    {
        $this->validate();
        
        try {
            if ($this->departmentId) {
                // Update existing department
                $department = Department::findOrFail($this->departmentId);
                $department->update([
                    'name' => $this->name,
                    'code' => $this->code,
                    'description' => $this->description,
                    'is_active' => $this->is_active,
                ]);
                
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Department updated successfully!'
                ]);
                
                Log::info('Department updated', [
                    'id' => $department->id,
                    'user_id' => auth()->id()
                ]);
            } else {
                // Create new department
                $department = Department::create([
                    'name' => $this->name,
                    'code' => $this->code,
                    'description' => $this->description,
                    'is_active' => $this->is_active,
                ]);
                
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Department created successfully!'
                ]);
                
                Log::info('Department created', [
                    'id' => $department->id,
                    'user_id' => auth()->id()
                ]);
            }
            
            // Reset and close modal
            $this->reset(['departmentId', 'name', 'code', 'description', 'showModal']);
        } catch (\Exception $e) {
            Log::error('Department save error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // Show delete confirmation
    public function confirmDelete($id)
    {
        $this->departmentToDelete = $id;
        $this->confirmingDeletion = true;
    }

    // Delete department
    public function destroyDepartment()
    {
        try {
            $department = Department::findOrFail($this->departmentToDelete);
            
            // Check if department is in use by papers
            // Uncomment and modify this logic based on your needs
            /*
            if ($department->papers()->count() > 0) {
                throw new \Exception('Cannot delete department that has papers assigned to it');
            }
            */
            
            $departmentName = $department->name;
            $department->delete();
            
            $this->confirmingDeletion = false;
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Department '{$departmentName}' deleted successfully!"
            ]);
            
            Log::info('Department deleted', [
                'id' => $this->departmentToDelete,
                'name' => $departmentName,
                'user_id' => auth()->id()
            ]);
        } catch (\Exception $e) {
            Log::error('Department delete error', [
                'id' => $this->departmentToDelete,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // Render the component
    public function render()
    {
        $departments = Department::query()
            ->when($this->search, function ($query) {
                return $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.department-management', ['departments' => $departments]);
    }
}
