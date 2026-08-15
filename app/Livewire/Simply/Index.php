<?php

namespace App\Livewire\Simply;

use App\Enums\ChallengeStatus;
use App\Models\Appointment;
use App\Models\Challenge;
use App\Models\Habit;
use App\Models\JobApplication;
use App\Models\MentalNutritionLog;
use App\Models\NightCheck;
use App\Models\PrayerDay;
use App\Models\QuranWirdDay;
use App\Models\Recovery;
use App\Models\RecoveryLog;
use App\Models\RecoveryTopic;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * "ببساطة" — a calm, focused view of today only (with timings), with light
 * check-off for tasks / habits / challenges. A quieter alternative to the dashboard.
 */
#[Layout('layouts.app')]
class Index extends Component
{
    public function toggleTask(int $id): void
    {
        $task = Task::ownedBy(Auth::user())->find($id);
        if ($task) {
            $task->setProgress($task->isDone() ? 0 : 100);
            $task->save();
        }
    }

    public function toggleHabit(int $id): void
    {
        $habit = Habit::ownedBy(Auth::user())->find($id);
        if (! $habit) {
            return;
        }
        $today = Carbon::today()->toDateString();
        $habit->isDoneOn($today)
            ? $habit->logs()->whereDate('date', $today)->delete()
            : $habit->logs()->create(['date' => $today]);
    }

    /**
     * Record how *yesterday* went for a recovery (clean / setback). We log the
     * previous day because you evaluate it when you wake up the next morning.
     */
    public function recordYesterday(int $recoveryId, bool $setback): void
    {
        $recovery = Recovery::query()->ownedBy(Auth::user())->active()->find($recoveryId);
        if (! $recovery) {
            return;
        }

        RecoveryLog::updateOrCreate(
            ['recovery_id' => $recovery->id, 'date' => Carbon::yesterday()->toDateString()],
            ['is_setback' => $setback],
        );
    }

    /** Daily check: 'done' (fed mind + prepared for sleep) or 'missed' (stayed up). */
    public function setNight(string $status): void
    {
        if (! in_array($status, ['done', 'missed'], true)) {
            return;
        }

        $today = Carbon::today()->toDateString();
        $existing = NightCheck::where('user_id', Auth::id())->whereDate('date', $today)->first();

        // Clicking the same state again clears it.
        if ($existing && $existing->status === $status) {
            $existing->delete();

            return;
        }

        NightCheck::updateOrCreate(
            ['user_id' => Auth::id(), 'date' => $today],
            ['status' => $status],
        );
    }

    public function toggleAppointment(int $id): void
    {
        $appointment = Appointment::where('user_id', Auth::id())->find($id);
        if ($appointment) {
            $appointment->update(['is_done' => ! $appointment->is_done]);
        }
    }

    public function toggleChallenge(int $id): void
    {
        $challenge = Challenge::where('user_id', Auth::id())->find($id);
        if (! $challenge) {
            return;
        }
        $today = Carbon::today()->toDateString();
        $challenge->isDoneOn($today)
            ? $challenge->logs()->whereDate('date', $today)->delete()
            : $challenge->logs()->create(['date' => $today]);
    }

