<section class="max-w-2xl mx-auto flex flex-col gap-6">
    <div>
        <flux:heading size="xl">{{ __('Generate Attendance') }}</flux:heading>
        <flux:text>{{ __('Generate daily attendance records from active client-guard deployments for a range of dates.') }}</flux:text>
    </div>

    @if (session()->has('success'))
        <div class="rounded-md bg-green-50 p-4 text-sm text-green-700 dark:bg-green-950/30 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit="generate" class="grid gap-6 rounded-lg border border-zinc-200 p-6 dark:border-zinc-700 bg-white dark:bg-zinc-900">
        <div class="grid gap-6 md:grid-cols-2">
            <flux:input wire:model="startDate" type="date" label="{{ __('Start Date') }}" required />

            <flux:input wire:model="endDate" type="date" label="{{ __('End Date') }}" required />
        </div>

        <div class="flex justify-end gap-3 border-t border-zinc-200 pt-6 dark:border-zinc-700">
            <flux:button type="submit" variant="primary">{{ __('Generate') }}</flux:button>
        </div>
    </form>
</section>
