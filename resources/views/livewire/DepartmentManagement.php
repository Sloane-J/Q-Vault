<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Department;
use Illuminate\Support\Facades\Log;

class DepartmentManagement extends Component
{
    use WithPagination;

    // Search and filter
    public $search = '';

    // Modal control
    public $showModal = false;
    public $confirmingDeletion = false;

    // Department form fields
    public $departmentId = null;
    public $name = '';
    public $code = '';
    public $description = '';
    public $is_active = true;

    // Validation rules
    protected function rules()
    {
        return [
            'name' => [
                'required', 
                'max:255', 
                $this->departmentId 
                    ? 'unique:departments,name,' . $this->departmentId 
                    : 'unique:departments,name'
            ],
            'code' => [
                'required', 
                'max:50', 
                $this->departmentId 
                    ? 'unique:departments,code,' . $this->departmentId 
                    : 'unique:departments,code'
            ],
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ];
    }

    // Custom validation messages
    protected $messages = [
        'name.unique' => 'A department with this name already exists.',
        'code.unique' => 'A department with this code already exists.',
    ];

    // Reset pagination when searching
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Open create modal
    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset([
            'departmentId', 
            'name', 
            'code', 
            'description', 
            'is_active'
        ]);
        $this->showModal = true;