    public function render(): View
    {
        $user = Auth::user();
        $today = Carbon::today();

        // Today's tasks
        $tasks = Task::query()->ownedBy($user)
            ->whereHas('day', fn ($q) => $q->whereDate('date', $today))
            ->orderByDesc('is_important')
            ->orderByRaw('start_time IS NULL')->orderBy('start_time')->get();

        // Timeline: timed tasks + appointments + job interviews today
        $timeline = collect();
        foreach ($tasks->whereNotNull('start_time') as $t) {
            $timeline->push([
                'sort' => $t->start_time, 'time' => $t->startLabel(),
                'emoji' => $t->kind->emoji(), 'title' => $t->title, 'kind' => $t->kind->label(),
                'toggle' => 'toggleTask', 'id' => $t->id, 'done' => $t->isDone(),
                'important' => $t->is_important, 'url' => route('tasks.show', $t),
            ]);
        }
        foreach (Appointment::query()->where('user_id', $user->id)->whereDate('date', $today)->get() as $a) {
            $timeline->push([
                'sort' => $a->time ?? '23:58', 'time' => $a->timeLabel(),
                'emoji' => $a->type->emoji(), 'title' => $a->title, 'kind' => $a->type->label(),
                'toggle' => 'toggleAppointment', 'id' => $a->id, 'done' => $a->is_done,
                'url' => route('appointments'),
            ]);
        }
        foreach (JobApplication::query()->where('user_id', $user->id)->whereDate('interview_at', $today)->get() as $j) {
            $timeline->push([
                'sort' => '23:59', 'time' => null,
                'emoji' => '🎙️', 'title' => 'انترفيو: '.$j->position.' — '.$j->company, 'kind' => 'انترفيو',
                'toggle' => null, 'id' => null, 'done' => null,
                'url' => route('jobs.show', $j),
            ]);
        }
        $timeline = $timeline->sortBy('sort')->values();

        // Untimed tasks
        $untimedTasks = $tasks->whereNull('start_time')->values();

        // Habits due today
        $habits = Habit::query()->ownedBy($user)->active()
            ->with(['logs' => fn ($q) => $q->whereDate('date', $today)])
            ->orderBy('position')->get()
            ->filter(fn (Habit $h) => $h->isActiveOn($today))->values();

        // Active challenges
        $challenges = Challenge::query()->where('user_id', $user->id)
            ->where('status', ChallengeStatus::Active)
            ->with(['logs' => fn ($q) => $q->whereDate('date', $today)])->get();

        // Spiritual
        $prayerDone = PrayerDay::query()->where('user_id', $user->id)->whereDate('date', $today)->first()?->doneCount() ?? 0;
        $quranDone = QuranWirdDay::query()->where('user_id', $user->id)->whereDate('date', $today)->exists();
        $hasTopics = RecoveryTopic::query()->where('user_id', $user->id)->exists();
        $nutritionDone = $hasTopics && MentalNutritionLog::query()->where('user_id', $user->id)->whereDate('date', $today)->exists();

        // Day progress across all actionable items
        $doneCount = $tasks->where('progress', 100)->count()
            + $habits->filter(fn (Habit $h) => $h->logs->isNotEmpty())->count()
            + $challenges->filter(fn (Challenge $c) => $c->logs->isNotEmpty())->count()
            + ($prayerDone >= 5 ? 1 : 0) + ($quranDone ? 1 : 0) + ($nutritionDone ? 1 : 0);
        $totalCount = $tasks->count() + $habits->count() + $challenges->count() + 2 + ($hasTopics ? 1 : 0);
        $percent = $totalCount > 0 ? (int) round($doneCount / $totalCount * 100) : 0;

        $hour = (int) now()->format('H');

        return view('livewire.simply.index', [
            'greeting' => $hour < 12 ? 'صباح الخير' : ($hour < 17 ? 'نهارك سعيد' : 'مساء الخير'),
            'dateLabel' => $today->translatedFormat('l، j F Y'),
            'line' => self::lines()[$today->dayOfYear % count(self::lines())],
            'percent' => $percent,
            'doneCount' => $doneCount,
            'totalCount' => $totalCount,
            'timeline' => $timeline,
            'untimedTasks' => $untimedTasks,
            'habits' => $habits,
            'challenges' => $challenges,
            'today' => $today->toDateString(),
            'prayerDone' => $prayerDone,
            'quranDone' => $quranDone,
            'nutritionDone' => $nutritionDone,
            'hasTopics' => $hasTopics,
            'recoveries' => Recovery::query()->ownedBy($user)->active()->get()->map(function (Recovery $r) use ($today) {
                $log = RecoveryLog::where('recovery_id', $r->id)->whereDate('date', $today->copy()->subDay())->first();

                return ['id' => $r->id, 'title' => $r->title, 'state' => $log ? ($log->is_setback ? 'setback' : 'clean') : null];
            })->all(),
            'yesterdayLabel' => $today->copy()->subDay()->translatedFormat('l، j M'),
            'nightStatus' => NightCheck::where('user_id', $user->id)->whereDate('date', $today)->value('status'),
        ]);
    }

    /** @return array<int, string> */
    private static function lines(): array
    {
        return [
            'خطوة صغيرة النهاردة أحسن من خطة كبيرة بكرة.',
            'ركّز على اللى قدّامك دلوقتي، وسيب الباقي.',
            'مش لازم تعمل كل حاجة، المهم تعمل أهم حاجة.',
            'الاتّساق أهم من الكمال.',
            'يوم بيوم، بترجع لنفسك.',
            'اهدأ… النهاردة كفايته.',
        ];
    }
}
