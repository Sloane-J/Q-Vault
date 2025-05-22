<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800 dark:text-neutral-200 leading-tight">
            {{ __('Version Management') }}
        </h2>
    </x-slot>

    <div class="flex justify-between items-center p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm mb-6">
        <div class="flex flex-col justify-center">
            <h3 class="text-lg font-semibold mb-1 text-neutral-800 dark:text-neutral-200">Paper Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                <div>
                    <dt class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Paper Title</dt>
                    <dd class="mt-1 text-sm text-neutral-900 dark:text-neutral-100">
                        @if(isset($paper) && $paper)
                            {{ $paper->title }}
                        @else
                            <span class="text-red-500">Paper not found</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Current Version</dt>
                    <dd class="mt-1 text-sm text-neutral-900 dark:text-neutral-100">
                        @if(isset($paper) && $paper && $paper->currentVersion)
                            v{{ $paper->currentVersion->version_number }}
                        @else
                            No version set
                        @endif
                    </dd>
                </div>
            </div>
        </div>
        <button wire:click="openUploadModal" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Upload New Version
        </button>
    </div>

    <div class="p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm mt-6">
        <h3 class="text-lg font-semibold mb-4 text-neutral-800 dark:text-neutral-200">Version History</h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                <thead class="bg-neutral-50 dark:bg-neutral-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Version</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Uploaded By</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Upload Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-neutral-900 divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse(isset ($versions) ? $versions : [] as $version)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900 dark:text-neutral-100">
                                v{{ $version->version_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                {{ $version->uploader->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                {{ $version->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                @if($paper->current_version_id === $version->id)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                        Current
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-neutral-100 text-neutral-800 dark:bg-neutral-700 dark:text-neutral-100">
                                        Previous
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button wire:click="viewVersion({{ $version->id }})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-600" title="View Details">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <a href="{{ Storage::url($version->file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-600" title="Download">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                    </a>
                                    @if($paper->current_version_id !== $version->id)
                                        <button wire:click="setAsCurrentVersion({{ $version->id }})" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-600" title="Set as Current Version">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                        <button wire:click="deleteVersion({{ $version->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600" title="Delete Version">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400 text-center">
                                No versions found for this paper. Upload a version to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ isset($versions) ? $versions->links() : '' }}
        </div>
    </div>

    {{-- Upload New Version Modal --}}
    <div x-data="{ show: $wire.entangle('showUploadModal') }"
        x-show="show"
        x-on:keydown.escape.window="show = false"
        class="fixed inset-0 overflow-y-auto z-50"
        style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-neutral-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;

            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-neutral-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-neutral-900 dark:text-neutral-100" id="modal-title">
                                Upload New Version
                            </h3>
                            <div class="mt-2">
                                <form wire:submit.prevent="uploadNewVersion">
                                    <div class="mb-4">
                                        <label for="newVersion" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Paper File (PDF)</label>
                                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-neutral-300 dark:border-neutral-600 border-dashed rounded-md">
                                            <div class="space-y-1 text-center">
                                                <svg class="mx-auto h-12 w-12 text-neutral-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <div class="flex text-sm text-neutral-600 dark:text-neutral-300">
                                                    <label for="newVersion" class="relative cursor-pointer rounded-md font-medium text-blue-600 hover:text-blue-500">
                                                        <input id="newVersion" wire:model="newVersion" type="file" class="sr-only">
                                                        <span>Upload a file</span>
                                                    </label>
                                                    <p class="pl-1">or drag and drop</p>
                                                </div>
                                                <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                                    PDF up to 20MB
                                                </p> 
                                                @if(isset($newVersion) && $newVersion) 
                                                    <p class="text-sm text-green-600 mt-2"> 
                                                        File selected: {{ $newVersion->getClientOriginalName() }} 
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        @error('newVersion') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="changeNotes" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Change Notes</label>
                                        <div class="mt-1">
                                            <textarea id="changeNotes" wire:model="changeNotes" rows="3" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-neutral-300 rounded-md dark:bg-neutral-800 dark:text-neutral-100 dark:border-neutral-600" placeholder="Describe what changed in this version..."></textarea>
                                        </div>
                                        @error('changeNotes') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-neutral-50 dark:bg-neutral-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="uploadNewVersion" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Upload
                    </button>
                    <button wire:click="closeUploadModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-neutral-300 shadow-sm px-4 py-2 bg-white dark:bg-neutral-700 text-base font-medium text-neutral-700 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- View Version Details Modal --}}
    <div x-data="{ show: $wire.entangle('showViewVersionModal') }"
         x-show="show"
         x-on:keydown.escape.window="show = false"
         class="fixed inset-0 overflow-y-auto z-50"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-neutral-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;

            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-neutral-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-neutral-900 dark:text-neutral-100" id="modal-title">
                                Version Details
                            </h3>
                           @if(isset($currentVersion) && $currentVersion)
                            <div class="mt-4">
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Version</h4>
                                    <p class="mt-1 text-sm text-neutral-900 dark:text-neutral-100">v{{ $currentVersion->version_number }}</p>
                                </div>
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Uploaded By</h4>
                                    <p class="mt-1 text-sm text-neutral-900 dark:text-neutral-100">{{ $currentVersion->uploader->name }}</p>
                                </div>
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Upload Date</h4>
                                    <p class="mt-1 text-sm text-neutral-900 dark:text-neutral-100">{{ $currentVersion->created_at->format('M d, Y H:i') }}</p>
                                </div>
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Status</h4>
                                    <p class="mt-1 text-sm text-neutral-900 dark:text-neutral-100">
                                        @if(isset($paper) && $paper->current_version_id === $currentVersion->id)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                                Current
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-neutral-100 text-neutral-800 dark:bg-neutral-700 dark:text-neutral-100">
                                                Previous
                                            </span>
                                        @endif
                                    </p>
                                </div>
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Change Notes</h4>
                                    <p class="mt-1 text-sm text-neutral-900 dark:text-neutral-100">
                                        {{ $currentVersion->change_notes ?: 'No change notes provided.' }}
                                    </p>
                                </div>
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Preview</h4>
                                    <div class="mt-2 border border-neutral-300 dark:border-neutral-600 rounded-md overflow-hidden" style="height: 400px;">
                                        <iframe src="{{ Storage::url($currentVersion->file_path) }}" class="w-full h-full"></iframe>
                                    </div>
                                </div>
                            </div>
                           @endif
                        </div>
                    </div>
                </div>
                <div class="bg-neutral-50 dark:bg-neutral-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    @if(isset($currentVersion) && $currentVersion)
                        <button wire:click="setAsCurrentVersion({{ $this->currentVersion->id }})"
                    type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Set as Current
                        </button>
                    @endif
                    <a href="{{ isset($currentVersion) && $currentVersion ? Storage::url($currentVersion->file_path) : '#' }}"
                    target="_blank" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Download
                    </a>
                    <button wire:click="closeViewVersionModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-neutral-300 shadow-sm px-4 py-2 bg-white dark:bg-neutral-700 text-base font-medium text-neutral-700 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>