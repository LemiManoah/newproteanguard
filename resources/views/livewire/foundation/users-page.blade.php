<section class="flex h-full w-full flex-1 flex-col gap-6">
    <flux:heading size="xl">{{ __('Users') }}</flux:heading>

    <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 text-left dark:bg-zinc-900">
                <tr>
                    <th class="px-4 py-3">{{ __('Name') }}</th>
                    <th class="px-4 py-3">{{ __('Email') }}</th>
                    <th class="px-4 py-3">{{ __('Role') }}</th>
                    <th class="px-4 py-3">{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach ($users as $user)
                    <tr>
                        <td class="px-4 py-3">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">{{ $user->role?->name ?? __('Unassigned') }}</td>
                        <td class="px-4 py-3">{{ $user->status ? __('Active') : __('Inactive') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
