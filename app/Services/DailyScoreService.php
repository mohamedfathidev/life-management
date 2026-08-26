<?php

namespace App\Services;

use App\Models\Challenge;
use App\Models\Habit;
use App\Models\MentalNutritionLog;
use App\Models\PrayerDay;
use App\Models\Project;
use App\Models\ProjectStep;
use App\Models\QuranWirdDay;
use App\Models\Recovery;
use App\Models\RecoveryLog;
use App\Models\RecoveryTopic;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Computes a per-day composite "how the day actually went" score (0-100) from
 * concrete signals — tasks/habits/challenges/lab-experiments done, prayers,
 * Quran wird, mental nutrition — the same "day progress" formula used live on
 * the Simply page, replayed across an arbitrary date range. Recovery status is
 * NOT one more equal-weight item in that ratio: it's applied afterward as a
 * boost (clean day) or a much heavier penalty (setback), so a relapse visibly
 * crashes the day's score instead of just costing one point among many.
 */
class DailyScoreService
{
    private const RECOVERY_CLEAN_BOOST = 10;

    private const RECOVERY_SETBACK_PENALTY = 30;

    public function __construct(private readonly User $user)
    {
    }

    /**
     * @return Collection<int, array{date: Carbon, percent: int, basePercent: int, done: int, total: int, setback: bool, breakdown: array<string, mixed>}>
     */
    public function forRange(Carbon $start, Carbon $end): Collection
    {
        $end = Carbon::today()->lt($end) ? Carbon::today() : $end;

        if ($end->lt($start)) {
            return collect();
        }

        $from = $start->toDateString();
        $to = $end->toDateString();

        $tasksByDate = Task::query()->ownedBy($this->user)
            ->whereHas('day', fn ($q) => $q->whereBetween('date', [$from, $to]))
            ->with('day:id,date')
            ->get()
            ->groupBy(fn (Task $t) => $t->day->date->toDateString());

        $habits = Habit::query()->ownedBy($this->user)
            ->with(['logs' => fn ($q) => $q->whereBetween('date', [$from, $to])])
            ->get();

        $challenges = Challenge::query()->ownedBy($this->user)
            ->with(['logs' => fn ($q) => $q->whereBetween('date', [$from, $to])])
            ->get();

        $prayerByDate = PrayerDay::query()->ownedBy($this->user)
            ->whereBetween('date', [$from, $to])->get()
            ->keyBy(fn (PrayerDay $p) => $p->date->toDateString());

        $quranDates = QuranWirdDay::query()->ownedBy($this->user)
            ->whereBetween('date', [$from, $to])->pluck('date')
            ->map(fn ($d) => $d->toDateString())->flip();

        $hasTopics = RecoveryTopic::query()->where('user_id', $this->user->id)->exists();
        $nutritionDates = $hasTopics
            ? MentalNutritionLog::query()->where('user_id', $this->user->id)
                ->whereBetween('date', [$from, $to])->pluck('date')
                ->map(fn ($d) => $d->toDateString())->flip()
            : collect();

        $hasProjects = Project::query()->where('user_id', $this->user->id)->exists();
        $labDates = $hasProjects
            ? ProjectStep::query()
                ->whereHas('project', fn ($q) => $q->where('user_id', $this->user->id))
                ->where('is_done', true)
                ->whereBetween('updated_at', ["{$from} 00:00:00", "{$to} 23:59:59"])
                ->pluck('updated_at')
                ->map(fn ($d) => $d->toDateString())->flip()
            : collect();

        $recoveries = Recovery::query()->ownedBy($this->user)
            ->where('start_date', '<=', $to)
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $from))
            ->get();

        $setbackKeys = RecoveryLog::query()
            ->whereIn('recovery_id', $recoveries->pluck('id'))
            ->where('is_setback', true)
            ->whereBetween('date', [$from, $to])
            ->get()
            ->map(fn (RecoveryLog $l) => $l->recovery_id.'_'.$l->date->toDateString())
            ->flip();

        $days = collect();
        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            $dateKey = $cursor->toDateString();

            $dayTasks = $tasksByDate->get($dateKey, collect());
            $tasksDone = $dayTasks->where('progress', 100)->count();
            $tasksTotal = $dayTasks->count();

            $dueHabits = $habits->filter(fn (Habit $h) => $h->isActiveOn($cursor));
            $habitsDone = $dueHabits->filter(fn (Habit $h) => $h->logs->contains(fn ($l) => $l->date->toDateString() === $dateKey))->count();
            $habitsTotal = $dueHabits->count();

            $dueChallenges = $challenges->filter(fn (Challenge $c) => $cursor->between($c->start_date, $c->endDate()));
            $challengesDone = $dueChallenges->filter(fn (Challenge $c) => $c->logs->contains(fn ($l) => $l->date->toDateString() === $dateKey))->count();
            $challengesTotal = $dueChallenges->count();

            $prayerDone = $prayerByDate->get($dateKey)?->doneCount() ?? 0;
            $quranDone = $quranDates->has($dateKey);
            $nutritionDone = $hasTopics && $nutritionDates->has($dateKey);
            $labDone = $hasProjects && $labDates->has($dateKey);

            // Base score: equal-weight ratio across everything EXCEPT recovery.
            $done = $tasksDone + $habitsDone + $challengesDone
                + ($prayerDone >= 5 ? 1 : 0) + ($quranDone ? 1 : 0) + ($nutritionDone ? 1 : 0) + ($labDone ? 1 : 0);
            $total = $tasksTotal + $habitsTotal + $challengesTotal
                + 2 + ($hasTopics ? 1 : 0) + ($hasProjects ? 1 : 0);

            $basePercent = $total > 0 ? (int) round($done / $total * 100) : 0;

            // Recovery: a modifier on top of the base score, not one more equal-weight item.
            $dueRecoveries = $recoveries->filter(fn (Recovery $r) => $cursor->gte($r->start_date) && (! $r->end_date || $cursor->lte($r->end_date)));
            $recoverySetbackToday = $dueRecoveries->filter(fn (Recovery $r) => $setbackKeys->has($r->id.'_'.$dateKey));
            $hadSetback = $recoverySetbackToday->isNotEmpty();

            $percent = $basePercent;
            if ($dueRecoveries->isNotEmpty()) {
                $percent = $hadSetback
                    ? max(0, $basePercent - self::RECOVERY_SETBACK_PENALTY)
                    : min(100, $basePercent + self::RECOVERY_CLEAN_BOOST);
            }

            $days->push([
                'date' => $cursor->copy(),
                'percent' => $percent,
                'basePercent' => $basePercent,
                'done' => $done,
                'total' => $total,
                'setback' => $hadSetback,
                'breakdown' => [
                    'tasks' => [$tasksDone, $tasksTotal],
                    'habits' => [$habitsDone, $habitsTotal],
                    'challenges' => [$challengesDone, $challengesTotal],
                    'prayers' => $prayerDone,
                    'quran' => $quranDone,
                    'nutrition' => $nutritionDone,
                    'lab' => $labDone,
                    'recovery' => [$dueRecoveries->count() - $recoverySetbackToday->count(), $dueRecoveries->count()],
                ],
            ]);

            $cursor->addDay();
        }

        return $days;
    }
}
