<?php

namespace App\Services;

use App\Enums\ChallengeStatus;
use App\Models\Activity;
use App\Models\Appointment;
use App\Models\Challenge;
use App\Models\Habit;
use App\Models\JobApplication;
use App\Models\MentalNutritionLog;
use App\Models\PrayerDay;
use App\Models\QuranWirdDay;
use App\Models\RecoveryTopic;
use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * A comprehensive read-only view of everything that concerns the user *today*
 * across modules (habits, challenges, worship, recovery, career, appointments),
 * so today's plan shows "what do I have today" beyond the planner tasks.
 *
 * Each item: emoji, label, done (bool|null — null = informational event), url.
 */
class TodayAgendaService
{
    public function __construct(private readonly User $user)
    {
    }

    public static function for(User $user): self
    {
        return new self($user);
    }

    /** @return array<int, array{title:string, items:array<int, array{emoji:string,label:string,done:?bool,url:string}>}> */
    public function groups(): array
    {
        $today = Carbon::today();
        $groups = [];

        // Habits due today
        $habitItems = [];
        $habits = Habit::query()->ownedBy($this->user)->active()
            ->with(['logs' => fn ($q) => $q->whereDate('date', $today)])
            ->orderBy('position')->get();
        foreach ($habits as $habit) {
            if (! $habit->isActiveOn($today)) {
                continue;
            }
            $habitItems[] = [
                'emoji' => '🔁',
                'label' => $habit->title,
                'done' => $habit->logs->isNotEmpty(),
                'url' => route('habits.show', $habit),
            ];
        }
        if ($habitItems) {
            $groups[] = ['title' => 'العادات', 'items' => $habitItems];
        }

        // Active challenges (check-in today)
        $challengeItems = [];
        $challenges = Challenge::query()->where('user_id', $this->user->id)
            ->where('status', ChallengeStatus::Active)
            ->with(['logs' => fn ($q) => $q->whereDate('date', $today)])->get();
        foreach ($challenges as $challenge) {
            $challengeItems[] = [
                'emoji' => '🔥',
                'label' => $challenge->title,
                'done' => $challenge->logs->isNotEmpty(),
                'url' => route('challenges.show', $challenge),
            ];
        }
        if ($challengeItems) {
            $groups[] = ['title' => 'التحديات', 'items' => $challengeItems];
        }

        // Religion (prayers + Quran wird)
        $religionItems = [];
        $prayerDay = PrayerDay::query()->where('user_id', $this->user->id)->whereDate('date', $today)->first();
        $prayed = $prayerDay?->doneCount() ?? 0;
        $religionItems[] = [
            'emoji' => '🕌',
            'label' => 'الصلوات ('.$prayed.'/5)',
            'done' => $prayed >= 5,
            'url' => route('religion.prayers'),
        ];
        $religionItems[] = [
            'emoji' => '📖',
            'label' => 'ورد القرآن',
            'done' => QuranWirdDay::query()->where('user_id', $this->user->id)->whereDate('date', $today)->exists(),
            'url' => route('religion.quran'),
        ];
        $groups[] = ['title' => 'الدين', 'items' => $religionItems];

        // Recovery (mental nutrition — only if the user has topics)
        if (RecoveryTopic::query()->where('user_id', $this->user->id)->exists()) {
            $groups[] = ['title' => 'التعافي', 'items' => [[
                'emoji' => '🧠',
                'label' => 'التغذية الذهنية',
                'done' => MentalNutritionLog::query()->where('user_id', $this->user->id)->whereDate('date', $today)->exists(),
                'url' => route('recovery.nutrition'),
            ]]];
        }

        // Career events happening today (activities + interviews)
        $careerItems = [];
        $activities = Activity::query()->ownedBy($this->user)
            ->whereNotIn('stage', ['done', 'rejected'])
            ->where(fn ($q) => $q->whereNotNull('start_date')->orWhereNotNull('end_date'))
            ->get();
        foreach ($activities as $act) {
            $start = $act->start_date;
            $end = $act->end_date;
            $onToday = ($start && $start->isSameDay($today))
                || ($end && $end->isSameDay($today))
                || ($start && $end && $today->between($start, $end));
            if ($onToday) {
                $careerItems[] = ['emoji' => $act->type->emoji(), 'label' => $act->title, 'done' => null, 'url' => route('activities.show', $act)];
            }
        }
        foreach (JobApplication::query()->where('user_id', $this->user->id)
            ->whereDate('interview_at', $today)->get() as $job) {
            $careerItems[] = ['emoji' => '🎙️', 'label' => 'انترفيو: '.$job->position.' — '.$job->company, 'done' => null, 'url' => route('jobs.show', $job)];
        }
        if ($careerItems) {
            $groups[] = ['title' => 'الكارير', 'items' => $careerItems];
        }

        // Appointments today
        $appointmentItems = [];
        foreach (Appointment::query()->where('user_id', $this->user->id)
            ->whereDate('date', $today)->orderBy('time')->get() as $ap) {
            $appointmentItems[] = [
                'emoji' => $ap->type->emoji(),
                'label' => $ap->title.($ap->timeLabel() ? ' ('.$ap->timeLabel().')' : ''),
                'done' => null,
                'url' => route('appointments'),
            ];
        }
        if ($appointmentItems) {
            $groups[] = ['title' => 'المواعيد', 'items' => $appointmentItems];
        }

        return $groups;
    }
}
