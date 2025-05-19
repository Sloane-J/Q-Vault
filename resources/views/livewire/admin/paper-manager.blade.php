<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

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


                {{-- Redirect to separate paper-uploader page --}}
                <div class="mb-4 text-right">
                    <a href="{{ route('papers.paper-uploader') }}" 
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium shadow-sm inline-block">
                        Upload New Paper
                    </a>
                </div>



                {{-- Papers List with Filters --}}
                <h2 class="text-lg font-semibold mb-4">Manage Papers</h2>

                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    {{-- Search and Reset --}}
                    <div class="flex flex-col md:flex-row md:items-center md:space-x-4">
                        <div class="flex-grow mb-2 md:mb-0">
                            <input type="text" wire:model.debounce.300ms="search" placeholder="Search papers..." class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <button wire:click="resetFilters" class="px-3 py-1.5 text-sm bg-white text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50 shadow-sm">
                                Reset Filters
                            </button>
                        </div>
                    </div>

                    {{-- Filters --}}
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2 mt-3">
                        <select wire:model="departmentFilter" class="filter-select">
                            <option value="">All Departments</option>
                            @foreach($this->departments as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>

                        <select wire:model="yearFilter" class="filter-select">
                            <option value="">All Years</option>
                            @foreach($years as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>

                        <select wire:model="levelFilter" class="filter-select">
                            <option value="">All Levels</option>
                            @foreach($levels as $lvl)
                                <option value="{{ $lvl }}">{{ $lvl }}</option>
                            @endforeach
                        </select>

                        <select wire:model="examTypeFilter" class="filter-select">
                            <option value="">All Exam Types</option>
                            @foreach($examTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>

                        <select wire:model="studentTypeFilter" class="filter-select">
                            <option value="">All Student Types</option>
                            @foreach($studentTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>

                        <select wire:model="semesterFilter" class="filter-select">
                            <option value="">All Semesters</option>
                            <option value="1">Semester 1</option>
                            <option value="2">Semester 2</option>
                        </select>
                    </div>
                </div>

                {{-- Papers Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="table-head">Title</th>
                                <th class="table-head">Course</th>
                                <th class="table-head">Details</th>
                                <th class="table-head">Status</th>
                                <th class="table-head">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($papers as $paper)
                                <tr>
                                    <td class="table-cell font-medium text-gray-900">{{ $paper->title }}</td>
                                    <td class="table-cell">{{ $paper->course_name }}</td>
                                    <td class="table-cell text-sm text-gray-600">
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
                                        <a href="{{ Storage::url($paper->file_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">View</a>
                                        <button wire:click="editPaper({{ $paper->id }})" class="text-blue-600 hover:text-blue-900">Edit</button>
                                        <button wire:click="confirmDelete({{ $paper->id }})" class="text-red-600 hover:text-red-900">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-gray-500">No papers found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $papers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Optional Tailwind Helper Classes --}}
@push('styles')
<style>
    .filter-select {
        @apply block w-full py-1.5 px-2 text-sm border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500;
    }
    .table-head {
        @apply px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider;
    }
    .table-cell {
        @apply px-6 py-4 whitespace-nowrap text-sm text-gray-700;
    }
</style>
@endpush
