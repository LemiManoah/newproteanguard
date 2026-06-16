<section class="flex h-full w-full flex-1 flex-col gap-6">
    <div>
        <flux:heading size="xl">{{ $business->name }}</flux:heading>
        <flux:text>{{ __('Operations dashboard') }}</flux:text>
    </div>

    <div class="grid gap-4 md:grid-cols-3 xl:grid-cols-5">
        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <flux:text>{{ __('Clients') }}</flux:text>
            <div class="mt-2 text-2xl font-semibold">{{ number_format($metrics['clients']) }}</div>
        </div>

        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <flux:text>{{ __('Guards') }}</flux:text>
            <div class="mt-2 text-2xl font-semibold">{{ number_format($metrics['guards']) }}</div>
        </div>

        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <flux:text>{{ __('Attendance') }}</flux:text>
            <div class="mt-2 text-2xl font-semibold">{{ number_format($metrics['attendance']) }}</div>
        </div>

        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <flux:text>{{ __('Billed') }}</flux:text>
            <div class="mt-2 text-2xl font-semibold">{{ number_format((float) $metrics['billed']) }}</div>
        </div>

        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <flux:text>{{ __('Paid') }}</flux:text>
            <div class="mt-2 text-2xl font-semibold">{{ number_format((float) $metrics['paid']) }}</div>
        </div>
    </div>
</section>
