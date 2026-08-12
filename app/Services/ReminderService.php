<?php

namespace App\Services;

use App\Enums\ChallengeStatus;
use App\Models\Activity;
use App\Models\Appointment;
use App\Models\Challenge;
use App\Models\JobApplication;
use App\Models\Day;
use App\Models\Goal;
use App\Models\Habit;
use App\Models\MentalNutritionLog;
use App\Models\PrayerDay;
use App\Models\QuranLog;
use App\Models\RecoveryTopic;
use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * Computes the user's pending reminders across modules for "right now".
 * Priority: 2 = today/urgent, 1 = soon, 0 = gentle nudge.
 */
class ReminderService
{
    public function __construct(private readonly User $user)
    {
    }

    public static function for(User $user): self
    {
        return new self($user);
    }

    /** @return array<int, array{emoji:string, text:string, url:string, priority:int}> */
    public function reminders(): array
    {
        $today = Carbon::today();
        $items = [];

        // Appointments today / tomorrow
        foreach (Appointment::query()->where('user_id', $this->user->id)
            ->whereDate('date', $today)->orderBy('time')->get() as $ap) {
            $items[] = ['emoji' => $ap->type->emoji(), 'text' => 'موعد النهاردة: '.$ap->title.($ap->timeLabel() ? ' ('.$ap->timeLabel().')' : ''), 'url' => route('appointments'), 'priority' => 2];
        }
        foreach (Appointment::query()->where('user_id', $this->user->id)
            ->whereDate('date', $today->copy()->addDay())->get() as $ap) {
            $items[] = ['emoji' => $ap->type->emoji(), 'text' => 'موعد بكرة: '.$ap->title, 'url' => route('appointments'), 'priority' => 1];
        }

        // Prayers not yet completed today
        $prayerDay = PrayerDay::query()->where('user_id', $this->user->id)->whereDate('date', $today)->first();
        $done = $prayerDay?->doneCount() ?? 0;
        if ($done < 5) {
            $items[] = ['emoji' => '🕌', 'text' => 'صلواتك النهاردة: '.$done.'/5 — علّم اللي فاتك', 'url' => route('religion.prayers'), 'priority' => 1];
        }

        // Today's planner tasks still pending
        $day = Day::query()->where('user_id', $this->user->id)->whereDate('date', $today)->with('tasks')->first();
        $pending = $day ? $day->tasks->where('progress', '<', 100)->count() : 0;
        if ($pending > 0) {
            $items[] = ['emoji' => '✅', 'text' => 'عندك '.$pending.' تاسك لسه في مخطط النهاردة', 'url' => route('planner.day', $today->toDateString()), 'priority' => 1];
        }

        // Habits not done today
        $habits = Habit::query()->where('user_id', $this->user->id)->where('is_archived', false)
            ->with(['logs' => fn ($q) => $q->whereDate('date', $today)])->get();
        $habitsPending = $habits->filter(fn (Habit $h) => $h->logs->isEmpty())->count();
        if ($habitsPending > 0) {
            $items[] = ['emoji' => '🔁', 'text' => $habitsPending.' عادة لسه ماعملتهاش النهاردة', 'url' => route('habits.index'), 'priority' => 0];
        }

        // Active challenges not checked today
        $challenges = Challenge::query()->where('user_id', $this->user->id)->where('status', ChallengeStatus::Active)
            ->with(['logs' => fn ($q) => $q->whereDate('date', $today)])->get();
        $chPending = $challenges->filter(fn (Challenge $c) => $c->logs->isEmpty())->count();
        if ($chPending > 0) {
            $items[] = ['emoji' => '🔥', 'text' => $chPending.' تحدٍّ محتاج تعليم النهاردة', 'url' => route('challenges.index'), 'priority' => 0];
        }

        // Recovery: today's mental nutrition not consumed yet (only if topics exist)
        if (RecoveryTopic::query()->where('user_id', $this->user->id)->exists()) {
            $nutritionDone = MentalNutritionLog::query()->where('user_id', $this->user->id)
                ->whereDate('date', $today)->exists();
            if (! $nutritionDone) {
                $items[] = ['emoji' => '🧠', 'text' => 'لسه ماخدتش تغذيتك الذهنية النهاردة', 'url' => route('recovery.nutrition'), 'priority' => 0];
            }
        }

        // Quran: today's wird not logged yet
        $quranToday = QuranLog::query()->where('user_id', $this->user->id)->whereDate('date', $today)->exists();
        if (! $quranToday) {
            $items[] = ['emoji' => '📖', 'text' => 'لسه ماسجلتش وردك من القرآن النهاردة', 'url' => route('religion.quran'), 'priority' => 0];
        }

        // Goal deadlines within 3 days
        foreach (Goal::query()->where('user_id', $this->user->id)
            ->whereNotNull('target_date')
            ->whereDate('target_date', '>=', $today)
            ->whereDate('target_date', '<=', $today->copy()->addDays(3))
            ->orderBy('target_date')->limit(5)->get() as $goal) {
            $items[] = ['emoji' => '🎯', 'text' => 'قرب ميعاد هدف: '.$goal->title.' ('.$goal->target_date->translatedFormat('j M').')', 'url' => route('goals.show', $goal), 'priority' => 2];
        }

        // Scholarship deadlines within 5 days
        foreach (Scholarship::query()->where('user_id', $this->user->id)
            ->whereNotNull('apply_to')
            ->whereDate('apply_to', '>=', $today)
            ->whereDate('apply_to', '<=', $today->copy()->addDays(5))
            ->whereNotIn('stage', ['accepted', 'rejected'])
            ->orderBy('apply_to')->limit(5)->get() as $sc) {
            $items[] = ['emoji' => '🎓', 'text' => 'قرب آخر موعد منحة: '.$sc->name.' ('.$sc->apply_to->translatedFormat('j M').')', 'url' => route('scholarships.show', $sc), 'priority' => 2];
        }

        // Activities & participations happening today or starting soon (hackathons,
        // competitions, workshops, conferences…). Interviews live in Appointments above.
        $soon = $today->copy()->addDays(3);
        $activities = Activity::query()->where('user_id', $this->user->id)
            ->whereNotIn('stage', ['done', 'rejected'])
            ->where(fn ($q) => $q->whereNotNull('start_date')->orWhereNotNull('end_date'))
            ->orderBy('start_date')
            ->get();

        foreach ($activities as $act) {
            $start = $act->start_date;
            $end = $act->end_date;
            $emoji = $act->type->emoji();
            $url = route('activities.index');

            if ($start && $start->isSameDay($today)) {
                $items[] = ['emoji' => $emoji, 'text' => 'النهاردة بداية: '.$act->title, 'url' => $url, 'priority' => 2];
            } elseif ($end && $end->isSameDay($today)) {
                $items[] = ['emoji' => $emoji, 'text' => 'النهاردة آخر يوم: '.$act->title, 'url' => $url, 'priority' => 2];
            } elseif ($start && $end && $today->between($start, $end)) {
                $items[] = ['emoji' => $emoji, 'text' => 'شغّال دلوقتي: '.$act->title, 'url' => $url, 'priority' => 2];
            } elseif ($start && $start->gt($today) && $start->lte($soon)) {
                $days = (int) $today->diffInDays($start);
                $when = $days === 1 ? 'بكرة' : 'بعد '.$days.' يوم';
                $items[] = ['emoji' => $emoji, 'text' => $when.': '.$act->title.' ('.$start->translatedFormat('j M').')', 'url' => $url, 'priority' => 1];
            }
        }

        // Job interviews today/tomorrow + application deadlines within 3 days
        foreach (JobApplication::query()->where('user_id', $this->user->id)
            ->whereNotNull('interview_at')
            ->whereDate('interview_at', '>=', $today)
            ->whereDate('interview_at', '<=', $today->copy()->addDay())
            ->orderBy('interview_at')->get() as $job) {
            $isToday = $job->interview_at->isSameDay($today);
            $items[] = ['emoji' => '🎙️', 'text' => ($isToday ? 'انترفيو النهاردة' : 'انترفيو بكرة').': '.$job->position.' — '.$job->company, 'url' => route('jobs.show', $job), 'priority' => 2];
        }
        foreach (JobApplication::query()->where('user_id', $this->user->id)
            ->whereNotNull('deadline')
            ->whereDate('deadline', '>=', $today)
            ->whereDate('deadline', '<=', $today->copy()->addDays(3))
            ->whereNotIn('stage', ['offer', 'rejected'])
            ->orderBy('deadline')->get() as $job) {
            $items[] = ['emoji' => '💼', 'text' => 'قرب آخر موعد تقديم: '.$job->position.' — '.$job->company.' ('.$job->deadline->translatedFormat('j M').')', 'url' => route('jobs.show', $job), 'priority' => 2];
        }

        // Highest priority first
        usort($items, fn ($a, $b) => $b['priority'] <=> $a['priority']);

        return $items;
    }
}
