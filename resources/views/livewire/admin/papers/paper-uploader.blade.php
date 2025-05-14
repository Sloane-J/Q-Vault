<div>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-semibold mb-6">Upload Exam Paper</h1>
                
                <form wire:submit.prevent="uploadPaper" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Paper File Upload -->
                    <div class="mb-6">
                        <label for="paper_file" class="block text-sm font-medium text-gray-700 mb-2">Paper File (PDF)</label>
                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col rounded-lg border-4 border-dashed w-full h-60 p-10 group text-center">
                                <div class="h-full w-full text-center flex flex-col items-center justify-center">
                                    @if ($paper)
                                        <p class="text-green-500">{{ $paper->getClientOriginalName() }} - Ready to upload!</p>
                                    @else
                                        <svg class="w-10 h-10 text-gray-400 group-hover:text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <p class="pointer-none text-gray-500 pt-2">Drag and drop or click to select a PDF file</p>
                                    @endif
                                </div>
                                <input type="file" wire:model="paper" id="paper_file" class="hidden" accept=".pdf" required />
                            </label>
                        </div>
                        @error('paper') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Department -->
                        <div class="mb-4">
                            <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                            <select wire:model="department_id" id="department_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md" required>
                                <option value="">Select Department</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Course -->
                        <div class="mb-4">
                            <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Course</label>
                            <select wire:model="course_id" id="course_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md" required>
                                <option value="">Select Course</option>
                                @if($department_id)
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->name }} ({{ $course->code }})</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('course_id') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Student Type -->
                        <div class="mb-4">
                            <label for="student_type" class="block text-sm font-medium text-gray-700 mb-2">Student Type</label>
                            <select wire:model="student_type" id="student_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md" required>
                                <option value="">Select Student Type</option>
                                <option value="HND">HND</option>
                                <option value="B-Tech">B-Tech</option>
                                <option value="Top-up">Top-up</option>
                            </select>
                            @error('student_type') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Level -->
                        <div class="mb-4">
                            <label for="level" class="block text-sm font-medium text-gray-700 mb-2">Level</label>
                            <select wire:model="level" id="level" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md" required>
                                <option value="">Select Level</option>
                                @if($student_type === 'HND')
                                    <option value="100">Level 100</option>
                                    <option value="200">Level 200</option>
                                    <option value="300">Level 300</option>
                                @elseif($student_type === 'B-Tech')
                                    <option value="100">Level 100</option>
                                    <option value="200">Level 200</option>
                                    <option value="300">Level 300</option>
                                    <option value="400">Level 400</option>
                                @elseif($student_type === 'Top-up')
                                    <option value="300">Level 300</option>
                                    <option value="400">Level 400</option>
                                @endif
                            </select>
                            @error('level') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Exam Year -->
                        <div class="mb-4">
                            <label for="exam_year" class="block text-sm font-medium text-gray-700 mb-2">Exam Year</label>
                            <select wire:model="exam_year" id="exam_year" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md" required>
                                <option value="">Select Year</option>
                                @php
                                    $currentYear = date('Y');
                                    for ($year = $currentYear; $year >= $currentYear - 10; $year--) {
                                        echo "<option value=\"{$year}\">{$year}</option>";
                                    }
                                @endphp
                            </select>
                            @error('exam_year') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Semester -->
                        <div class="mb-4">
                            <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">Semester</label>
                            <select wire:model="semester" id="semester" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md" required>
                                <option value="">Select Semester</option>
                                <option value="1">First Semester</option>
                                <option value="2">Second Semester</option>
                            </select>
                            @error('semester') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Exam Type -->
                        <div class="mb-4">
                            <label for="exam_type" class="block text-sm font-medium text-gray-700 mb-2">Exam Type</label>
                            <select wire:model="exam_type" id="exam_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md" required>
                                <option value="">Select Exam Type</option>
                                <option value="final">Final Exam</option>
                                <option value="resit">Resit Exam</option>
                                <option value="mid-semester">Mid-Semester Exam</option>
                                <option value="quiz">Quiz</option>
                                <option value="assignment">Assignment</option>
                            </select>
                            @error('exam_type') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Visibility -->
                        <div class="mb-4">
                            <label for="visibility" class="block text-sm font-medium text-gray-700 mb-2">Visibility</label>
                            <select wire:model="visibility" id="visibility" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md" required>
                                <option value="public">Public (All users can access)</option>
                                <option value="restricted">Restricted (Admins only)</option>
                            </select>
                            @error('visibility') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <!-- Tags -->
                    <div class="mb-4">
                        <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">Tags (Comma separated)</label>
                        <input type="text" wire:model="tags" id="tags" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="exam, calculus, engineering, etc.">
                        @error('tags') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Description/Notes -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description/Notes (Optional)</label>
                        <textarea wire:model="description" id="description" rows="3" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Add any additional information about this exam paper..."></textarea>
                        @error('description') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Upload Progress -->
                    <div class="mb-4" wire:loading wire:target="uploadPaper">
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: 100%"></div>
                        </div>
                        <p class="text-center text-sm text-gray-500 mt-2">Uploading paper, please wait...</p>
                    </div>
                    
                    <!-- Error/Success Messages -->
                    @if (session()->has('success'))
                        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if (session()->has('error'))
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Submit Buttons -->
                    <div class="flex justify-end">
                        <button type="button" wire:click="resetForm" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 mr-2">
                            Reset Form
                        </button>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Upload Paper
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>