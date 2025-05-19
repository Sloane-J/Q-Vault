<div>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-neutral-900 overflow-hidden shadow-sm sm:rounded-lg border border-neutral-200 dark:border-neutral-700">
            <div class="p-6 bg-white dark:bg-neutral-900 border-b border-neutral-200 dark:border-neutral-700">
                <h1 class="text-3xl font-bold text-neutral-800 dark:text-neutral-200 mb-8">
                    {{ __('Upload Exam Paper') }}
                </h1>

                <form wire:submit.prevent="uploadPaper" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-6">
                        <label for="paper_file" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                            {{ __('Paper File (PDF)') }}
                        </label>
                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col rounded-lg border-4 border-dashed w-full h-60 p-10 group text-center
                                          border-neutral-300 dark:border-neutral-600
                                          hover:border-blue-500 hover:bg-neutral-50 dark:hover:border-blue-400 dark:hover:bg-neutral-800
                                          transition-colors duration-200 cursor-pointer">
                                <div class="h-full w-full text-center flex flex-col items-center justify-center">
                                    @if ($paper)
                                        <p class="text-green-600 dark:text-green-400 font-medium">{{ $paper->getClientOriginalName() }} - {{ __('Ready to upload!') }}</p>
                                    @else
                                        <svg class="w-12 h-12 text-neutral-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <p class="pointer-none text-neutral-500 dark:text-neutral-400 pt-3 text-base">
                                            {{ __('Drag and drop or click to select a PDF file') }}
                                        </p>
                                    @endif
                                </div>
                                <input type="file" wire:model="paper" id="paper_file" class="hidden" accept=".pdf" required />
                            </label>
                        </div>
                        @error('paper') <span class="text-red-600 dark:text-red-400 text-sm mt-2 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="department_id" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                                {{ __('Department') }}
                            </label>
                            <select wire:model="department_id" id="department_id" class="block w-full pl-3 pr-10 py-2 text-base rounded-lg
                                   border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200
                                   focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                <option value="">{{ __('Select Department') }}</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id') <span class="text-red-600 dark:text-red-400 text-sm mt-2 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="course_id" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                                {{ __('Course') }}
                            </label>
                            <select wire:model="course_id" id="course_id" class="block w-full pl-3 pr-10 py-2 text-base rounded-lg
                                   border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200
                                   focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                <option value="">{{ __('Select Course') }}</option>
                                @if($department_id)
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->name }} ({{ $course->code }})</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('course_id') <span class="text-red-600 dark:text-red-400 text-sm mt-2 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="student_type" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                                {{ __('Student Type') }}
                            </label>
                            <select wire:model="student_type" id="student_type" class="block w-full pl-3 pr-10 py-2 text-base rounded-lg
                                   border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200
                                   focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                <option value="">{{ __('Select Student Type') }}</option>
                                <option value="HND">HND</option>
                                <option value="B-Tech">B-Tech</option>
                                <option value="Top-up">Top-up</option>
                            </select>
                            @error('student_type') <span class="text-red-600 dark:text-red-400 text-sm mt-2 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="level" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                                {{ __('Level') }}
                            </label>
                            <select wire:model="level" id="level" class="block w-full pl-3 pr-10 py-2 text-base rounded-lg
                                   border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200
                                   focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                <option value="">{{ __('Select Level') }}</option>
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
                            @error('level') <span class="text-red-600 dark:text-red-400 text-sm mt-2 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="exam_year" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                                {{ __('Exam Year') }}
                            </label>
                            <select wire:model="exam_year" id="exam_year" class="block w-full pl-3 pr-10 py-2 text-base rounded-lg
                                   border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200
                                   focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                <option value="">{{ __('Select Year') }}</option>
                                @php
                                    $currentYear = date('Y');
                                    for ($year = $currentYear; $year >= $currentYear - 10; $year--) {
                                        echo "<option value=\"{$year}\">{$year}</option>";
                                    }
                                @endphp
                            </select>
                            @error('exam_year') <span class="text-red-600 dark:text-red-400 text-sm mt-2 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="semester" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                                {{ __('Semester') }}
                            </label>
                            <select wire:model="semester" id="semester" class="block w-full pl-3 pr-10 py-2 text-base rounded-lg
                                   border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200
                                   focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                <option value="">{{ __('Select Semester') }}</option>
                                <option value="1">First Semester</option>
                                <option value="2">Second Semester</option>
                            </select>
                            @error('semester') <span class="text-red-600 dark:text-red-400 text-sm mt-2 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="exam_type" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                                {{ __('Exam Type') }}
                            </label>
                            <select wire:model="exam_type" id="exam_type" class="block w-full pl-3 pr-10 py-2 text-base rounded-lg
                                   border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200
                                   focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                <option value="">{{ __('Select Exam Type') }}</option>
                                <option value="final">Final Exam</option>
                                <option value="resit">Resit Exam</option>
                                <option value="mid-semester">Mid-Semester Exam</option>
                                <option value="quiz">Quiz</option>
                                <option value="assignment">Assignment</option>
                            </select>
                            @error('exam_type') <span class="text-red-600 dark:text-red-400 text-sm mt-2 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="visibility" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                                {{ __('Visibility') }}
                            </label>
                            <select wire:model="visibility" id="visibility" class="block w-full pl-3 pr-10 py-2 text-base rounded-lg
                                   border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200
                                   focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                <option value="public">{{ __('Public (All users can access)') }}</option>
                                <option value="restricted">{{ __('Restricted (Admins only)') }}</option>
                            </select>
                            @error('visibility') <span class="text-red-600 dark:text-red-400 text-sm mt-2 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-6 mb-4">
                        <label for="tags" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                            {{ __('Tags (Comma separated)') }}
                        </label>
                        <input type="text" wire:model="tags" id="tags" class="block w-full rounded-lg
                               border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200
                               focus:ring-blue-500 focus:border-blue-500 shadow-sm sm:text-sm" placeholder="{{ __('exam, calculus, engineering, etc.') }}">
                        @error('tags') <span class="text-red-600 dark:text-red-400 text-sm mt-2 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">
                            {{ __('Description/Notes (Optional)') }}
                        </label>
                        <textarea wire:model="description" id="description" rows="3" class="shadow-sm block w-full rounded-lg
                                  border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200
                                  focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="{{ __('Add any additional information about this exam paper...') }}"></textarea>
                        @error('description') <span class="text-red-600 dark:text-red-400 text-sm mt-2 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-6" wire:loading wire:target="uploadPaper">
                        <div class="w-full bg-neutral-200 dark:bg-neutral-700 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: 100%"></div>
                        </div>
                        <p class="text-center text-sm text-neutral-500 dark:text-neutral-400 mt-2">
                            {{ __('Uploading paper, please wait...') }}
                        </p>
                    </div>

                    @if (session()->has('success'))
                        <div class="bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 dark:border-green-700 p-4 mb-6 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-700 dark:text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-800 dark:text-green-300">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 dark:border-red-700 p-4 mb-6 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-700 dark:text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-800 dark:text-red-300">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end space-x-3 mt-8">
                        <button type="button" wire:click="resetForm" class="px-5 py-2 border border-neutral-300 dark:border-neutral-600 text-neutral-700 dark:text-neutral-300 bg-neutral-100 dark:bg-neutral-700 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-neutral-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Reset Form') }}
                        </button>
                        <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Upload Paper') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>