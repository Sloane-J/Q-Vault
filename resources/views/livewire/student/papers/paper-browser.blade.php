<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Browse Past Papers</h1>
        <p class="text-gray-600">Find and download past examination papers</p>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
        <!-- Search Bar -->
        <div class="mb-4">
            <div class="relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search papers by title, course name, or description..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Filters Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <!-- Department Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                <select wire:model.live="selectedDepartment" class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Student Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Program</label>
                <select wire:model.live="selectedStudentType" class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Programs</option>
                    @foreach($studentTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Level Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                <select wire:model.live="selectedLevel" class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Levels</option>
                    @foreach($levels as $level)
                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Semester Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                <select wire:model.live="selectedSemester" class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Semesters</option>
                    @foreach($semesters as $semester)
                        <option value="{{ $semester }}">{{ $semester }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Second Row Filters -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <!-- Exam Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Exam Type</label>
                <select wire:model.live="selectedExamType" class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Types</option>
                    @foreach($examTypes as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Year Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                <select wire:model.live="selectedYear" class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Years</option>
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Clear Filters -->
            <div class="flex items-end">
                <button 
                    wire:click="clearFilters" 
                    class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out"
                >
                    Clear Filters
                </button>
            </div>
        </div>

        <!-- Results Count -->
        <div class="flex justify-between items-center text-sm text-gray-600">
            <span>{{ $papers->total() }} papers found</span>
            <div class="flex items-center space-x-2">
                <span>Sort by:</span>
                <button 
                    wire:click="sortBy('title')" 
                    class="hover:text-blue-600 {{ $sortBy === 'title' ? 'text-blue-600 font-medium' : '' }}"
                >
                    Title
                    @if($sortBy === 'title')
                        <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </button>
                <button 
                    wire:click="sortBy('exam_year')" 
                    class="hover:text-blue-600 {{ $sortBy === 'exam_year' ? 'text-blue-600 font-medium' : '' }}"
                >
                    Year
                    @if($sortBy === 'exam_year')
                        <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </button>
                <button 
                    wire:click="sortBy('created_at')" 
                    class="hover:text-blue-600 {{ $sortBy === 'created_at' ? 'text-blue-600 font-medium' : '' }}"
                >
                    Latest
                    @if($sortBy === 'created_at')
                        <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </button>
            </div>
        </div>
    </div>

    <!-- Papers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        @forelse($papers as $paper)
            <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <!-- Paper Header -->
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $paper->title }}</h3>
                        <p class="text-sm text-gray-600 mb-2">{{ $paper->course_name }}</p>
                        @if($paper->description)
                            <p class="text-sm text-gray-500">{{ Str::limit($paper->description, 100) }}</p>
                        @endif
                    </div>

                    <!-- Paper Details -->
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-xs text-gray-600">
                            <span class="w-20 font-medium">Department:</span>
                            <span>{{ $paper->department->name }}</span>
                        </div>
                        <div class="flex items-center text-xs text-gray-600">
                            <span class="w-20 font-medium">Program:</span>
                            <span>{{ $paper->studentType->name }} - {{ $paper->level->name }}</span>
                        </div>
                        <div class="flex items-center text-xs text-gray-600">
                            <span class="w-20 font-medium">Semester:</span>
                            <span>{{ $paper->semester }}</span>
                        </div>
                        <div class="flex items-center text-xs text-gray-600">
                            <span class="w-20 font-medium">Type:</span>
                            <span>{{ $paper->exam_type }}</span>
                        </div>
                        <div class="flex items-center text-xs text-gray-600">
                            <span class="w-20 font-medium">Year:</span>
                            <span>{{ $paper->exam_year }}</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-2">
                        <button 
                            wire:click="downloadPaper({{ $paper->id }})"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out flex items-center justify-center"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download
                        </button>
                        <a 
                            href="{{ route('papers.view', $paper->id) }}" 
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out flex items-center justify-center"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View
                        </a>
                    </div>

                    <!-- Upload Info -->
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-400">
                            Uploaded {{ $paper->created_at->diffForHumans() }} by {{ $paper->user->name }}
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No papers found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your search criteria or filters.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($papers->hasPages())
        <div class="mt-6">
            {{ $papers->links() }}
        </div>
    @endif
</div>