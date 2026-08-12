<?php

namespace App\Services;

use App\Enums\ChallengeStatus;
use App\Models\Challenge;
use App\Models\ChallengeLog;
use App\Models\ComfortExperience;
use App\Models\DailyLog;
use App\Models\Day;
use App\Models\DiaryEntry;
use App\Models\Donation;
use App\Models\Goal;
use App\Models\GoalReview;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\PrayerDay;
use App\Models\QuranLog;
use App\Models\Recovery;
use App\Models\RecoveryLog;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * Builds an auto-generated review (weekly/monthly) that aggregates the user's
 * achievements across every module for a [start, end] date range.
 */
class ReviewService
{
    private string $from;
    private string $to;

    public function __construct(private readonly User $user, Carbon $start, Carbon $end)
    {
        $this->from = $start->toDateString();
        $this->to = $end->toDateString();
    }

    /** @return array<string, mixed> */
    public function summary(): array
    {
        return [
            'prayers' => $this->prayers(),
            'habits' => $this->habitCheckins(),
            'challenges' => $this->challenges(),
            'donations' => $this->donations(),
            'quranPages' => $this->quranPages(),
            'planner' => $this->planner(),
            'goalsCompleted' => $this->goalsCompleted(),
            'activeGoals' => $this->user->goals()->active()->count(),
            'moodAvg' => $this->moodAverage(),
            'recoverySetbacks' => $this->recoverySetbacks(),
            'comfortDone' => $this->comfortDone(),
            'diaryEntries' => $this->diaryEntries(),
        ];
    }

    /** @return array{completion:int, onTime:int, days:int} */
    private function prayers(): array
    {
        $days = PrayerDay::query()->ownedBy($this->user)
            ->whereBetween('date', [$this->from, $this->to])->get();

        $elapsed = $this->elapsedDays();
        $possible = max(1, $elapsed * 5);

        return [
            'completion' => (int) round($days->sum(fn (PrayerDay $d) => $d->doneCount()) / $possible * 100),
            'onTime' => (int) round($days->sum(fn (PrayerDay $d) => $d->onTimeCount()) / $possible * 100),
            'days' => $days->count(),
        ];
    }

    private function habitCheckins(): int
    {
        $ids = Habit::query()->ownedBy($this->user)->pluck('id');

        return HabitLog::query()->whereIn('habit_id', $ids)
            ->whereBetween('date', [$this->from, $this->to])->count();
    }

    /** @return array{checkins:int, active:int} */
    private function challenges(): array
    {
        $ids = Challenge::query()->ownedBy($this->user)->pluck('id');

        return [
            'checkins' => ChallengeLog::query()->whereIn('challenge_id', $ids)
                ->whereBetween('date', [$this->from, $this->to])->count(),
            'active' => Challenge::query()->ownedBy($this->user)->where('status', ChallengeStatus::Active)->count(),
        ];
    }

    /** @return array{total:float, count:int} */
    private function donations(): array
    {
        $q = Donation::query()->ownedBy($this->user)->whereBetween('date', [$this->from, $this->to]);

        return ['total' => (float) $q->sum('amount'), 'count' => $q->count()];
    }

    private function quranPages(): int
    {
        return (int) QuranLog::query()->ownedBy($this->user)
            ->whereBetween('date', [$this->from, $this->to])->sum('pages');
    }

    /** @return array{daysClosed:int, tasksDone:int, workedMinutes:int} */
    private function planner(): array
    {
        $days = Day::query()->ownedBy($this->user)
            ->whereBetween('date', [$this->from, $this->to])
            ->with('breaks')
            ->get();

        $dayIds = $days->pluck('id');

        return [
            'daysClosed' => $days->where('status', \App\Enums\DayStatus::Closed)->count(),
            'tasksDone' => Task::query()->whereIn('day_id', $dayIds)->where('progress', 100)->count(),
            'workedMinutes' => (int) $days->sum(fn (Day $d) => $d->workedMinutes()),
        ];
    }

    private function goalsCompleted(): int
    {
        return GoalReview::query()->where('user_id', $this->user->id)
            ->whereBetween('closed_on', [$this->from, $this->to])->count();
    }

    private function moodAverage(): ?float
    {
        $avg = DailyLog::query()->ownedBy($this->user)
            ->whereNotNull('mood')
            ->whereBetween('date', [$this->from, $this->to])
            ->avg('mood');

        return $avg ? round($avg, 1) : null;
    }

    private function recoverySetbacks(): int
    {
        $ids = Recovery::query()->ownedBy($this->user)->pluck('id');

        return RecoveryLog::query()->whereIn('recovery_id', $ids)
            ->where('is_setback', true)
            ->whereBetween('date', [$this->from, $this->to])->count();
    }

    private function comfortDone(): int
    {
        return ComfortExperience::query()->ownedBy($this->user)
            ->whereBetween('done_on', [$this->from, $this->to])->count();
    }

    private function diaryEntries(): int
    {
        return DiaryEntry::query()->ownedBy($this->user)
            ->whereBetween('date', [$this->from, $this->to])->count();
    }

    /** Days in the range that have already elapsed (start → min(end, today)). */
    private function elapsedDays(): int
    {
        $start = Carbon::parse($this->from);
        $end = Carbon::parse($this->to);
        $today = Carbon::today();
        $effectiveEnd = $end->gt($today) ? $today : $end;

        if ($effectiveEnd->lt($start)) {
            return 0;
        }

        return (int) $start->diffInDays($effectiveEnd) + 1;
    }
}
