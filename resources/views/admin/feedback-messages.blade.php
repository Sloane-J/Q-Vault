<x-layouts.app :title="__('Feedback Messages')">

    {{-- Gate check for the entire layout content --}}
    @can('access-feedback-dashboard')

        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Feedback Messages') }}
            </h2>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    @livewire('feedback-messages')
                </div>
            </div>
        </div>

    @else
        {{-- Fallback content if the user is not authorized --}}
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">
                        {{ __("You don't have permission to view this page.") }}
                    </h3>
                    <p class="text-neutral-600 dark:text-neutral-400">
                        Please contact an administrator if you believe this is an error.
                    </p>
                </div>
            </div>
        </div>
    @endcan

</x-layouts.app>