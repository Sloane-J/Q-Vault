<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse">
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('Q Vault')" class="grid">
                    <flux:navlist.item
                        icon="home"
                        :href="route('dashboard')"
                        :current="request()->routeIs('dashboard')"
                        
                    >
                        {{ __('Dashboard') }}
                    </flux:navlist.item>

                    <flux:navlist.item
                        icon="academic-cap"
                        :href="route('admin.departments')"
                        :current="request()->routeIs('admin.department.*')"
                        wire:navigate
                    >
                        {{ __('Departments') }}
                    </flux:navlist.item>

                    {{-- Paper Management Drop Down --}}
                    <flux:navlist.group expandable heading="Paper Management" icon="newspaper">
                        <flux:navlist.item 
                            :href="route('admin.papers.index')" 
                            icon="clipboard-document-list"
                            :current="request()->routeIs('admin.papers.index')"
                            wire:navigate
                        >
                            {{ __('Paper Manager') }}
                        </flux:navlist.item>

                        <flux:navlist.item 
                            :href="route('admin.papers.index')" 
                            icon="folder-open"
                            :current="request()->routeIs('admin.papers.paper.versions')"
                            wire:navigate
                        >
                            {{ __('Version Management') }}
                        </flux:navlist.item>
                    </flux:navlist.group>

                    <flux:navlist.item
                        icon="book-open"
                        :href="route('admin.courses')"
                        :current="request()->routeIs('admin.courses.*')"
                        wire:navigate
                    >
                        {{ __('Course Management') }}
                    </flux:navlist.item>

                </flux:navlist.group>

                    {{-- Analytics Drop Down --}}
                    <flux:navlist.group expandable heading="Analytics & Auditing" icon="newspaper">
                        {{-- For: dashboard.blade.php --}}
                        <flux:navlist.item 
                            :href="route('admin.analytics')" 
                            icon="chart-pie"
                            :current="request()->routeIs('admin.analytics.dashboard')"
                            
                        >
                            {{ __('Analytics Overview') }}
                        </flux:navlist.item>


                        {{-- For: storage-analytics.blade.php, export-data.blade.php 
                        <flux:navlist.item 
                            :href="route('admin.papers.index')" 
                            icon="wrench-screwdriver"
                            :current="request()->routeIs('admin.papers.index')"
                            wire:navigate
                        >
                            {{ __('Systems Analytics') }}
                    </flux:navlist.item>
--}}
                    </flux:navlist.group>

                    @auth
    @if (auth()->user()->isAdmin() && (auth()->user()->name === 'Super Admin' || auth()->user()->name === 'Sloane Jnr'))
        <flux:navlist.item
            icon="chat-bubble-left-right"
            :href="route('admin.feedback-messages')"
            :current="request()->routeIs('admin.feedback-messages')"
        >
            {{ __('Feedback Messages') }}
        </flux:navlist.item>
    @endif
@endauth
            </flux:navlist>

            <flux:spacer />

            <flux:navlist variant="outline">
                <flux:navlist.item icon="folder-git-2" href="https://github.com/Sloane-J/Q-Vault" target="_blank">
                    {{ __('Repository') }}
                </flux:navlist.item>

                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                    {{ __('Documentation') }}
                </flux:navlist.item>
            </flux:navlist>

            {{-- Desktop User Menu --}}
            <flux:dropdown position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        {{-- Mobile User Menu --}}
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-3" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>