<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800 dark:text-neutral-200 leading-tight">
            {{ __('Course Management') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid gap-6 md:grid-cols-3 w-full mb-6">
                <!-- Total Courses -->
                <div
                    class="flex justify-between items-center p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                    <div class="flex flex-col justify-center">
                        <h3 class="text-sm font-medium text-neutral-500 dark:text-neutral-400 mb-1">Total Courses</h3>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ \App\Models\Course::count() }}
                        </p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-700 dark:text-blue-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>

                <!-- Active Courses -->
                <div
                    class="flex justify-between items-center p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                    <div class="flex flex-col justify-center">
                        <h3 class="text-sm font-medium text-neutral-500 dark:text-neutral-400 mb-1">Active Courses</h3>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-white">
                            {{ \App\Models\Course::where('active', true)->count() }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-green-100 text-green-600 dark:bg-green-700 dark:text-green-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Departments -->
                <div
                    class="flex justify-between items-center p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
                    <div class="flex flex-col justify-center">
                        <h3 class="text-sm font-medium text-neutral-500 dark:text-neutral-400 mb-1">Departments</h3>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-white">
                            {{ \App\Models\Department::count() }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-700 dark:text-blue-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Search and Add Button -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div class="w-full md:w-1/3">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-neutral-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input wire:model.live="searchTerm" type="text" placeholder="Search courses..."
                            class="block w-full pl-10 pr-3 py-2 border border-neutral-300 dark:border-neutral-700 rounded-md leading-5 bg-white dark:bg-neutral-900 placeholder-neutral-500 dark:placeholder-neutral-400 focus:outline-none focus:ring-white-500 focus:border-white-500 sm:text-sm text-neutral-900 dark:text-white">
                    </div>
                </div>
                <button wire:click="create"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white-500 dark:bg-blue-500 dark:hover:bg-blue-600">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Add New Course
                </button>
            </div>

            <!-- Courses Table -->
            <div
                class="bg-white dark:bg-neutral-900 overflow-hidden shadow-sm sm:rounded-lg border border-neutral-200 dark:border-neutral-700">
                @if (session()->has('message'))
                    <div
                        class="mb-4 bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 dark:border-green-700 p-4 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-700 dark:text-green-400" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-800 dark:text-green-300">{{ session('message') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                        <thead class="bg-neutral-50 dark:bg-neutral-800">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                                    Code</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                                    Name</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                                    Department</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                                    Status</th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-neutral-900 divide-y divide-neutral-200 dark:divide-neutral-700">
                            @forelse ($courses as $course)
                                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800">
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900 dark:text-white">
                                        {{ $course->code }}</td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-neutral-900 dark:text-white">
                                            {{ $course->name }}</div>
                                        @if ($course->description)
                                            <div
                                                class="text-sm text-neutral-500 dark:text-neutral-400 truncate max-w-xs">
                                                {{ $course->description }}</div>
                                        @endif
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                        {{ $course->department->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $course->active ? 'bg-green-100 text-green-500 dark:bg-green-700 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-200' }}">
                                            {{ $course->active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click="edit({{ $course->id }})"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200 mr-3"><svg class="w-5 h-5"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>
                                        <button wire:click="confirmDelete({{ $course->id }})"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200"><svg
                                                class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5"
                                        class="px-6 py-4 text-center text-sm text-neutral-500 dark:text-neutral-400">
                                        No courses found. Create one to get started.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 border-t border-neutral-200 dark:border-neutral-700">
                    {{ $courses->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="fixed inset-0 z-10 overflow-y-auto" style="display: {{ $isOpen ? 'block' : 'none' }}">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-neutral-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white dark:bg-neutral-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                @if ($confirmingDeletion)
                    <div class="bg-white dark:bg-neutral-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-700 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600 dark:text-red-200" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-neutral-900 dark:text-white"
                                    id="modal-headline">
                                    Delete Course
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                                        Are you sure you want to delete this course? This action cannot be undone.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-neutral-50 dark:bg-neutral-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="delete" type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm dark:bg-red-500 dark:hover:bg-red-600">
                            Delete
                        </button>
                        <button wire:click="closeModal" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-neutral-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-neutral-700 hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-neutral-700 dark:text-white dark:border-neutral-600 dark:hover:bg-neutral-600">
                            Cancel
                        </button>
                    </div>
                @else
                    <form>
                        <div class="bg-white dark:bg-neutral-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div
                                    class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-700 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-200"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-neutral-900 dark:text-white">
                                        {{ $course_id ? 'Edit Course' : 'Create New Course' }}
                                    </h3>
                                </div>
                            </div>

                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="code"
                                        class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Course
                                        Code</label>
                                    <input type="text" id="name" wire:model.defer="name"
                                        class="block w-full px-4 py-2 text-base rounded-lg shadow-sm
           transition ease-in-out duration-150
           bg-white dark:bg-neutral-800
           text-neutral-900 dark:text-white
           border border-neutral-300 dark:border-neutral-700
           placeholder-neutral-500 dark:placeholder-neutral-400
           focus:outline-none focus:ring-2 ">
                                    @error('code')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="name"
                                        class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Course
                                        Name</label>
                                    <input type="text" id="name" wire:model.defer="name"
                                        class="block w-full px-4 py-2 text-base rounded-lg shadow-sm
           transition ease-in-out duration-150
           bg-white dark:bg-neutral-800
           text-neutral-900 dark:text-white
           border border-neutral-300 dark:border-neutral-700
           placeholder-neutral-500 dark:placeholder-neutral-400
           focus:outline-none focus:ring-2 ">
                                    @error('name')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="description"
                                        class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Description
                                        (Optional)</label>
                                    <textarea id="description" wire:model.defer="description" rows="3"
                                        class="block w-full px-4 py-2 text-base rounded-lg shadow-sm
           resize-y transition ease-in-out duration-150
           bg-white dark:bg-neutral-800
           text-neutral-900 dark:text-white
           border border-neutral-300 dark:border-neutral-700
           placeholder-neutral-500 dark:placeholder-neutral-400
           focus:outline-none focus:ring-2"
                                        placeholder="Add a description..."></textarea>
                                    @error('description')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="department_id"
                                        class="block text-sm font-medium">Department</label>
                                    <select id="department_id" wire:model.defer="department_id"
    class="block w-full px-4 py-2 text-base rounded-lg shadow-sm
           transition ease-in-out duration-150
           bg-white dark:bg-neutral-800
           text-neutral-900 dark:text-neutral-500
           border border-neutral-300 dark:border-neutral-500
           focus:outline-none focus:ring-1">
    <option value="">Select Department</option>
    @foreach ($departments as $department)
        <option value="{{ $department->id }}">{{ $department->name }}</option>
    @endforeach
</select>
                                    @error('department_id')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="flex items-center">
                                    <input id="active" wire:model.defer="active" type="checkbox"
                                        class="h-4 w-4 text-blue-600 focus:ring-white-500 border-neutral-300 rounded dark:bg-neutral-900 dark:border-neutral-700 dark:checked:bg-blue-500">
                                    <label for="active"
                                        class="ml-2 block text-sm text-neutral-900 dark:text-neutral-300">
                                        Active Course
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="bg-neutral-50 dark:bg-neutral-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button wire:click.prevent="{{ $course_id ? 'update' : 'store' }}" type="button"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white-500 sm:ml-3 sm:w-auto sm:text-sm dark:bg-blue-500 dark:hover:bg-blue-600">
                                {{ $course_id ? 'Update Course' : 'Create Course' }}
                            </button>
                            <button wire:click="closeModal" type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-neutral-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-neutral-700 hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-neutral-700 dark:text-white dark:border-neutral-600 dark:hover:bg-neutral-600">
                                Cancel
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
