<section class="flex h-full w-full flex-1 flex-col gap-6">
    <flux:heading size="xl">{{ __('Audit Logs') }}</flux:heading>

    <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 text-left dark:bg-zinc-900">
                <tr>
                    <th class="px-4 py-3">{{ __('Action') }}</th>
                    <th class="px-4 py-3">{{ __('User') }}</th>
                    <th class="px-4 py-3">{{ __('Date') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach ($logs as $log)
                    <tr>
                        <td class="px-4 py-3">{{ $log->action }}</td>
                        <td class="px-4 py-3">{{ $log->userId }}</td>
                        <td class="px-4 py-3">{{ $log->created_at?->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
