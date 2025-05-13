<div>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">
                        Manage Papers
                    </h2>
                    <a href="{{ route('admin.papers.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add New Paper
                    </a>
                </div>
                
                                <!-- Search and Filter Bar -->
                                <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                                        <div class="col-span-1 md:col-span-2">
                                            <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                                            <div class="mt-1 flex rounded-md shadow-sm">
                                                <input wire:model.debounce.300ms="search" type="text" name="search" id="search" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" placeholder="Search by title, course or description...">
                                            </div>
                                        </div>
                                        <div>
                                            <label for="department" class="block text-sm font-medium text-gray-700">Department</label>
                                            <select wire:model="departmentFilter" id="department" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                <option value="">All Departments</option>
                                                @foreach($departments as $department)
                                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                                            <select wire:model="yearFilter" id="year" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                <option value="">All Years</option>
                                                @foreach($years as $year)
                                                    <option value="{{ $year }}">{{ $year }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 gap-4 mt-4 md:grid-cols-3">
                                        <div>
                                            <label for="student_type" class="block text-sm font-medium text-gray-700">Student Type</label>
                                            <select wire:model="studentTypeFilter" id="student_type" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                <option value="">All Types</option>
                                                @foreach($studentTypes as $type)
                                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label for="level" class="block text-sm font-medium text-gray-700">Level</label>
                                            <select wire:model="levelFilter" id="level" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                <option value="">All Levels</option>
                                                @foreach($levels as $level)
                                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label for="visibility" class="block text-sm font-medium text-gray-700">Visibility</label>
                                            <select wire:model="visibilityFilter" id="visibility" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                <option value="">All</option>
                                                <option value="1">Visible</option>
                                                <option value="0">Hidden</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                
                                <!-- Papers Table -->
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    <div class="flex items-center">
                                                        Title
                                                        <button wire:click="sortBy('title')" class="ml-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                @if($sortField === 'title' && $sortDirection === 'asc')
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                                @else
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                                @endif
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Department
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Course / Year
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    <div class="flex items-center">
                                                        Uploaded
                                                        <button wire:click="sortBy('created_at')" class="ml-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                @if($sortField === 'created_at' && $sortDirection === 'asc')
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                                @else
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                                @endif
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Status
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Downloads
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse ($papers as $paper)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $paper->title }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ Str::limit($paper->description, 50) }}
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm text-gray-900">{{ $paper->department->name }}</div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ $paper->studentType->name }} / Level {{ $paper->level->level_number }}
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm text-gray-900">{{ $paper->course_name }}</div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ $paper->exam_year }} / {{ ucfirst($paper->semester) }} / {{ ucfirst($paper->exam_type) }}
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        <div>{{ $paper->created_at->format('d M, Y') }}</div>
                                                        <div class="text-xs">by {{ $paper->user->name }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @if($paper->visibility)
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                                Visible
                                                            </span>
                                                        @else
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                                Hidden
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $paper->downloads_count }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                        <div class="flex items-center justify-center space-x-2">
                                                            <!-- Preview Button -->
                                                            <button wire:click="previewPaper({{ $paper->id }})" class="text-blue-600 hover:text-blue-900" title="Preview">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                </svg>
                                                            </button>
                                                            
                                                            <!-- Edit Button -->
                                                            <a href="{{ route('admin.papers.edit', $paper->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                </svg>
                                                            </a>
                                                            
                                                            <!-- Versions Button -->
                                                            <button wire:click="managePaperVersions({{ $paper->id }})" class="text-purple-600 hover:text-purple-900" title="Versions">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                                </svg>
                                                            </button>
                                                            
                                                            <!-- Toggle Visibility Button -->
                                                            <button wire:click="toggleVisibility({{ $paper->id }})" class="{{ $paper->visibility ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }}" title="{{ $paper->visibility ? 'Hide' : 'Show' }}">
                                                                @if($paper->visibility)
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                                    </svg>
                                                                @else
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                    </svg>
                                                                @endif
                                                            </button>
                                                            
                                                            <!-- Delete Button -->
                                                            <button wire:click="confirmPaperDeletion({{ $paper->id }})" class="text-red-600 hover:text-red-900" title="Delete">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                                        No papers found. 
                                                        <a href="{{ route('admin.papers.create') }}" class="text-indigo-600 hover:text-indigo-900">Upload a new paper</a>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Pagination -->
                                <div class="mt-4">
                                    {{ $papers->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Paper Preview Modal -->
                    <x-dialog-modal wire:model="showingPaperPreview">
                        <x-slot name="title">
                            {{ $previewPaper ? $previewPaper->title : 'Paper Preview' }}
                        </x-slot>
                
                        <x-slot name="content">
                            @if ($previewPaper)
                                <div class="h-96">
                                    <iframe src="{{ route('papers.preview', $previewPaper->id) }}" class="w-full h-full border-0"></iframe>
                                </div>
                                <div class="mt-4 text-sm text-gray-600">
                                    <p><strong>Course:</strong> {{ $previewPaper->course_name }}</p>
                                    <p><strong>Year:</strong> {{ $previewPaper->exam_year }}</p>
                                    <p><strong>Department:</strong> {{ $previewPaper->department->name }}</p>
                                    <p><strong>Type:</strong> {{ ucfirst($previewPaper->exam_type) }} ({{ ucfirst($previewPaper->semester) }} Semester)</p>
                                    <p><strong>Level:</strong> {{ $previewPaper->level->name }}</p>
                                    <p><strong>Uploaded by:</strong> {{ $previewPaper->user->name }} on {{ $previewPaper->created_at->format('d M, Y') }}</p>
                                    <p><strong>Total Downloads:</strong> {{ $previewPaper->downloads_count }}</p>
                                </div>
                            @else
                                <div class="text-center py-10">
                                    <p>Paper not found or cannot be previewed.</p>
                                </div>
                            @endif
                        </x-slot>
                
                        <x-slot name="footer">
                            <x-secondary-button wire:click="$set('showingPaperPreview', false)" wire:loading.attr="disabled">
                                Close
                            </x-secondary-button>
                        </x-slot>
                    </x-dialog-modal>
                    
                    <!-- Paper Versions Modal -->
                    <x-dialog-modal wire:model="showingPaperVersions">
                        <x-slot name="title">
                            @if ($currentPaper)
                                {{ $currentPaper->title }} - Versions
                            @else
                                Paper Versions
                            @endif
                        </x-slot>
                
                        <x-slot name="content">
                            @if ($currentPaper)
                                <div class="mb-4">
                                    <form wire:submit.prevent="addPaperVersion">
                                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                            <div>
                                                <x-label for="version_number" value="Version Number" />
                                                <x-input id="version_number" type="number" class="mt-1 block w-full" wire:model.defer="newVersion.version_number" />
                                                <x-input-error for="newVersion.version_number" class="mt-2" />
                                            </div>
                                            <div>
                                                <x-label for="version_file" value="Upload File" />
                                                <input id="version_file" type="file" class="mt-1 block w-full" wire:model="newVersion.file" />
                                                <x-input-error for="newVersion.file" class="mt-2" />
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <x-label for="version_notes" value="Notes" />
                                            <textarea id="version_notes" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" wire:model.defer="newVersion.notes"></textarea>
                                            <x-input-error for="newVersion.notes" class="mt-2" />
                                        </div>
                                        <div class="mt-4 flex justify-end">
                                            <x-button type="submit">
                                                Add Version
                                            </x-button>
                                        </div>
                                    </form>
                                </div>
                                
                                <div class="mt-6">
                                    <h3 class="font-semibold text-gray-800 mb-3">Existing Versions</h3>
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Version</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <!-- Original Version -->
                                            <tr class="bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    Original
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $currentPaper->created_at->format('d M, Y') }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500">
                                                    Initial upload
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                    <button wire:click="previewPaperVersion({{ $currentPaper->id }}, 'original')" class="text-blue-600 hover:text-blue-900" title="Preview">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </button>
                                                    <a href="{{ route('papers.download', $currentPaper->id) }}" class="text-green-600 hover:text-green-900 ml-2" title="Download">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                        </svg>
                                                    </a>
                                                </td>
                                            </tr>
                                            
                                            <!-- Additional Versions -->
                                            @forelse ($paperVersions as $version)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        Version {{ $version->version_number }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $version->created_at->format('d M, Y') }}
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-500">
                                                        {{ $version->notes }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                        <button wire:click="previewPaperVersion({{ $currentPaper->id }}, {{ $version->id }})" class="text-blue-600 hover:text-blue-900" title="Preview">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                        </button>
                                                        <a href="{{ route('papers.version.download', $version->id) }}" class="text-green-600 hover:text-green-900 ml-2" title="Download">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                            </svg>
                                                        </a>
                                                        <button wire:click="confirmVersionDeletion({{ $version->id }})" class="text-red-600 hover:text-red-900 ml-2" title="Delete">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>