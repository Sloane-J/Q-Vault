<div class="dark:bg-neutral-900 min-h-screen">
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm overflow-hidden p-6">
            {{-- Session Flash Messages --}}
            @if (session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('message') }}</span>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Form for Adding/Editing Papers --}}
            @if($showForm)
                <div class="mb-6 p-4 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900">
                    <h3 class="text-lg font-medium mb-4">{{ $paperId ? 'Edit Paper' : 'Add New Paper' }}</h3>
                    
                    <form wire:submit.prevent="savePaper">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                            <input
                                type="text"
                                id="title"
                                wire:model="title"
                                class="mt-1 block w-full rounded-md
                                    border border-neutral-300 dark:border-neutral-700
                                    bg-white dark:bg-neutral-800
                                    text-neutral-800 dark:text-neutral-200
                                    shadow-sm
                                    focus:border-blue-500 focus:ring-blue-500 focus:ring-1
                                    transition ease-in-out duration-150
                                    py-2 px-3
                                    sm:text-sm
                                    placeholder-neutral-400 dark:placeholder-neutral-500"
                                placeholder="Enter paper title"
                            >
                            @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <!-- Department Dropdown -->
                                <div>
                                    <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Department</label>
                                    <select
                                        id="department_id"
                                        wire:model.live="department_id"
                                        class="block w-full rounded-md
                                            border border-neutral-300 dark:border-neutral-700
                                            bg-white dark:bg-neutral-800
                                            text-neutral-800 dark:text-neutral-200
                                            focus:border-blue-500 focus:ring-blue-500 focus:ring-1
                                            transition ease-in-out duration-150
                                            py-2 px-3 sm:text-sm"
                                    >
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('department_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <!-- Course Dropdown -->
                                <div>
                                    <label for="course_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Course Name</label>
                                    <select 
                                        id="course_id" 
                                        wire:model.live="course_id" 
                                        class="mt-1 block w-full rounded-md
                                                border border-neutral-300 dark:border-neutral-700
                                                bg-white dark:bg-neutral-800
                                                text-neutral-800 dark:text-neutral-200
                                                shadow-sm
                                                focus:border-blue-500 focus:ring-blue-500 focus:ring-1
                                                transition ease-in-out duration-150
                                                py-2 px-3
                                                sm:text-sm
                                                {{ !$department_id ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        {{ !$department_id ? 'disabled' : '' }}
                                    >
                                        <option value="">
                                            {{ $department_id ? 'Select a course' : 'Select department first' }}
                                        </option>
                                        @if($department_id && count($filteredCourses) > 0)
                                            @foreach($filteredCourses as $course)
                                                <option value="{{ $course->id }}">{{ $course->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('course_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            
                            <div>
                                <label for="exam_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Year</label>
                                <select id="exam_year" wire:model="exam_year" class="block w-full rounded-md
                                        border border-neutral-300 dark:border-neutral-700
                                        bg-white dark:bg-neutral-800
                                        text-neutral-800 dark:text-neutral-200
                                        focus:border-blue-500 focus:ring-blue-500 focus:ring-1
                                        transition ease-in-out duration-150
                                        py-2 px-3 sm:text-sm">
                                    <option value="">Select Year</option>
                                    @foreach($years as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                                @error('exam_year') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="exam_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Exam Type</label>
                                <select id="exam_type" wire:model="exam_type" class="block w-full rounded-md
                                        border border-neutral-300 dark:border-neutral-700
                                        bg-white dark:bg-neutral-800
                                        text-neutral-800 dark:text-neutral-200
                                        focus:border-blue-500 focus:ring-blue-500 focus:ring-1
                                        transition ease-in-out duration-150
                                        py-2 px-3 sm:text-sm">
                                    <option value="">Select Type</option>
                                    @foreach($examTypes as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                                @error('exam_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="semester" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Semester</label>
                                <select id="semester" wire:model="semester" class="block w-full rounded-md
                                        border border-neutral-300 dark:border-neutral-700
                                        bg-white dark:bg-neutral-800
                                        text-neutral-800 dark:text-neutral-200
                                        focus:border-blue-500 focus:ring-blue-500 focus:ring-1
                                        transition ease-in-out duration-150
                                        py-2 px-3 sm:text-sm">
                                    <option value="">Select Semester</option>
                                    <option value="1">Semester 1</option>
                                    <option value="2">Semester 2</option>
                                </select>
                                @error('semester') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="student_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Student Type</label>
                                <select id="student_type" wire:model="student_type" class="block w-full rounded-md
                                        border border-neutral-300 dark:border-neutral-700
                                        bg-white dark:bg-neutral-800
                                        text-neutral-800 dark:text-neutral-200
                                        focus:border-blue-500 focus:ring-blue-500 focus:ring-1
                                        transition ease-in-out duration-150
                                        py-2 px-3 sm:text-sm">
                                    <option value="">Select Student Type</option>
                                    @foreach($studentTypes as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                                @error('student_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="level" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Level</label>
                                <select id="level" wire:model="level" class="block w-full rounded-md
                                        border border-neutral-300 dark:border-neutral-700
                                        bg-white dark:bg-neutral-800
                                        text-neutral-800 dark:text-neutral-200
                                        focus:border-blue-500 focus:ring-blue-500 focus:ring-1
                                        transition ease-in-out duration-150
                                        py-2 px-3 sm:text-sm">
                                    <option value="">Select Level</option>
                                    @foreach($levels as $lvl)
                                        <option value="{{ $lvl }}">{{ $lvl }}</option>
                                    @endforeach
                                </select>
                                @error('level') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="visibility" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Visibility</label>
                                <select id="visibility" wire:model="visibility" class="block w-full rounded-md
                                        border border-neutral-300 dark:border-neutral-700
                                        bg-white dark:bg-neutral-800
                                        text-neutral-800 dark:text-neutral-200
                                        focus:border-blue-500 focus:ring-blue-500 focus:ring-1
                                        transition ease-in-out duration-150
                                        py-2 px-3 sm:text-sm">
                                    <option value="public">Public</option>
                                    <option value="restricted">Restricted</option>
                                </select>
                                @error('visibility') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                <textarea id="description" wire:model="description" rows="3" class="mt-1 block w-full rounded-md border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                                @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="md:col-span-2">
                                <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">PDF File</label>
                                <input type="file" id="file" wire:model="file" accept=".pdf" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-blue-50 file:text-blue-700
                                    hover:file:bg-blue-100
                                ">
                                @if($existingFilePath)
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Current file: {{ basename($existingFilePath) }}</p>
                                @endif
                                <div wire:loading wire:target="file" class="mt-1 text-xs text-blue-600">Uploading...</div>
                                @error('file') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="mt-4 flex justify-end space-x-3">
                            <button type="button" wire:click="toggleForm" class="px-4 py-2 bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ $paperId ? 'Update Paper' : 'Save Paper' }}
                            </button>
                        </div>
                    </form>
                </div>
            @else
                {{-- Add Button --}}
                <div class="mb-4 text-right">
                    <button wire:click="toggleForm" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium shadow-sm inline-block">
                        Add New Paper
                    </button>
                </div>
            @endif

            {{-- Papers List with Filters --}}
            <h2 class="text-lg font-semibold mb-4 dark:text-white">Manage Papers</h2>

            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 p-4 mb-4">
                {{-- Search and Reset --}}
                <div class="flex flex-col md:flex-row md:items-center md:space-x-4">
                    <div class="flex-grow mb-2 md:mb-0">
                        <input type="text" wire:model.debounce.300ms="search" placeholder="Search papers..." class="pl-10 pr-4 py-2 w-full rounded-md border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 focus:border-blue-500 focus:ring-blue-500 focus:ring-1 transition ease-in-out duration-150 text-base placeholder-neutral-400 dark:placeholder-neutral-500">
                    </div>
                    <div>
                        <button wire:click="resetFilters" class="px-3 py-1.5 text-sm bg-white dark:bg-neutral-800 text-gray-700 dark:text-gray-300 border border-neutral-200 dark:border-neutral-700 rounded-md hover:bg-gray-50 dark:hover:bg-neutral-700 shadow-sm">
                            Reset Filters
                        </button>
                    </div>
                </div>

                {{-- Filters --}}
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2 mt-3">
                    <select
                        wire:model="departmentFilter"
                        class="filter-select
                            pl-10 pr-4 py-2 w-full rounded-md
                            border border-neutral-300 dark:border-neutral-700
                            bg-white dark:bg-neutral-800
                            text-neutral-800 dark:text-neutral-200
                            focus:border-blue-500 focus:ring-blue-500 focus:ring-1
                            transition ease-in-out duration-150 text-base"
                    >
                        <option value="">All Departments</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>

                    <select wire:model="yearFilter" class="filter-select
                            pl-10 pr-4 py-2 w-full rounded-md
                            border border-neutral-300 dark:border-neutral-700
                            bg-white dark:bg-neutral-800
                            text-neutral-800 dark:text-neutral-200
                            focus:border-blue-500 focus:ring-blue-500 focus:ring-1
                            transition ease-in-out duration-150 text-base" >
                        <option value="">All Years</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>

                    <select wire:model="levelFilter" class="filter-select
                            pl-10 pr-4 py-2 w-full rounded-md
                            border border-neutral-300 dark:border-neutral-700
                            bg-white dark:bg-neutral-800
                            text-neutral-800 dark:text-neutral-200
                            focus:border-blue-500 focus:ring-blue-500 focus:ring-1
                            transition ease-in-out duration-150 text-base">
                        <option value="">All Levels</option>
                        @foreach($levels as $lvl)
                            <option value="{{ $lvl }}">{{ $lvl }}</option>
                        @endforeach
                    </select>

                    <select wire:model="examTypeFilter" class="filter-select
                            pl-10 pr-4 py-2 w-full rounded-md
                            border border-neutral-300 dark:border-neutral-700
                            bg-white dark:bg-neutral-800
                            text-neutral-800 dark:text-neutral-200
                            focus:border-blue-500 focus:ring-blue-500 focus:ring-1
                            transition ease-in-out duration-150 text-base">
                        <option value="">All Exam Types</option>
                        @foreach($examTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>

                    <select wire:model="studentTypeFilter" class="filter-select
                            pl-10 pr-4 py-2 w-full rounded-md
                            border border-neutral-300 dark:border-neutral-700
                            bg-white dark:bg-neutral-800
                            text-neutral-800 dark:text-neutral-200
                            focus:border-blue-500 focus:ring-blue-500 focus:ring-1
                            transition ease-in-out duration-150 text-base">
                        <option value="">All Student Types</option>
                        @foreach($studentTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>

                    <select wire:model="semesterFilter" class="filter-select
                            pl-10 pr-4 py-2 w-full rounded-md
                            border border-neutral-300 dark:border-neutral-700
                            bg-white dark:bg-neutral-800
                            text-neutral-800 dark:text-neutral-200
                            focus:border-blue-500 focus:ring-blue-500 focus:ring-1
                            transition ease-in-out duration-150 text-base">
                        <option value="">All Semesters</option>
                        <option value="1">Semester 1</option>
                        <option value="2">Semester 2</option>
                    </select>
                </div>
            </div>

            {{-- Papers Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                    <thead class="bg-neutral-50 dark:bg-neutral-800 border-b border-neutral-200 dark:border-neutral-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-neutral-900 divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse($papers as $paper)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800">
                                <td class="table-cell font-medium text-gray-900 dark:text-white">{{ $paper->title }}</td>
                                <td class="table-cell dark:text-gray-300">{{ $paper->course_name }}</td>
                                <td class="table-cell text-sm text-gray-600 dark:text-gray-400">
                                    <div>{{ $paper->department->name ?? 'N/A' }}</div>
                                    <div>{{ $paper->exam_type }} • {{ $paper->exam_year }}</div>
                                    <div>Level: {{ $paper->level }}, Semester: {{ $paper->semester }}</div>
                                    <div>Type: {{ $paper->student_type }}</div>
                                </td>
                                <td class="table-cell">
                                    @if($paper->visibility === 'public')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Public
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Restricted
                                        </span>
                                    @endif
                                </td>
                                <td class="table-cell text-sm space-x-2">
                                    <a href="{{ Storage::url($paper->file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">View</a>
                                    <button wire:click="editPaper({{ $paper->id }})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">Edit</button>
                                    <button wire:click="confirmDelete({{ $paper->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-gray-500 dark:text-gray-400">No papers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $papers->links() }}
            </div>

            {{-- Delete Confirmation Modal --}}
            @if($confirmingDeletion)
            <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white dark:bg-neutral-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white dark:bg-neutral-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                        Delete Paper
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Are you sure you want to delete this paper? This action cannot be undone.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-neutral-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button wire:click="deletePaper" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Delete
                            </button>
                            <button wire:click="$set('confirmingDeletion', false)" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-neutral-200 dark:border-neutral-700 shadow-sm px-4 py-2 bg-white dark:bg-neutral-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
</div>

{{-- Tailwind Helper Classes --}}
@push('styles')
<style>
    .filter-select {
        @apply block w-full py-1.5 px-2 text-sm border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-gray-700 dark:text-gray-300;
    }
    .table-head {
        @apply px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider;
    }
    .table-cell {
        @apply px-6 py-4 whitespace-nowrap text-sm;
    }
</style>
@endpush