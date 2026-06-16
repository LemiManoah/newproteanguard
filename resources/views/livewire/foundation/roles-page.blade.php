<section class="flex h-full w-full flex-1 flex-col gap-6">
    <flux:heading size="xl">{{ __('Roles') }}</flux:heading>

    <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 text-left dark:bg-zinc-900">
                <tr>
                    <th class="px-4 py-3">{{ __('Name') }}</th>
                    <th class="px-4 py-3">{{ __('Users') }}</th>
                    <th class="px-4 py-3">{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach ($roles as $role)
                    <tr>
                        <td class="px-4 py-3">{{ $role->name }}</td>
                        <td class="px-4 py-3">{{ $role->users()->count() }}</td>
                        <td class="px-4 py-3">{{ $role->status ? __('Active') : __('Inactive') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
