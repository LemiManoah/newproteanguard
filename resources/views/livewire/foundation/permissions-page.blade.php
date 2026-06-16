<section class="flex h-full w-full flex-1 flex-col gap-6">
    <flux:heading size="xl">{{ __('Permissions') }}</flux:heading>

    <div class="overflow-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
        <table class="w-full min-w-max text-sm">
            <thead class="bg-zinc-50 text-left dark:bg-zinc-900">
                <tr>
                    <th class="sticky left-0 bg-zinc-50 px-4 py-3 dark:bg-zinc-900">{{ __('Permission') }}</th>
                    @foreach ($roles as $role)
                        <th class="px-4 py-3">{{ $role->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach ($permissionColumns as $column)
                    <tr>
                        <td class="sticky left-0 bg-white px-4 py-3 font-medium dark:bg-zinc-800">{{ str($column)->replace('_', ' ')->headline() }}</td>
                        @foreach ($roles as $role)
                            <td class="px-4 py-3">
                                <button
                                    type="button"
                                    wire:click="toggle({{ $role->id }}, '{{ $column }}')"
                                    class="rounded border px-3 py-1 text-xs {{ $role->getAttribute($column) ? 'border-emerald-500 text-emerald-600' : 'border-zinc-300 text-zinc-500 dark:border-zinc-600' }}"
                                >
                                    {{ $role->getAttribute($column) ? __('On') : __('Off') }}
                                </button>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
