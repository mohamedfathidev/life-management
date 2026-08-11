<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * Aggregates dashboard statistics for a user.
 * Later phases (streaks, prayers, deadlines) extend this single entry point
 * rather than scattering queries across Livewire components.
 */
class DashboardService
{
    public function __construct(private readonly User $user)
    {
    }

    public static function for(User $user): self
    {
        return new self($user);
    }

    public function activeGoalsCount(): int
    {
        return $this->user->goals()->active()->count();
    }

    public function totalGoalsCount(): int
    {
        return $this->user->goals()->count();
    }

    public function logsTodayCount(): int
    {
        return $this->user->dailyLogs()->forDate(Carbon::today())->count();
    }

    /**
     * Mood values for the last 7 days (today inclusive), oldest first.
     * Missing days are null so the chart can show gaps.
     *
     * @return array<int, array{date:string, mood:int|null}>
     */
    public function moodTrend(int $days = 7): array
    {
        $start = Carbon::today()->subDays($days - 1);

        $byDate = $this->user->dailyLogs()
            ->whereNotNull('mood')
            ->whereDate('date', '>=', $start)
            ->get()
            ->groupBy(fn ($log) => $log->date->toDateString())
            ->map(fn ($logs) => (int) round($logs->avg('mood')));

        $trend = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i)->toDateString();
            $trend[] = ['date' => $date, 'mood' => $byDate[$date] ?? null];
        }

        return $trend;
    }

    /** Goals with an upcoming (future) target date, soonest first. */
    public function upcomingDeadlines(int $limit = 5)
    {
        return $this->user->goals()
            ->whereNotNull('target_date')
            ->whereDate('target_date', '>=', Carbon::today())
            ->orderBy('target_date')
            ->limit($limit)
            ->get();
    }
}
