<?php

namespace App\Services;

use App\Enums\ChallengeStatus;
use App\Enums\RecoveryStatus;
use App\Models\Appointment;
use App\Models\Challenge;
use App\Models\Day;
use App\Models\Habit;
use App\Models\PrayerDay;
use App\Models\Recovery;
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
     * Mood values for the current week (Saturday to Friday), oldest first.
     * Combines mood from DailyLogs, Day closure ratings, and RecoveryLogs.
     *
     * @return array{startDate: Carbon, endDate: Carbon, points: array<int, array{date:string, dayName:string, dayNumber:string, mood:int|null, isToday:bool}>}
     */
    public function moodTrendForWeek(): array
    {
        $today = Carbon::today();
        $start = $today->dayOfWeek === Carbon::SATURDAY
            ? $today->copy()
            : $today->copy()->previous(Carbon::SATURDAY);

        $end = $start->copy()->addDays(6); // Friday

        // Fetch moods from all sources
        $dailyLogs = $this->user->dailyLogs()
            ->whereNotNull('mood')
            ->whereBetween('date', [$start, $end])
            ->get();

        $days = Day::ownedBy($this->user)
            ->whereNotNull('rating')
            ->whereBetween('date', [$start, $end])
            ->get();

        $recoveryLogs = \App\Models\RecoveryLog::query()
            ->whereHas('recovery', fn ($q) => $q->where('user_id', $this->user->id))
            ->whereNotNull('mood')
            ->whereBetween('date', [$start, $end])
            ->get();

        $points = [];
        for ($i = 0; $i < 7; $i++) {
            $dateObj = $start->copy()->addDays($i);
            $d = $dateObj->toDateString();
            $scores = [];

            foreach ($dailyLogs->filter(fn ($l) => $l->date->toDateString() === $d) as $dl) {
                $scores[] = (int) $dl->mood;
            }
            foreach ($days->filter(fn ($l) => $l->date->toDateString() === $d) as $day) {
                $scores[] = (int) $day->rating;
            }
            foreach ($recoveryLogs->filter(fn ($l) => $l->date->toDateString() === $d) as $rl) {
                $scores[] = (int) $rl->mood;
            }

            $avgMood = ! empty($scores) ? (int) round(array_sum($scores) / count($scores)) : null;

            $emoji = match (true) {
                $avgMood === null => '',
                $avgMood >= 9 => '🤩',
                $avgMood >= 7 => '😊',
                $avgMood >= 5 => '😐',
                $avgMood >= 3 => '😔',
                default => '😖',
            };

            $points[] = [
                'date' => $d,
                'dayName' => $dateObj->translatedFormat('D'),
                'dayNumber' => $dateObj->translatedFormat('j M'),
                'mood' => $avgMood,
                'emoji' => $emoji,
                'isToday' => $d === $today->toDateString(),
            ];
        }

        return [
            'startDate' => $start,
            'endDate' => $end,
            'points' => $points,
        ];
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

    // ---------------------------------------------------------------------
    // Today at a glance (cross-module)
    // ---------------------------------------------------------------------

    /** @return array{total:int, done:int, percent:int, started:bool, closed:bool} */
    public function todayPlan(): array
    {
        $day = Day::query()->ownedBy($this->user)->forDate(Carbon::today())->with('tasks')->first();
        $tasks = $day?->tasks ?? collect();

        return [
            'total' => $tasks->count(),
            'done' => $tasks->where('progress', 100)->count(),
            'percent' => $day?->completionPercent() ?? 0,
            'started' => (bool) $day?->isStarted(),
            'closed' => (bool) $day?->isClosed(),
        ];
    }

    /** @return array{done:int, total:int, onTime:int} */
    public function prayersToday(): array
    {
        $day = PrayerDay::query()->ownedBy($this->user)->whereDate('date', Carbon::today())->first();

        return [
            'done' => $day?->doneCount() ?? 0,
            'total' => 5,
            'onTime' => $day?->onTimeCount() ?? 0,
        ];
    }

    /** @return array{done:int, total:int} */
    public function habitsToday(): array
    {
        $habits = Habit::query()->ownedBy($this->user)->active()
            ->with(['logs' => fn ($q) => $q->whereDate('date', Carbon::today())])
            ->get();

        return [
            'done' => $habits->filter(fn (Habit $h) => $h->logs->isNotEmpty())->count(),
            'total' => $habits->count(),
        ];
    }

    /** @return array{done:int, total:int} */
    public function challengesToday(): array
    {
        $challenges = Challenge::query()->ownedBy($this->user)
            ->where('status', ChallengeStatus::Active)
            ->with(['logs' => fn ($q) => $q->whereDate('date', Carbon::today())])
            ->get();

        return [
            'done' => $challenges->filter(fn (Challenge $c) => $c->logs->isNotEmpty())->count(),
            'total' => $challenges->count(),
        ];
    }

    /** Active recoveries with their current clean streak. */
    public function activeRecoveries(int $limit = 4)
    {
        return Recovery::query()->ownedBy($this->user)
            ->where('status', RecoveryStatus::Active)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /** Upcoming appointments (today onward). */
    public function upcomingAppointments(int $limit = 5)
    {
        return Appointment::query()->ownedBy($this->user)
            ->whereDate('date', '>=', Carbon::today())
            ->orderBy('date')
            ->orderBy('time')
            ->limit($limit)
            ->get();
    }
}
