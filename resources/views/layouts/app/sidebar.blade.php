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

            @php
                $role = auth()->user()?->role;
                $attendanceOpen = request()->routeIs('attendance.*');
                $clientsOpen = request()->routeIs('clients.*', 'client-documents.*', 'client-categories.*', 'assignments.*');
                $guardsOpen = request()->routeIs('guards.*');
                $armoryOpen = request()->routeIs('armory.*');
                $expensesOpen = request()->routeIs('expenses.*');
                $inventoryOpen = request()->routeIs('inventory.*');
                $adminOpen = request()->routeIs('users.*', 'roles.*', 'permissions.*', 'modes.*', 'audits.*');
            @endphp

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Menu')" class="grid gap-2">
                    <flux:sidebar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>

                    @if ($role?->manage_attendance)
                        <details @if ($attendanceOpen) open @endif>
                            <summary class="flex cursor-pointer list-none items-center gap-3 rounded-md px-2 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-200/60 dark:text-zinc-200 dark:hover:bg-zinc-800">
                                <flux:icon.layout-grid class="size-4 text-zinc-500" />
                                <span class="flex-1">{{ __('Attendance') }}</span>
                                <span class="text-lg leading-none text-zinc-500">&gt;</span>
                            </summary>

                            <div class="ms-4 mt-2 grid gap-1.5 border-s border-zinc-200 ps-2 dark:border-zinc-700">
                                <flux:sidebar.item icon="layout-grid" :href="route('attendance.index')" :current="request()->routeIs('attendance.index')" wire:navigate>
                                    {{ __('Attendance') }}
                                </flux:sidebar.item>

                                <flux:sidebar.item icon="layout-grid" :href="route('attendance.add')" :current="request()->routeIs('attendance.add')" wire:navigate>
                                    {{ __('add attendace') }}
                                </flux:sidebar.item>

                                <flux:sidebar.item icon="book-open-text" :href="route('attendance.summary')" :current="request()->routeIs('attendance.summary')" wire:navigate>
                                    {{ __('Summary') }}
                                </flux:sidebar.item>

                                <flux:sidebar.item icon="book-open-text" :href="route('attendance.guard')" :current="request()->routeIs('attendance.guard')" wire:navigate>
                                    {{ __('By Guard') }}
                                </flux:sidebar.item>

                                <flux:sidebar.item icon="layout-grid" :href="route('attendance.generate')" :current="request()->routeIs('attendance.generate')" wire:navigate>
                                    {{ __('Generate Attendance') }}
                                </flux:sidebar.item>
                            </div>
                        </details>
                    @endif

                    @if ($role?->view_clients || $role?->add_client || $role?->assign_guards || $role?->view_client_schedule || $role?->mange_client_categories)
                        <details @if ($clientsOpen) open @endif>
                            <summary class="flex cursor-pointer list-none items-center gap-3 rounded-md px-2 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-200/60 dark:text-zinc-200 dark:hover:bg-zinc-800">
                                <flux:icon.folder-git-2 class="size-4 text-zinc-500" />
                                <span class="flex-1">{{ __('Clients') }}</span>
                                <span class="text-lg leading-none text-zinc-500">&gt;</span>
                            </summary>

                            <div class="ms-4 mt-2 grid gap-1.5 border-s border-zinc-200 ps-2 dark:border-zinc-700">
                                @if ($role?->add_client)
                                    <flux:sidebar.item icon="layout-grid" :href="route('clients.create')" :current="request()->routeIs('clients.create')" wire:navigate>
                                        {{ __('New Client') }}
                                    </flux:sidebar.item>
                                @endif

                                @if ($role?->view_clients)
                                    <flux:sidebar.item icon="layout-grid" :href="route('clients.index')" :current="request()->routeIs('clients.index', 'clients.edit')" wire:navigate>
                                        {{ __('Clients') }}
                                    </flux:sidebar.item>
                                @endif

                                @if ($role?->assign_guards)
                                    <flux:sidebar.item icon="book-open-text" :href="route('assignments.index')" :current="request()->routeIs('assignments.*')" wire:navigate>
                                        {{ __('Assign Guard') }}
                                    </flux:sidebar.item>
                                @endif

                                @if ($role?->view_client_schedule)
                                    <flux:sidebar.item icon="book-open-text" :href="route('clients.schedule')" :current="request()->routeIs('clients.schedule')" wire:navigate>
                                        {{ __('Client Schedule') }}
                                    </flux:sidebar.item>
                                @endif

                                @if ($role?->view_clients)
                                    <flux:sidebar.item icon="book-open-text" :href="route('clients.cycle')" :current="request()->routeIs('clients.cycle')" wire:navigate>
                                        {{ __('Guard Cycle') }}
                                    </flux:sidebar.item>
                                @endif

                                @if ($role?->mange_client_categories)
                                    <flux:sidebar.item icon="book-open-text" :href="route('client-categories.index')" :current="request()->routeIs('client-categories.*')" wire:navigate>
                                        {{ __('Categories') }}
                                    </flux:sidebar.item>
                                @endif

                                @if ($role?->view_clients)
                                    <flux:sidebar.item icon="book-open-text" :href="route('clients.former')" :current="request()->routeIs('clients.former')" wire:navigate>
                                        {{ __('Former Clients') }}
                                    </flux:sidebar.item>
                                @endif
                            </div>
                        </details>
                    @endif

                    @if ($role?->view_guards || $role?->add_guards || $role?->edit_guards)
                        <details @if ($guardsOpen) open @endif>
                            <summary class="flex cursor-pointer list-none items-center gap-3 rounded-md px-2 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-200/60 dark:text-zinc-200 dark:hover:bg-zinc-800">
                                <flux:icon.book-open-text class="size-4 text-zinc-500" />
                                <span class="flex-1">{{ __('Guards') }}</span>
                                <span class="text-lg leading-none text-zinc-500">&gt;</span>
                            </summary>

                            <div class="ms-4 mt-2 grid gap-1.5 border-s border-zinc-200 ps-2 dark:border-zinc-700">
                                @if ($role?->add_guards || $role?->view_guards)
                                    <flux:sidebar.item icon="layout-grid" :href="route('guards.create')" :current="request()->routeIs('guards.create')" wire:navigate>
                                        {{ __('New Guard') }}
                                    </flux:sidebar.item>
                                @endif

                                @if ($role?->view_guards)
                                    <flux:sidebar.item icon="folder-git-2" :href="route('guards.index')" :current="request()->routeIs('guards.index', 'guards.edit')" wire:navigate>
                                        {{ __('Guards') }}
                                    </flux:sidebar.item>

                                    <flux:sidebar.item icon="folder-git-2" :href="route('guards.former')" :current="request()->routeIs('guards.former')" wire:navigate>
                                        {{ __('Former Guards') }}
                                    </flux:sidebar.item>
                                @endif
                            </div>
                        </details>
                    @endif

                    @if ($role?->manage_guns)
                        <details @if ($armoryOpen) open @endif>
                            <summary class="flex cursor-pointer list-none items-center gap-3 rounded-md px-2 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-200/60 dark:text-zinc-200 dark:hover:bg-zinc-800">
                                <flux:icon.layout-grid class="size-4 text-zinc-500" />
                                <span class="flex-1">{{ __('Armory') }}</span>
                                <span class="text-lg leading-none text-zinc-500">&gt;</span>
                            </summary>

                            <div class="ms-4 mt-2 grid gap-1.5 border-s border-zinc-200 ps-2 dark:border-zinc-700">
                                <flux:sidebar.item icon="layout-grid" :href="route('armory.guns')" :current="request()->routeIs('armory.guns')" wire:navigate>
                                    {{ __('Guns') }}
                                </flux:sidebar.item>

                                <flux:sidebar.item icon="folder-git-2" :href="route('armory.assignments')" :current="request()->routeIs('armory.assignments')" wire:navigate>
                                    {{ __('Gun Assignment') }}
                                </flux:sidebar.item>

                                <flux:sidebar.item icon="book-open-text" :href="route('armory.bullet-additions')" :current="request()->routeIs('armory.bullet-additions')" wire:navigate>
                                    {{ __('Add Bullets') }}
                                </flux:sidebar.item>

                                <flux:sidebar.item icon="book-open-text" :href="route('armory.bullet-usage')" :current="request()->routeIs('armory.bullet-usage')" wire:navigate>
                                    {{ __('Bullets Usage') }}
                                </flux:sidebar.item>

                                <flux:sidebar.item icon="book-open-text" :href="route('armory.maintenance')" :current="request()->routeIs('armory.maintenance')" wire:navigate>
                                    {{ __('Maintenance') }}
                                </flux:sidebar.item>
                            </div>
                        </details>
                    @endif

                    @if ($role?->view_expenses || $role?->record_expenses || $role?->edit_expenses || $role?->delete_expenses)
                        <details @if ($expensesOpen) open @endif>
                            <summary class="flex cursor-pointer list-none items-center gap-3 rounded-md px-2 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-200/60 dark:text-zinc-200 dark:hover:bg-zinc-800">
                                <flux:icon.book-open-text class="size-4 text-zinc-500" />
                                <span class="flex-1">{{ __('Expenses') }}</span>
                                <span class="text-lg leading-none text-zinc-500">&gt;</span>
                            </summary>

                            <div class="ms-4 mt-2 grid gap-1.5 border-s border-zinc-200 ps-2 dark:border-zinc-700">
                                @if ($role?->view_expenses)
                                    <flux:sidebar.item icon="layout-grid" :href="route('expenses.index')" :current="request()->routeIs('expenses.index')" wire:navigate>
                                        {{ __('Expenses') }}
                                    </flux:sidebar.item>

                                    <flux:sidebar.item icon="book-open-text" :href="route('expenses.categories')" :current="request()->routeIs('expenses.categories')" wire:navigate>
                                        {{ __('Categories') }}
                                    </flux:sidebar.item>

                                    <flux:sidebar.item icon="book-open-text" :href="route('expenses.budgets')" :current="request()->routeIs('expenses.budgets')" wire:navigate>
                                        {{ __('Budgets') }}
                                    </flux:sidebar.item>
                                @endif
                            </div>
                        </details>
                    @endif

                    @if ($role?->manage_inventory)
                        <details @if ($inventoryOpen) open @endif>
                            <summary class="flex cursor-pointer list-none items-center gap-3 rounded-md px-2 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-200/60 dark:text-zinc-200 dark:hover:bg-zinc-800">
                                <flux:icon.layout-grid class="size-4 text-zinc-500" />
                                <span class="flex-1">{{ __('Inventory') }}</span>
                                <span class="text-lg leading-none text-zinc-500">&gt;</span>
                            </summary>

                            <div class="ms-4 mt-2 grid gap-1.5 border-s border-zinc-200 ps-2 dark:border-zinc-700">
                                <flux:sidebar.item icon="layout-grid" :href="route('inventory.items')" :current="request()->routeIs('inventory.items')" wire:navigate>
                                    {{ __('Items') }}
                                </flux:sidebar.item>

                                <flux:sidebar.item icon="book-open-text" :href="route('inventory.categories')" :current="request()->routeIs('inventory.categories')" wire:navigate>
                                    {{ __('Categories') }}
                                </flux:sidebar.item>

                                <flux:sidebar.item icon="book-open-text" :href="route('inventory.units')" :current="request()->routeIs('inventory.units')" wire:navigate>
                                    {{ __('Units') }}
                                </flux:sidebar.item>

                                <flux:sidebar.item icon="folder-git-2" :href="route('inventory.stock-ins')" :current="request()->routeIs('inventory.stock-ins')" wire:navigate>
                                    {{ __('Stock In') }}
                                </flux:sidebar.item>

                                <flux:sidebar.item icon="folder-git-2" :href="route('inventory.stock-usages')" :current="request()->routeIs('inventory.stock-usages')" wire:navigate>
                                    {{ __('Stock Usage') }}
                                </flux:sidebar.item>

                                <flux:sidebar.item icon="book-open-text" :href="route('inventory.movements')" :current="request()->routeIs('inventory.movements')" wire:navigate>
                                    {{ __('Movements') }}
                                </flux:sidebar.item>
                            </div>
                        </details>
                    @endif

                    {{-- Old sidebar order continues here as modules are ported: Bills, Staff. --}}

                    @if ($role?->view_users || $role?->manage_permission || $role?->view_paymodes || $role?->view_logs)
                        <details @if ($adminOpen) open @endif>
                            <summary class="flex cursor-pointer list-none items-center gap-3 rounded-md px-2 py-2.5 text-sm font-medium text-zinc-700 hover:bg-zinc-200/60 dark:text-zinc-200 dark:hover:bg-zinc-800">
                                <flux:icon.folder-git-2 class="size-4 text-zinc-500" />
                                <span class="flex-1">{{ __('Administration') }}</span>
                                <span class="text-lg leading-none text-zinc-500">&gt;</span>
                            </summary>

                            <div class="ms-4 mt-2 grid gap-1.5 border-s border-zinc-200 ps-2 dark:border-zinc-700">
                                @if ($role?->view_users)
                                    <flux:sidebar.item icon="layout-grid" :href="route('users.index')" :current="request()->routeIs('users.*')" wire:navigate>
                                        {{ __('Users') }}
                                    </flux:sidebar.item>
                                @endif

                                @if ($role?->manage_permission)
                                    <flux:sidebar.item icon="folder-git-2" :href="route('roles.index')" :current="request()->routeIs('roles.*')" wire:navigate>
                                        {{ __('Roles') }}
                                    </flux:sidebar.item>

                                    <flux:sidebar.item icon="book-open-text" :href="route('permissions.index')" :current="request()->routeIs('permissions.*')" wire:navigate>
                                        {{ __('Permissions') }}
                                    </flux:sidebar.item>
                                @endif

                                @if ($role?->view_paymodes)
                                    <flux:sidebar.item icon="folder-git-2" :href="route('modes.index')" :current="request()->routeIs('modes.*')" wire:navigate>
                                        {{ __('Payment Modes') }}
                                    </flux:sidebar.item>
                                @endif

                                @if ($role?->view_logs)
                                    <flux:sidebar.item icon="book-open-text" :href="route('audits.index')" :current="request()->routeIs('audits.*')" wire:navigate>
                                        {{ __('Audit Logs') }}
                                    </flux:sidebar.item>
                                @endif
                            </div>
                        </details>
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
