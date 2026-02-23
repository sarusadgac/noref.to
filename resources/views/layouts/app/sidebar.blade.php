<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50 dark:bg-zinc-950">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('home') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group class="grid">
                    <flux:sidebar.item icon="chart-bar-square" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="shield-check" :href="route('admin.links')" :current="request()->routeIs('admin.links')" wire:navigate>
                        {{ __('Links') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="user-group" :href="route('admin.users')" :current="request()->routeIs('admin.users')" wire:navigate>
                        {{ __('Users') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="flag" :href="route('admin.reports')" :current="request()->routeIs('admin.reports')" wire:navigate>
                        {{ __('Reports') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="globe-alt" :href="route('admin.domains')" :current="request()->routeIs('admin.domains')" wire:navigate>
                        {{ __('Domains') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group heading="Monitor" class="grid">
                    <flux:sidebar.item icon="chart-bar" :href="route('admin.pulse')" :current="request()->routeIs('admin.pulse')" wire:navigate>
                        {{ __('Pulse') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>


        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

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
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('home')" icon="arrow-top-right-on-square" wire:navigate>
                            {{ __('View Site') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <div class="px-2 py-1.5" x-data>
                        <flux:radio.group variant="segmented" x-model="$flux.appearance" size="sm">
                            <flux:radio value="light" icon="sun" icon-trailing="" />
                            <flux:radio value="dark" icon="moon" icon-trailing="" />
                            <flux:radio value="system" icon="computer-desktop" icon-trailing="" />
                        </flux:radio.group>
                    </div>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
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
