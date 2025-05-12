<?php

namespace App\Livewire;

use App\Models\Department;
use Livewire\Component;
use Livewire\WithPagination;

class DepartmentManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $confirmingDeletion = false;

    // Department form fields
    public $departmentId;
    public $name;
    public $code;
    public $description;
    public $is_active = true; // Default to active

    public function mount()
    {
        // We will fetch departments in the render method for pagination and search
    }

    public function render()
    {
        $departments = Department::when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%');
        })
        ->paginate(10); // Adjust the number per page as needed

        return view('livewire.department-management', [
            'departments' => $departments,
        ]);
    }

    // Placeholder methods (will be implemented later)
    public function openCreateModal()
    {
        $this->reset(['departmentId', 'name', 'code', 'description', 'is_active']);
        $this->showModal = true;
    }

    public function openEditModal($id) {}

    public function confirmDelete($id) {}

    public function saveDepartment() {}

    public function destroyDepartment() {}
}
