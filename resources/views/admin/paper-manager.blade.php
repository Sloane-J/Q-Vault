<x-layouts.app :title="__('Paper Management')">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Paper Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Include the Livewire Component -->
            @livewire('admin.paper-manager')
        </div>
    </div>
</x-app.layout>