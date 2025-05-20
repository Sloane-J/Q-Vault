<x-layouts.app :title="__('Version Management')">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800 dark:text-neutral-200 leading-tight">
            {{ __('Version Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6"> 
            <div class="p-4 sm:p-8 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 shadow-sm">

                @livewire('admin.paper-versions', ['paperId' => $paperId])
            </div>
        </div>
    </div>
</x-layouts.app>