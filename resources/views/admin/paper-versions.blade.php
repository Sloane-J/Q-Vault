<x-app.layout :title="__('Version Management')">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Version Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:paper-versions :paper-id="$paperId" />
        </div>
    </div>
</x-app.layout>