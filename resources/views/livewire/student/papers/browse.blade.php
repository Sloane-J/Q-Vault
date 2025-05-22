<x-app.layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Browse Exam Papers') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- Search and Filter Section -->
                    <div class="mb-8 bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Search & Filter</h3>
                        <form class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Search Input -->
                            <div class="col-span-1 md:col-span-3">
                                <label for="search" class="block text-sm font-medium text-gray-700">Search Papers</label>
                                <div class="mt-1">
                                    <input type="text" name="search" id="search" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Search by keyword, course name, etc.">
                                </div>
                            </div>

                            <!-- Department Filter -->
                            <div>
                                <label for="department" class="block text-sm font-medium text-gray-700">Department</label>
                                <select id="department" name="department" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="">All Departments</option>
                                    <!-- Dynamic department options would be added here -->
                                </select>
                            </div>

                            <!-- Level Filter -->
                            <div>
                                <label for="level" class="block text-sm font-medium text-gray-700">Level</label>
                                <select id="level" name="level" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="">All Levels</option>
                                    <option value="100">Level 100</option>
                                    <option value="200">Level 200</option>
                                    <option value="300">Level 300</option>
                                    <option value="400">Level 400</option>
                                </select>
                            </div>

                            <!-- Exam Type Filter -->
                            <div>
                                <label for="exam_type" class="block text-sm font-medium text-gray-700">Exam Type</label>
                                <select id="exam_type" name="exam_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="">All Types</option>
                                    <option value="final">Final Exam</option>
                                    <option value="resit">Resit</option>
                                    <option value="mid-sem">Mid-Semester</option>
                                </select>
                            </div>

                            <!-- Year Filter -->
                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                                <select id="year" name="year" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="">All Years</option>
                                    <!-- Dynamic year options would be added here -->
                                    <option value="2024">2024</option>
                                    <option value="2023">2023</option>
                                    <option value="2022">2022</option>
                                    <option value="2021">2021</option>
                                    <option value="2020">2020</option>
                                </select>
                            </div>

                            <!-- Student Type Filter -->
                            <div>
                                <label for="student_type" class="block text-sm font-medium text-gray-700">Student Type</label>
                                <select id="student_type" name="student_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="">All Types</option>
                                    <option value="hnd">HND</option>
                                    <option value="btech">B-Tech</option>
                                    <option value="topup">Top-up</option>
                                </select>
                            </div>

                            <!-- Semester Filter -->
                            <div>
                                <label for="semester" class="block text-sm font-medium text-gray-700">Semester</label>
                                <select id="semester" name="semester" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="">All Semesters</option>
                                    <option value="1">First Semester</option>
                                    <option value="2">Second Semester</option>
                                </select>
                            </div>

                            <!-- Apply Filters Button -->
                            <div class="col-span-1 md:col-span-3 flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Apply Filters
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Papers List Section -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-medium text-gray-900">Exam Papers</h3>
                        
                        <!-- No papers found state -->
                        <div class="hidden text-center py-10">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No papers found</h3>
                            <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                        </div>
                        
                        <!-- Papers grid -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            <!-- Sample Paper Card 1 -->
                            <div class="bg-white shadow rounded-lg overflow-hidden">
                                <div class="p-4">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-lg font-semibold text-gray-900">Introduction to Computer Science</h4>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Final
                                        </span>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-500">
                                        <p>Computer Science Department</p>
                                        <p>Level 100 • B-Tech • 2023</p>
                                        <p>First Semester</p>
                                    </div>
                                </div>
                                <div class="border-t border-gray-200 bg-gray-50 px-4 py-3">
                                    <div class="flex justify-between">
                                        <button type="button" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
                                            <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Preview
                                        </button>
                                        <button type="button" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
                                            <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                            Download
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sample Paper Card 2 -->
                            <div class="bg-white shadow rounded-lg overflow-hidden">
                                <div class="p-4">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-lg font-semibold text-gray-900">Database Management</h4>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Resit
                                        </span>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-500">
                                        <p>Computer Science Department</p>
                                        <p>Level 200 • HND • 2022</p>
                                        <p>Second Semester</p>
                                    </div>
                                </div>
                                <div class="border-t border-gray-200 bg-gray-50 px-4 py-3">
                                    <div class="flex justify-between">
                                        <button type="button" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
                                            <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Preview
                                        </button>
                                        <button type="button" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
                                            <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                            Download
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sample Paper Card 3 -->
                            <div class="bg-white shadow rounded-lg overflow-hidden">
                                <div class="p-4">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-lg font-semibold text-gray-900">Software Engineering</h4>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Mid-Sem
                                        </span>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-500">
                                        <p>Computer Science Department</p>
                                        <p>Level 300 • Top-up • 2024</p>
                                        <p>First Semester</p>
                                    </div>
                                </div>
                                <div class="border-t border-gray-200 bg-gray-50 px-4 py-3">
                                    <div class="flex justify-between">
                                        <button type="button" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
                                            <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Preview
                                        </button>
                                        <button type="button" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
                                            <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                            Download
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-8 flex justify-center">
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Previous</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                <a href="#" aria-current="page" class="z-10 bg-blue-50 border-blue-500 text-blue-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    1
                                </a>
                                <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    2
                                </a>
                                <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    3
                                </a>
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                    ...
                                </span>
                                <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    8
                                </a>
                                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Next</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app.layout>