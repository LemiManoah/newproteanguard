<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class AttendanceHelperService
{
    /**
     * @return array<string, array{label: string, from: string, to: string}>
     */
    public function dateRanges(?CarbonImmutable $today = null): array
    {
        $today ??= CarbonImmutable::today();

        return [
            'today' => [
                'label' => 'Today',
                'from' => $today->toDateString(),
                'to' => $today->toDateString(),
            ],
            'yesterday' => [
                'label' => 'Yesterday',
                'from' => $today->subDay()->toDateString(),
                'to' => $today->subDay()->toDateString(),
            ],
            'this_week' => [
                'label' => 'This week',
                'from' => $today->startOfWeek()->toDateString(),
                'to' => $today->endOfWeek()->toDateString(),
            ],
            'this_month' => [
                'label' => 'This month',
                'from' => $today->startOfMonth()->toDateString(),
                'to' => $today->endOfMonth()->toDateString(),
            ],
        ];
    }

    /**
     * @return array{0: string, 1: string}
     */
    public function resolveDateRange(?string $range, ?string $from, ?string $to): array
    {
        $ranges = $this->dateRanges();

        if ($range && $range !== 'custom' && isset($ranges[$range])) {
            return [$ranges[$range]['from'], $ranges[$range]['to']];
        }

        $from = $from ?: today()->toDateString();
        $to = $to ?: $from;

        if ($from > $to) {
            return [$to, $from];
        }

        return [$from, $to];
    }

    /**
     * @return Collection<int, string>
     */
    public function datesBetween(string $from, string $to): Collection
    {
        return collect(CarbonPeriod::create($from, $to))
            ->map(fn ($date): string => $date->toDateString());
    }

    public function dutyLabel(bool $overtime): string
    {
        return $overtime ? 'Overtime' : 'Duty';
    }

    public function attendanceBadgeColor(?AttendanceStatus $status): string
    {
        return match ($status) {
            AttendanceStatus::Present => 'green',
            AttendanceStatus::Absent => 'red',
            AttendanceStatus::Replaced => 'amber',
            default => 'zinc',
        };
    }
}
