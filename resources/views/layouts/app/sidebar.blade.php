<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>

                    @if (auth()->user()?->role?->view_users)
                        <flux:sidebar.item icon="layout-grid" :href="route('users.index')" :current="request()->routeIs('users.*')" wire:navigate>
                            {{ __('Users') }}
                        </flux:sidebar.item>
                    @endif

                    @if (auth()->user()?->role?->manage_permission)
                        <flux:sidebar.item icon="folder-git-2" :href="route('roles.index')" :current="request()->routeIs('roles.*')" wire:navigate>
                            {{ __('Roles') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="book-open-text" :href="route('permissions.index')" :current="request()->routeIs('permissions.*')" wire:navigate>
                            {{ __('Permissions') }}
                        </flux:sidebar.item>
                    @endif

                    @if (auth()->user()?->role?->view_paymodes)
                        <flux:sidebar.item icon="folder-git-2" :href="route('modes.index')" :current="request()->routeIs('modes.*')" wire:navigate>
                            {{ __('Payment Modes') }}
                        </flux:sidebar.item>
                    @endif

                    @if (auth()->user()?->role?->view_logs)
                        <flux:sidebar.item icon="book-open-text" :href="route('audits.index')" :current="request()->routeIs('audits.*')" wire:navigate>
                            {{ __('Audit Logs') }}
                        </flux:sidebar.item>
                    @endif
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Operations')" class="grid">
                    @if (auth()->user()?->role?->view_clients)
                        <flux:sidebar.item icon="layout-grid" :href="route('clients.index')" :current="request()->routeIs('clients.*')" wire:navigate>
                            {{ __('Clients') }}
                        </flux:sidebar.item>
                    @endif

                    @if (auth()->user()?->role?->view_guards)
                        <flux:sidebar.item icon="folder-git-2" :href="route('guards.index')" :current="request()->routeIs('guards.*')" wire:navigate>
                            {{ __('Guards') }}
                        </flux:sidebar.item>
                    @endif

                    @if (auth()->user()?->role?->assign_guards)
                        <flux:sidebar.item icon="book-open-text" :href="route('assignments.index')" :current="request()->routeIs('assignments.*')" wire:navigate>
                            {{ __('Assignments') }}
                        </flux:sidebar.item>
                    @endif

                    @if (auth()->user()?->role?->manage_attendance)
                        <flux:sidebar.item icon="layout-grid" :href="route('attendance.index')" :current="request()->routeIs('attendance.*')" wire:navigate>
                            {{ __('Attendance') }}
                        </flux:sidebar.item>
                    @endif
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

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

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
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
