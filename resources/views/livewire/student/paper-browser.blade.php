<div class="min-h-screen bg-neutral-50 dark:bg-neutral-950 text-gray-900 dark:text-gray-100">
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold mb-2 text-gray-900 dark:text-gray-100">Browse Exam Papers</h1>
            <p class="text-gray-600 dark:text-gray-400">Find and download past exam papers for your courses</p>
        </div>

        {{-- Filters Section --}}
        <div
            class="p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm mb-8">
            <div class="mb-6">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500 w-5 h-5"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Search by course name, title, or description..."
                        class="w-full pl-10 pr-4 py-3 border border-neutral-300 dark:border-neutral-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department</label>
                    <select wire:model.live="selectedDepartment"
                        class="w-full border border-neutral-300 dark:border-neutral-600 rounded-md px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                        <option value="" class="dark:bg-neutral-800 dark:text-gray-100">All Departments</option>
                        @if (isset($departments) && $departments->count() > 0)
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" class="dark:bg-neutral-800 dark:text-gray-100">
                                    {{ $department->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Level</label>
                    <select wire:model.live="selectedLevel"
                        class="w-full border border-neutral-300 dark:border-neutral-600 rounded-md px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                        <option value="" class="dark:bg-neutral-800 dark:text-gray-100">All Levels</option>
                        @if (isset($levels) && $levels->count() > 0)
                            @foreach ($levels as $level)
                                <option value="{{ $level->id }}" class="dark:bg-neutral-800 dark:text-gray-100">
                                    {{ $level->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Course</label>
                    <select wire:model.live="selectedCourse"
                        class="w-full border border-neutral-300 dark:border-neutral-600 rounded-md px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                        <option value="" class="dark:bg-neutral-800 dark:text-gray-100">All Courses</option>
                        @if (isset($availableCourses) && $availableCourses->count() > 0)
                            @foreach ($availableCourses as $course)
                                <option value="{{ $course->id }}" class="dark:bg-neutral-800 dark:text-gray-100">
                                    {{ $course->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Year</label>
                    <select wire:model.live="selectedYear"
                        class="w-full border border-neutral-300 dark:border-neutral-600 rounded-md px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white dark:bg-neutral-800 text-gray-900 dark:text-gray-100">
                        <option value="" class="dark:bg-neutral-800 dark:text-gray-100">All Years</option>
                        @if (isset($availableYears) && $availableYears->count() > 0)
                            @foreach ($availableYears as $year)
                                <option value="{{ $year }}" class="dark:bg-neutral-800 dark:text-gray-100">
                                    {{ $year }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>


            </div>

            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <button wire:click="clearFilters"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 border border-neutral-300 dark:border-neutral-600 rounded-md hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors bg-white dark:bg-neutral-800">
                        Clear Filters
                    </button>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        {{ isset($papers) ? $papers->total() : 0 }} papers found
                    </span>
                </div>
            </div>
        </div>

       {{-- Papers Grid --}}
        @if (isset($papers) && $papers->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4 mb-8">
                @foreach ($papers as $paper)
                    <div
                        class="bg-white dark:bg-neutral-900 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700 overflow-hidden hover:shadow-lg transition-all duration-300 group">
                        {{-- Top Section with Icon and Year Badge --}}
                        <div
                            class="relative bg-gradient-to-br from-red-100 to-red-200 dark:from-red-700/30 dark:to-red-600 p-4 h-32 flex items-center justify-center">
                            {{-- Year Badge --}}
                            <div class="absolute top-2 right-2">
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-white/90 dark:bg-neutral-800/90 text-red-700 dark:text-red-300 shadow-sm">
                                    {{ $paper->exam_year }}
                                </span>
                            </div>

                            {{-- Document Icon --}}
                            <div
                                class="w-12 h-12 bg-white/20 dark:bg-white/10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                            </div>
                        </div>

                        {{-- Content Section --}}
                        <div class="p-4">
                            {{-- Course Name --}}
                            <h3
                                class="font-bold text-gray-900 dark:text-gray-100 text-sm mb-2 line-clamp-2 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">
                                {{ $paper->course_name }}
                            </h3>

                            {{-- Department Name (Bold) --}}
                            <p
                                class="font-bold text-gray-800 dark:text-gray-100 text-xs mb-2 group-hover:text-primary-600 transition-colors duration-300 truncate">
                                {{ $paper->department->name ?? 'Unknown Department' }}
                            </p>

                            {{-- Course ID and Level Info in one line separated by dots --}}
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 line-clamp-2">
                                <span
                                    class="text-xs text-gray-400 dark:text-gray-300">{{ $paper->course->name ?? 'N/A' }}</span>
                                <span class="font-bold text-gray-400 dark:text-gray-300">•</span>
                                <span
                                    class=" text-gray-400 dark:text-gray-300">{{ $paper->level->name ?? 'N/A' }}</span>

                                <span class="font-bold text-gray-400 dark:text-gray-300">•</span>
                                <span class=" text-gray-400 dark:text-gray-300">{{ $paper->formatted_semester }}</span>

                                @if (isset($paper->exam_type))
                                    <span class="font-bold text-gray-400 dark:text-gray-300">•</span>
                                    <span
                                        class="text-gray-400 dark:text-gray-300">{{ ucfirst($paper->exam_type) }}</span>
                                @endif
                                @if (isset($paper->student_type))
                                    <span class="font-bold text-gray-400 dark:text-gray-300">•</span>
                                    <span class=" text-gray-400 dark:text-gray-300">{{ $paper->student_type }}</span>
                                @endif
                            </p>

                            {{-- Footer --}}
                            <div
                                class="flex items-center justify-between pt-3 border-t border-neutral-100 dark:border-neutral-700">
                                <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                    {{ $paper->downloads_count ?? 0 }} downloads
                                </div>

                                <button wire:click="downloadPaper({{ $paper->id }})"
                                    class="w-8 h-8 bg-red-600 dark:bg-red-500 hover:bg-red-700 dark:hover:bg-red-600 text-white rounded-lg flex items-center justify-center transition-all duration-200 group-hover:scale-105">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if (isset($papers) && $papers->hasPages())
                <div class="flex justify-center mt-8">
                    {{ $papers->links() }}
                </div>
            @endif
        @else
            <div
                class="p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm text-center py-10">
                <div
                    class="w-24 h-24 mx-auto mb-6 bg-neutral-100 dark:bg-neutral-800 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">No papers found</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Try adjusting your search criteria or filters to find
                    exam papers.</p>
                <button wire:click="clearFilters"
                    class="inline-flex items-center px-6 py-3 bg-red-500 hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                    Clear All Filters
                </button>
            </div>
        @endif
    </div>
</div>
