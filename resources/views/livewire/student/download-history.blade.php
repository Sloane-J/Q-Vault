<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-neutral-900 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-6">Your Download History</h2>

                @if($downloads->count() > 0)
                    <div class="grid gap-4 md:grid-cols-1 lg:grid-cols-2">
                        @foreach($downloads as $download)
                            <div class="flex justify-between items-center p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex flex-col justify-center flex-1">
                                    <h3 class="text-lg font-semibold mb-1 text-blue-700 dark:text-blue-400">
                                        {{ $download->paper->title ?? 'N/A' }}
                                    </h3>
                                    <div class="space-y-1">
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            <span class="text-gray-500 dark:text-gray-400">Department:</span> 
                                            {{ $download->paper->department->name ?? 'N/A' }}
                                        </p>
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            <span class="text-gray-500 dark:text-gray-400">Course:</span> 
                                            {{ $download->paper->course->name ?? 'N/A' }}
                                        </p>
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            <span class="text-gray-500 dark:text-gray-400">Level:</span> 
                                            {{ $download->paper->level->name ?? 'N/A' }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                            Downloaded {{ $download->downloaded_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="flex flex-col gap-2 ml-4">
                                    <button class="inline-flex items-center px-3 py-2 text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Preview
                                    </button>
                                    
                                    <button class="inline-flex items-center px-3 py-2 text-xs font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                        </svg>
                                        Share
                                    </button>
                                    
                                    <button class="inline-flex items-center px-3 py-2 text-xs font-medium text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                        </svg>
                                        Save
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Livewire Pagination Links --}}
                    <div class="mt-8">
                        {{ $downloads->links() }}
                    </div>
                @else
                    <div class="text-center py-10">
                        <div class="mx-auto h-24 w-24 rounded-full bg-gray-100 dark:bg-neutral-800 flex items-center justify-center mb-4">
                            <svg class="h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">No downloads yet</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Start by browsing and downloading exam papers to build your collection.</p>
                        
                        <div class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Browse Papers
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>