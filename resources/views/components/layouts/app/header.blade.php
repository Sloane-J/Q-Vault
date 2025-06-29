<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    @voletStyles
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <a href="{{ route('student.dashboard') }}"
            class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0" >
            <x-app-logo />
        </a>

       <flux:navbar class="-mb-px max-lg:hidden">
    <flux:navbar.item icon="cube-transparent" :href="route('student.dashboard')"
        :current="request()->routeIs('student.dashboard')">
        {{ __('Dashboard') }}
    </flux:navbar.item>

    <flux:navbar.item icon="inbox-arrow-down" :href="route('student.paper-browser')"
        :current="request()->routeIs('student.paper-browser')" wire:navigate>
        {{ __('Browse Papers') }}
    </flux:navbar.item>

    <flux:navbar.item icon="clock" :href="route('student.download.history')"
        :current="request()->routeIs('student.download.history')" wire:navigate>
        {{ __('Download History') }}
    </flux:navbar.item>
</flux:navbar>

        <flux:spacer />

        <!-- Desktop User Menu -->
        <flux:dropdown position="top" align="end">
            <flux:profile class="cursor-pointer" :initials="auth()->user()->initials()" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
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
                    <flux:menu.item :href="route('settings.profile')" icon="user-circle" wire:navigate>{{ __('Profile') }}
                    </flux:menu.item>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}
                    </flux:menu.item>
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

    <!-- Mobile Menu -->
    <flux:sidebar stashable sticky
        class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('student.dashboard') }}" class="ms-1 flex items-center space-x-2 rtl:space-x-reverse"
            >
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Student Portal')">
                <flux:navlist.item icon="layout-grid" :href="route('student.dashboard')"
                    :current="request()->routeIs('student.dashboard')">
                    {{ __('Dashboard') }}
                </flux:navlist.item>

               <flux:navlist.item icon="inbox-arrow-down" :href="route('student.paper-browser')"
    :current="request()->routeIs('student.paper-browser')" wire:navigate>
    {{ __('Browse Papers') }}
</flux:navlist.item>

                <flux:navlist.item icon="clock" :href="route('student.download.history')"
                    :current="request()->routeIs('student.download.history')" wire:navigate>
                    {{ __('Download History') }}
                </flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />

        <flux:navlist variant="outline">
            <flux:navlist.item icon="cog" :href="route('settings.profile')" wire:navigate>
                {{ __('Settings') }}
            </flux:navlist.item>
        </flux:navlist>
    </flux:sidebar>

    {{ $slot }}

    @fluxScripts
    @volet
</body>

</html>