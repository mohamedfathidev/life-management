<?php

namespace App\Services;

use App\Enums\DayStatus;
use App\Models\Day;
use App\Models\DayBreak;

/**
 * The day workflow: starting/ending the day, breaks, and closing the day
 * (rating + reflection + carrying over unfinished tasks).
 */
class DayService
{
    public function __construct(private readonly PlannerService $planner)
    {
    }

    public function start(Day $day): void
    {
        if (! $day->started_at) {
            $day->started_at = now();
            $day->save();
        }
    }

    /** Set an explicit start time (user determines it, e.g. after waking late). */
    public function setStartedAt(Day $day, \Illuminate\Support\Carbon $at): void
    {
        $day->started_at = $at;
        $day->save();
    }

    public function end(Day $day): void
    {
        $day->ended_at = now();
        $day->save();
    }

    /** Begin a break. No-op if one is already running. */
    public function startBreak(Day $day): ?DayBreak
    {
        if ($day->breaks()->whereNull('ended_at')->exists()) {
            return null;
        }

        // Starting a break implies the day has started.
        $this->start($day);

        return $day->breaks()->create(['started_at' => now()]);
    }

    public function endBreak(DayBreak $break): void
    {
        if (! $break->ended_at) {
            $break->ended_at = now();
            $break->save();
        }
    }

    /**
     * Close the day: apply rating + reflection, end any running break/day,
     * and dispatch unfinished tasks per the user's decisions.
     *
     * @param  array<int, string>  $decisions  taskId => 'carry' | 'pool' (default 'carry')
     */
    public function close(Day $day, int $rating, ?string $reflection, array $decisions = []): void
    {
        // Close any running break first so worked-time is accurate.
        $running = $day->breaks()->whereNull('ended_at')->first();
        if ($running) {
            $this->endBreak($running);
        }

        // Snapshot the REAL completion now, before any tasks are carried over.
        $day->close_summary = $this->completionSnapshot($day);

        foreach ($day->tasks()->incomplete()->get() as $task) {
            $decision = $decisions[$task->id] ?? 'carry';

            if ($decision === 'pool') {
                $task->day_id = null; // send to backlog pool
                $task->save();

                continue;
            }

            // carry: move the same task to the next day, preserving progress
            $nextDay = $this->planner->resolveDay($day->user, $day->date->copy()->addDay());
            $task->day_id = $nextDay->id;
            $task->carry_count++;
            $task->save();
        }

        $day->status = DayStatus::Closed;
        $day->ended_at = $day->ended_at ?? now();
        $day->rating = $rating;
        $day->reflection = $reflection;
        $day->save();
    }

    /**
     * Real completion of the day across tasks + habits + challenges + worship,
     * captured before any task is carried over.
     *
     * @return array<string, int>
     */
    private function completionSnapshot(Day $day): array
    {
        $user = $day->user;
        $date = $day->date;

        $tasks = $day->tasks()->get();
        $tasksTotal = $tasks->count();
        $tasksDone = $tasks->where('progress', 100)->count();
        $carried = $tasks->where('progress', '<', 100)->count();

        $done = $tasksDone;
        $total = $tasksTotal;

        // Habits due that day
        $habits = \App\Models\Habit::query()->where('user_id', $user->id)->where('is_archived', false)
            ->with(['logs' => fn ($q) => $q->whereDate('date', $date)])->get()
            ->filter(fn (\App\Models\Habit $h) => $h->isActiveOn($date));
        $total += $habits->count();
        $done += $habits->filter(fn (\App\Models\Habit $h) => $h->logs->isNotEmpty())->count();

        // Active challenges
        $challenges = \App\Models\Challenge::query()->where('user_id', $user->id)
            ->where('status', \App\Enums\ChallengeStatus::Active)
            ->with(['logs' => fn ($q) => $q->whereDate('date', $date)])->get();
        $total += $challenges->count();
        $done += $challenges->filter(fn (\App\Models\Challenge $c) => $c->logs->isNotEmpty())->count();

        // Prayers (out of 5)
        $prayerDone = \App\Models\PrayerDay::query()->where('user_id', $user->id)->whereDate('date', $date)->first()?->doneCount() ?? 0;
        $total += 5;
        $done += $prayerDone;

        // Quran wird (1)
        $total += 1;
        $done += \App\Models\QuranWirdDay::query()->where('user_id', $user->id)->whereDate('date', $date)->exists() ? 1 : 0;

        // Mental nutrition (1, only if the user has topics)
        if (\App\Models\RecoveryTopic::query()->where('user_id', $user->id)->exists()) {
            $total += 1;
            $done += \App\Models\MentalNutritionLog::query()->where('user_id', $user->id)->whereDate('date', $date)->exists() ? 1 : 0;
        }

        return [
            'percent' => $total > 0 ? (int) round($done / $total * 100) : 0,
            'tasks_done' => $tasksDone,
            'tasks_total' => $tasksTotal,
            'carried' => $carried,
            'done' => $done,
            'total' => $total,
        ];
    }
}
