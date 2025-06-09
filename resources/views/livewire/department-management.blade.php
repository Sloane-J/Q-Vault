<div class="flex flex-col p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-8">
        <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-4 lg:mb-0">
            {{ __('Department Management') }}
        </h2>
        <button
            wire:click="openCreateModal"
            class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 text-base font-medium"
        >
            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            {{ __('Add New Department') }}
        </button>
    </div>

    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 mb-6">
        <div class="relative flex-1 w-full sm:w-auto">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="{{ __('Search departments by name or code...') }}"
                class="pl-10 pr-4 py-2 w-full rounded-md border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-neutral-800 dark:text-neutral-200 focus:border-blue-500 focus:ring-blue-500 focus:ring-1 transition ease-in-out duration-150 text-base placeholder-neutral-400 dark:placeholder-neutral-500"
            />
        </div>
    </div>

    <div class="overflow-x-auto rounded-lg border border-neutral-200 dark:border-neutral-700 shadow-md">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
            <thead class="bg-neutral-50 dark:bg-neutral-800">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                        {{ __('Name') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                        {{ __('Code') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                        {{ __('Status') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                        {{ __('Created') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">
                        {{ __('Actions') }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-neutral-900 divide-y divide-neutral-200 dark:divide-neutral-700">
                @forelse($departments as $department)
                    <tr wire:key="{{ $department->id }}" class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition duration-150 ease-in-out">
                        <td class="px-6 py-4 whitespace-nowrap text-base font-medium text-neutral-900 dark:text-neutral-100">
                            {{ $department->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900 dark:text-neutral-100">
                            {{ $department->code }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $department->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                {{ $department->is_active ? __('Active') : __('Inactive') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                            {{ $department->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end items-center space-x-3">
                                <button
                                    wire:click="openEditModal({{ $department->id }})"
                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-200"
                                    title="{{ __('Edit') }}"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button
                                    wire:click="confirmDelete({{ $department->id }})"
                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors duration-200"
                                    title="{{ __('Delete') }}"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-base text-neutral-500 dark:text-neutral-400">
                            {{ __('No departments found.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $departments->links() }}
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
            <div class="relative w-full max-w-md mx-auto my-6">
                <div class="relative flex flex-col w-full bg-white border-0 rounded-lg shadow-lg dark:bg-neutral-900 outline-none focus:outline-none">
                    <div class="flex items-start justify-between p-5 border-b border-solid rounded-t border-neutral-200 dark:border-neutral-700">
                        <h3 class="text-xl font-semibold text-neutral-800 dark:text-neutral-100">
                            {{ $departmentId ? __('Edit Department') : __('Create Department') }}
                        </h3>
                        <button
                            wire:click="$set('showModal', false)"
                            class="p-1 ml-auto bg-transparent border-0 text-neutral-500 dark:text-neutral-400 opacity-70 hover:opacity-100 transition-opacity"
                            aria-label="Close modal"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="relative flex-auto p-6">
                        <form wire:submit.prevent="saveDepartment">
                            <div class="mb-4">
                                <label for="name" class="block mb-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    {{ __('Department Name') }}
                                </label>
                                <input
                                    type="text"
                                    wire:model="name"
                                    id="name"
                                   class="block w-full rounded-md border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200 focus:border-blue-500 focus:ring-blue-500 focus:ring-1 transition ease-in-out duration-150 py-2 px-3 text-sm placeholder-neutral-400 dark:placeholder-neutral-500"
                                    required
                                >
                                @error('name')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="code" class="block mb-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    {{ __('Department Code') }}
                                </label>
                                <input
                                    type="text"
                                    wire:model="name"
                                    id="name"
                                   class="block w-full rounded-md border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200 focus:border-blue-500 focus:ring-blue-500 focus:ring-1 transition ease-in-out duration-150 py-2 px-3 text-sm placeholder-neutral-400 dark:placeholder-neutral-500"
                                    required
                                >
                                @error('code')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="description" class="block mb-2 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    {{ __('Description') }}
                                </label>
                                <textarea
                                    wire:model="description"
                                    id="description"
                                    rows="3"
                                    class="block w-full rounded-md border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200 focus:border-blue-500 focus:ring-blue-500 focus:ring-1 transition ease-in-out duration-150 py-2 px-3 text-sm placeholder-neutral-400 dark:placeholder-neutral-500"
                                ></textarea>
                            </div>

                            <div class="mb-6">
                                <label class="inline-flex items-center">
                                    <input
                                        type="checkbox"
                                        wire:model="is_active"
                                        class="block w-full rounded-md border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200 focus:border-blue-500 focus:ring-blue-500 focus:ring-1 transition ease-in-out duration-150 py-2 px-3 text-sm placeholder-neutral-400 dark:placeholder-neutral-500"
                                    >
                                    <span class="ml-2 text-sm text-neutral-700 dark:text-neutral-300">
                                        {{ __('Active Department') }}
                                    </span>
                                </label>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <button
                                    type="button"
                                    wire:click="$set('showModal', false)"
                                    class="px-5 py-2 border border-neutral-300 dark:border-neutral-600 text-neutral-700 dark:text-neutral-300 bg-neutral-100 dark:bg-neutral-700 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-neutral-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                >
                                    {{ __('Cancel') }}
                                </button>
                                <button
                                    type="submit"
                                    class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                >
                                    {{ $departmentId ? __('Update') : __('Create') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="fixed inset-0 z-40 bg-black opacity-40"></div>
    @endif

    @if($confirmingDeletion)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
            <div class="relative w-full max-w-md mx-auto my-6">
                <div class="relative flex flex-col w-full bg-white border-0 rounded-lg shadow-lg dark:bg-neutral-900 outline-none focus:outline-none">
                    <div class="flex items-start justify-between p-5 border-b border-solid rounded-t border-neutral-200 dark:border-neutral-700">
                        <h3 class="text-xl font-semibold text-neutral-800 dark:text-neutral-100">
                            {{ __('Confirm Deletion') }}
                        </h3>
                        <button
                            wire:click="$set('confirmingDeletion', false)"
                            class="p-1 ml-auto bg-transparent border-0 text-neutral-500 dark:text-neutral-400 opacity-70 hover:opacity-100 transition-opacity"
                            aria-label="Close modal"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="relative flex-auto p-6">
                        <p class="text-neutral-600 dark:text-neutral-300 mb-6">
                            {{ __('Are you sure you want to delete this department? This action cannot be undone.') }}
                        </p>

                        <div class="flex justify-end space-x-3">
                            <button
                                wire:click="$set('confirmingDeletion', false)"
                                class="px-5 py-2 border border-neutral-300 dark:border-neutral-600 text-neutral-700 dark:text-neutral-300 bg-neutral-100 dark:bg-neutral-700 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-neutral-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                {{ __('Cancel') }}
                            </button>
                            <button
                                wire:click="destroyDepartment"
                                class="px-5 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                {{ __('Delete') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="fixed inset-0 z-40 bg-black opacity-40"></div>
    @endif
</div>