<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50 dark:bg-zinc-950 bg-noise antialiased">
        <div class="bg-mesh-gradient"></div>

        <div class="[grid-area:header]">
            <flux:header container class="relative z-10 border-b border-zinc-200 dark:border-zinc-700/30 bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md">
                <a href="{{ route('home') }}" class="flex items-center gap-2" wire:navigate>
                    <x-app-logo-icon class="size-5" />
                    <span class="font-heading text-lg font-bold tracking-tight text-zinc-900 dark:text-zinc-100">{{ config('app.name') }}</span>
                </a>

                <flux:navbar class="-mb-px max-lg:hidden ml-6">
                    <flux:navbar.item :href="route('report')" :current="request()->routeIs('report')" wire:navigate>
                        {{ __('Report') }}
                    </flux:navbar.item>
                    <flux:navbar.item :href="route('integrate')" :current="request()->routeIs('integrate')" wire:navigate>
                        {{ __('Integrate') }}
                    </flux:navbar.item>

                    @auth
                        <flux:navbar.item :href="route('my-links')" :current="request()->routeIs('my-links')" wire:navigate>
                            {{ __('My Links') }}
                        </flux:navbar.item>

                    @endauth
                </flux:navbar>

                <flux:spacer />

                @guest
                    <flux:navbar class="space-x-0.5 rtl:space-x-reverse py-0!">
                        <flux:navbar.item :href="route('login')" wire:navigate>
                            {{ __('Login') }}
                        </flux:navbar.item>
                        <flux:navbar.item :href="route('register')" wire:navigate>
                            {{ __('Register') }}
                        </flux:navbar.item>
                    </flux:navbar>
                @endguest

                @auth
                    <flux:dropdown position="bottom" align="end">
                        <flux:profile
                            :initials="auth()->user()->initials()"
                            icon-trailing="chevron-down"
                        />

                        <flux:menu>
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

                            <flux:menu.separator />

                            <flux:menu.radio.group>
                                <flux:menu.item :href="route('my-links')" icon="link" wire:navigate>
                                    {{ __('My Links') }}
                                </flux:menu.item>
                                <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                                    {{ __('Settings') }}
                                </flux:menu.item>
                            </flux:menu.radio.group>

                            @admin
                                <flux:menu.separator />
                                <flux:menu.radio.group>
                                    <flux:menu.item :href="route('admin.dashboard')" icon="shield-check" wire:navigate>
                                        {{ __('Admin') }}
                                    </flux:menu.item>
                                </flux:menu.radio.group>
                            @endadmin

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
                                >
                                    {{ __('Log Out') }}
                                </flux:menu.item>
                            </form>
                        </flux:menu>
                    </flux:dropdown>
                @endauth
            </flux:header>

            <!-- Mobile Nav -->
            <div class="relative z-10 lg:hidden border-b border-zinc-200 dark:border-zinc-700/30 bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md px-4 py-2 flex items-center gap-4 overflow-x-auto">
                <a href="{{ route('report') }}" class="text-sm text-zinc-500 dark:text-zinc-400 whitespace-nowrap transition-colors duration-150 hover:text-emerald-600 dark:hover:text-emerald-400 {{ request()->routeIs('report') ? 'font-semibold !text-emerald-600 dark:!text-emerald-400' : '' }}" wire:navigate>
                    {{ __('Report') }}
                </a>
                <a href="{{ route('integrate') }}" class="text-sm text-zinc-500 dark:text-zinc-400 whitespace-nowrap transition-colors duration-150 hover:text-emerald-600 dark:hover:text-emerald-400 {{ request()->routeIs('integrate') ? 'font-semibold !text-emerald-600 dark:!text-emerald-400' : '' }}" wire:navigate>
                    {{ __('Integrate') }}
                </a>
                @auth
                    <a href="{{ route('my-links') }}" class="text-sm text-zinc-500 dark:text-zinc-400 whitespace-nowrap transition-colors duration-150 hover:text-emerald-600 dark:hover:text-emerald-400 {{ request()->routeIs('my-links') ? 'font-semibold !text-emerald-600 dark:!text-emerald-400' : '' }}" wire:navigate>
                        {{ __('My Links') }}
                    </a>
                @endauth
            </div>
        </div>

        <flux:main container class="relative z-10">
            {{ $slot }}
        </flux:main>

        <footer class="relative z-10 border-t border-zinc-200 dark:border-zinc-700/30 py-6 [grid-area:footer]">
            <div class="mx-auto max-w-7xl px-6 flex flex-col items-center gap-3">
                <nav class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1">
                    <a href="{{ route('about') }}" class="font-mono-accent text-xs text-zinc-400 dark:text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-150" wire:navigate>{{ __('About') }}</a>
                    <span class="text-zinc-300 dark:text-zinc-700">&middot;</span>
                    <a href="{{ route('privacy') }}" class="font-mono-accent text-xs text-zinc-400 dark:text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-150" wire:navigate>{{ __('Privacy') }}</a>
                    <span class="text-zinc-300 dark:text-zinc-700">&middot;</span>
                    <a href="{{ route('terms') }}" class="font-mono-accent text-xs text-zinc-400 dark:text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-150" wire:navigate>{{ __('Terms') }}</a>
                    <span class="text-zinc-300 dark:text-zinc-700">&middot;</span>
                    <a href="{{ route('report') }}" class="font-mono-accent text-xs text-zinc-400 dark:text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-150" wire:navigate>{{ __('Report') }}</a>
                    <span class="text-zinc-300 dark:text-zinc-700">&middot;</span>
                    <a href="{{ route('integrate') }}" class="font-mono-accent text-xs text-zinc-400 dark:text-zinc-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-150" wire:navigate>{{ __('Integrate') }}</a>
                </nav>
                <p class="font-mono-accent text-xs text-zinc-400 dark:text-zinc-600">{{ __('Privacy-first link shortening') }} &middot; {{ config('app.name') }}</p>
            </div>
        </footer>

        @fluxScripts
        @stack('scripts')
    </body>
</html>
