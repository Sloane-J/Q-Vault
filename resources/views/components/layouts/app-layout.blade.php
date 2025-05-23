@php
    $isAdmin = auth()->user() && auth()->user()->isAdmin();
@endphp

@if($isAdmin)
    <x-layouts.app.sidebar :title="$title ?? null">
        <flux:main>

        </flux:main>
    </x-layouts.app.sidebar>
@else
    <x-layouts.app.header :title="$title ?? null">
        <flux:main>

        </flux:main>
    </x-layouts.app.header>
@endif