<?php

namespace App\Support;

use App\Enums\ChallengeStatus;
use App\Enums\ExperienceStatus;
use App\Enums\GoalStatus;
use App\Models\Challenge;
use App\Models\ComfortExperience;
use App\Models\Day;
use App\Models\DiaryEntry;
use App\Models\Donation;
use App\Models\Goal;
use App\Models\Habit;
use App\Models\PrayerDay;
use App\Models\QuranLog;
use App\Models\Recovery;
use App\Models\Task;
use App\Models\User;
use App\Models\VolunteerActivity;

/**
 * Registry of achievement definitions. Definitions live in code; unlocked
 * state lives in the `achievements` table. Each has a target and a callable
 * returning the user's current progress (earned when current >= target).
 */
class Achievements
{
    /**
     * @return array<int, array{key:string, title:string, desc:string, emoji:string, group:string, target:int, current:callable}>
     */
    public static function all(): array
    {
        return [
            // Goals
            self::def('first_goal', 'أول هدف', 'أكملت أول هدف كبير', '🎯', 'الأهداف', 1,
                fn (User $u) => Goal::query()->ownedBy($u)->where('status', GoalStatus::Completed)->count()),
            self::def('goals_5', 'صانع أهداف', 'أكملت ٥ أهداف', '🏆', 'الأهداف', 5,
                fn (User $u) => Goal::query()->ownedBy($u)->where('status', GoalStatus::Completed)->count()),

            // Prayers
            self::def('prayers_ontime_30', 'محافظ', 'صلّيت ٣٠ صلاة في وقتها', '🕌', 'الصلاة', 30,
                fn (User $u) => (int) PrayerDay::query()->ownedBy($u)->get()->sum(fn (PrayerDay $d) => $d->onTimeCount())),
            self::def('prayers_full_7', 'أسبوع كامل', '٧ أيام صلّيت فيها الخمس', '🌟', 'الصلاة', 7,
                fn (User $u) => PrayerDay::query()->ownedBy($u)->get()->filter(fn (PrayerDay $d) => $d->isComplete())->count()),

            // Quran
            self::def('quran_khatmah', 'ختمة', 'أتممت ختمة كاملة (٦٠٤ صفحة)', '📖', 'القرآن', 1,
                fn (User $u) => intdiv((int) QuranLog::query()->ownedBy($u)->sum('pages'), QuranLog::MUSHAF_PAGES)),

            // Recovery
            self::def('recovery_100', 'صمود', 'وصلت ١٠٠ يوم نظيف متواصل', '💎', 'التعافي', 100,
                fn (User $u) => (int) Recovery::query()->ownedBy($u)->get()->map(fn (Recovery $r) => $r->bestStreakDays())->max() ?: 0),

            // Habits & challenges
            self::def('habit_streak_21', 'عادة راسخة', '٢١ يوم متتالي في عادة', '🔁', 'العادات', 21,
                fn (User $u) => (int) Habit::query()->ownedBy($u)->get()->map(fn (Habit $h) => $h->currentStreak())->max() ?: 0),
            self::def('challenge_done', 'منجِز تحدٍّ', 'أكملت تحدّيًا كاملًا', '🔥', 'التحديات', 1,
                fn (User $u) => Challenge::query()->ownedBy($u)->where('status', ChallengeStatus::Completed)->count()),

            // Planner
            self::def('tasks_100', 'منتِج', 'أنجزت ١٠٠ تاسك', '✅', 'المخطط', 100,
                fn (User $u) => Task::query()->where('user_id', $u->id)->where('progress', 100)->count()),
            self::def('days_closed_7', 'منضبط', 'قفلت ٧ أيام بالمخطط', '📋', 'المخطط', 7,
                fn (User $u) => Day::query()->ownedBy($u)->where('status', \App\Enums\DayStatus::Closed)->count()),

            // Giving & growth
            self::def('donation_first', 'يد بتعطي', 'سجّلت أول صدقة', '🤲', 'العطاء', 1,
                fn (User $u) => Donation::query()->ownedBy($u)->count()),
            self::def('volunteer_first', 'متطوّع', 'سجّلت أول نشاط تطوّعي', '🤝', 'العطاء', 1,
                fn (User $u) => VolunteerActivity::query()->ownedBy($u)->count()),
            self::def('comfort_5', 'جريء', 'خرجت من زون الأمان ٥ مرات', '🚀', 'التطوير', 5,
                fn (User $u) => ComfortExperience::query()->ownedBy($u)->where('status', ExperienceStatus::Done)->count()),
            self::def('diary_10', 'كاتب', 'كتبت ١٠ مذكرات', '📝', 'التطوير', 10,
                fn (User $u) => DiaryEntry::query()->ownedBy($u)->count()),
        ];
    }

    private static function def(string $key, string $title, string $desc, string $emoji, string $group, int $target, callable $current): array
    {
        return compact('key', 'title', 'desc', 'emoji', 'group', 'target', 'current');
    }
}
