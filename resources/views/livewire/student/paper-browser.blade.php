<div class="min-h-screen bg-neutral-50 dark:bg-neutral-950 text-gray-900 dark:text-gray-100"> {{-- Adjusted overall background and default text colors --}}
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold mb-2 text-gray-900 dark:text-gray-100">Browse Exam Papers</h1>
            <p class="text-gray-600 dark:text-gray-400">Find and download past exam papers for your courses</p>
        </div>

        {{-- Filters Section --}}
        <div class="p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm mb-8">
            <div class="mb-6">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search by course name, title, or description..."
                        class="w-full pl-10 pr-4 py-3 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500"
                    >
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department</label>
                    <select wire:model.live="selectedDepartment" class="w-full border border-neutral-300 dark:border-neutral-600 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                        <option value="" class="dark:bg-neutral-800 dark:text-gray-100">All Departments</option>
                        @if(isset($departments) && $departments->count() > 0)
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" class="dark:bg-neutral-800 dark:text-gray-100">{{ $department->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Level</label>
                    <select wire:model.live="selectedLevel" class="w-full border border-neutral-300 dark:border-neutral-600 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                        <option value="" class="dark:bg-neutral-800 dark:text-gray-100">All Levels</option>
                        @if(isset($levels) && $levels->count() > 0)
                            @foreach($levels as $level)
                                <option value="{{ $level->id }}" class="dark:bg-neutral-800 dark:text-gray-100">Level {{ $level->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Course</label>
                    <select wire:model.live="selectedCourse" class="w-full border border-neutral-300 dark:border-neutral-600 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                        <option value="" class="dark:bg-neutral-800 dark:text-gray-100">All Courses</option>
                        @if(isset($availableCourses) && $availableCourses->count() > 0)
                            @foreach($availableCourses as $course)
                                <option value="{{ $course }}" class="dark:bg-neutral-800 dark:text-gray-100">{{ $course }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Year</label>
                    <select wire:model.live="selectedYear" class="w-full border border-neutral-300 dark:border-neutral-600 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                        <option value="" class="dark:bg-neutral-800 dark:text-gray-100">All Years</option>
                        @if(isset($availableYears) && $availableYears->count() > 0)
                            @foreach($availableYears as $year)
                                <option value="{{ $year }}" class="dark:bg-neutral-800 dark:text-gray-100">{{ $year }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <button
                        wire:click="clearFilters"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border border-neutral-300 dark:border-neutral-600 rounded-md hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors bg-white dark:bg-neutral-800"
                    >
                        Clear Filters
                    </button>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        {{ isset($papers) ? $papers->total() : 0 }} papers found
                    </span>
                </div>
            </div>
        </div>

        {{-- Papers Grid --}}
        @if(isset($papers) && $papers->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                @foreach($papers as $paper)
                    <div class="p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm overflow-hidden hover:shadow-lg transition-all duration-300 group"> {{-- Applied card design --}}
                        <div class="relative pb-4">
                            <div class="absolute top-0 right-0"> {{-- Adjusted positioning for better spacing --}}
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{
                                        $paper->exam_year >= 2024 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' :
                                        ($paper->exam_year >= 2022 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' :
                                        'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300')
                                    }}">
                                    {{ $paper->exam_year }}
                                </span>
                            </div>

                            <div class="w-12 h-12 rounded-lg mb-4 flex items-center justify-center
                                {{
                                    $paper->exam_year >= 2024 ? 'bg-green-100 dark:bg-green-900' :
                                    ($paper->exam_year >= 2022 ? 'bg-blue-100 dark:bg-blue-900' : 'bg-purple-100 dark:bg-purple-900')
                                }}">
                                <svg class="w-6 h-6
                                    {{
                                        $paper->exam_year >= 2024 ? 'text-green-600 dark:text-green-400' :
                                        ($paper->exam_year >= 2022 ? 'text-blue-600 dark:text-blue-400' : 'text-purple-600 dark:text-purple-400')
                                    }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>

                            <h3 class="font-bold text-gray-900 dark:text-gray-100 text-lg mb-2 line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                {{ $paper->course_name }}
                            </h3>

                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">
                                {{ $paper->department->name ?? 'Unknown Department' }}
                            </p>

                            <div class="space-y-2 mb-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Level</span>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ $paper->level->name ?? 'N/A' }}
                                    </span>
                                </div>

                                @if($paper->title && $paper->title !== $paper->course_name)
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Title</span>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate ml-2">
                                        {{ $paper->title }}
                                    </span>
                                </div>
                                @endif

                                @if($paper->description)
                                <div class="pt-2">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Description</span>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">
                                        {{ $paper->description }}
                                    </p>
                                </div>
                                @endif
                            </div>

                            <div class="flex items-center justify-between pt-4 border-t border-neutral-100 dark:border-neutral-700">
                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                                    </svg>
                                    {{ $paper->downloads_count ?? 0 }} downloads
                                </div>

                                <button
                                    wire:click="downloadPaper({{ $paper->id }})"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors group-hover:bg-blue-700 dark:group-hover:bg-blue-600"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Download
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if(isset($papers) && $papers->hasPages())
                <div class="flex justify-center mt-8">
                    {{ $papers->links() }}
                </div>
            @endif
        @else
            <div class="p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm text-center py-10"> {{-- Applied card design --}}
                <div class="w-24 h-24 mx-auto mb-6 bg-neutral-100 dark:bg-neutral-800 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">No papers found</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Try adjusting your search criteria or filters to find exam papers.</p>
                <button
                    wire:click="clearFilters"
                    class="inline-flex items-center px-6 py-3 bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white font-medium rounded-lg transition-colors"
                >
                    Clear All Filters
                </button>
            </div>
        @endif
    </div>

    {{-- Loading Spinner --}}
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm flex items-center space-x-4">
            <svg class="animate-spin h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-700 dark:text-gray-300 font-medium">Loading papers...</span>
        </div>
    </div>
</div>

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush