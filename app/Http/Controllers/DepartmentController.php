<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments.
     */
    public function index()
    {
        $departments = Department::all();
        return view('departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new department.
     */
    public function create()
    {
        return view('departments.create');
    }

    /**
     * Store a newly created department in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:departments|max:255',
            'code' => 'required|unique:departments|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $department = Department::create($validatedData);

        return redirect()->route('departments.index')
            ->with('success', 'Department created successfully.');
    }

    /**
     * Display the specified department.
     */
    public function show(Department $department)
    {
        return view('departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    /**
     * Update the specified department in storage.
     */
    public function update(Request $request, Department $department)
    {
        $validatedData = $request->validate([
            'name' => [
                'required', 
                'max:255', 
                Rule::unique('departments')->ignore($department->id)
            ],
            'code' => [
                'required', 
                'max:50', 
                Rule::unique('departments')->ignore($department->id)
            ],
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $department->update($validatedData);

        return redirect()->route('departments.index')
            ->with('success', 'Department updated successfully');
    }

    /**
     * Remove the specified department from storage.
     */
    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->route('departments.index')
            ->with('success', 'Department deleted successfully');
    }
}