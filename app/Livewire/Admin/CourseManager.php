<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use App\Models\Department;
use App\Models\Level;
use Livewire\Component;
use Livewire\WithPagination;

class CourseManager extends Component
{
    use WithPagination;

    public $name;
    public $code; // <-- Added
    public $description;
    public $department_id;
    public $active = true;
    public $course_id;
    public $isOpen = false;
    public $confirmingDeletion = false;
    public $searchTerm = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:50|unique:courses,code', // <-- Added validation
        'description' => 'nullable|string',
        'department_id' => 'required|exists:departments,id',
        'active' => 'boolean',
    ];

    public function render()
    {
        $searchTerm = '%' . $this->searchTerm . '%';

        return view('livewire.admin.course-manager', [
            'courses' => Course::where('name', 'like', $searchTerm)
                ->orderBy('name')
                ->paginate(10),
            'departments' => Department::orderBy('name')->get(),
            'levels' => Level::orderBy('level_number')->get(),
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->confirmingDeletion = false;
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->code = ''; // <-- Reset
        $this->description = '';
        $this->department_id = '';
        $this->active = true;
        $this->course_id = '';
        $this->resetErrorBag();
    }

    public function store()
    {
        $this->validate();

        Course::create([
            'name' => $this->name,
            'code' => $this->code, // <-- Required
            'description' => $this->description,
            'department_id' => $this->department_id,
            'active' => $this->active,
        ]);

        session()->flash('message', 'Course created successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);
        $this->course_id = $id;
        $this->name = $course->name;
        $this->code = $course->code; // <-- Added
        $this->description = $course->description;
        $this->department_id = $course->department_id;
        $this->active = $course->active;

        $this->openModal();
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courses,code,' . $this->course_id, // <-- Adjusted
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'active' => 'boolean',
        ]);

        $course = Course::find($this->course_id);
        $course->update([
            'name' => $this->name,
            'code' => $this->code, // <-- Added
            'description' => $this->description,
            'department_id' => $this->department_id,
            'active' => $this->active,
        ]);

        session()->flash('message', 'Course updated successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeletion = true;
        $this->course_id = $id;
    }

    public function delete()
    {
        Course::find($this->course_id)->delete();
        session()->flash('message', 'Course deleted successfully.');
        $this->closeModal();
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }
}
