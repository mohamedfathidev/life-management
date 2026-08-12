<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\Appointment;
use App\Models\CareerSetting;
use App\Models\Challenge;
use App\Models\ChallengeLog;
use App\Models\ComfortExperience;
use App\Models\Cv;
use App\Models\DailyLog;
use App\Models\Day;
use App\Models\DayBreak;
use App\Models\DiaryEntry;
use App\Models\Donation;
use App\Models\Duaa;
use App\Models\Goal;
use App\Models\GoalReview;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\JobApplication;
use App\Models\JobPrepItem;
use App\Models\MarketingPost;
use App\Models\MentalNutritionLog;
use App\Models\PrayerDay;
use App\Models\QuranLog;
use App\Models\Recovery;
use App\Models\RecoveryLog;
use App\Models\RecoveryTopic;
use App\Models\Scholarship;
use App\Models\ScholarshipTopic;
use App\Models\StudyTrack;
use App\Models\Task;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VolunteerActivity;
use App\Models\Week;
use Illuminate\Support\Carbon;

/**
 * Builds a full JSON snapshot of a user's data across every module — a
 * personal backup. Encrypted fields are decrypted in the output (owner's copy).
 */
class DataExportService
{
    public function __construct(private readonly User $user)
    {
    }

    public static function for(User $user): self
    {
        return new self($user);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $id = $this->user->id;

        $dayIds = Day::where('user_id', $id)->pluck('id');
        $habitIds = Habit::where('user_id', $id)->pluck('id');
        $challengeIds = Challenge::where('user_id', $id)->pluck('id');
        $recoveryIds = Recovery::where('user_id', $id)->pluck('id');
        $jobIds = JobApplication::where('user_id', $id)->pluck('id');

        return [
            'app' => 'سيبها على الله (فانية)',
            'version' => 1,
            'exported_at' => Carbon::now()->toIso8601String(),
            'user' => [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'locale' => $this->user->locale,
                'theme' => $this->user->theme?->value,
                'created_at' => $this->user->created_at?->toIso8601String(),
            ],

            // Goals & planner
            'goals' => $this->owned(Goal::class),
            'goal_reviews' => $this->owned(GoalReview::class),
            'weeks' => $this->owned(Week::class),
            'days' => $this->owned(Day::class),
            'day_breaks' => DayBreak::whereIn('day_id', $dayIds)->get()->toArray(),
            'tasks' => $this->owned(Task::class),
            'daily_logs' => $this->owned(DailyLog::class),
            'appointments' => $this->owned(Appointment::class),

            // Self-development
            'habits' => $this->owned(Habit::class),
            'habit_logs' => HabitLog::whereIn('habit_id', $habitIds)->get()->toArray(),
            'challenges' => $this->owned(Challenge::class),
            'challenge_logs' => ChallengeLog::whereIn('challenge_id', $challengeIds)->get()->toArray(),
            'recoveries' => $this->owned(Recovery::class),
            'recovery_logs' => RecoveryLog::whereIn('recovery_id', $recoveryIds)->get()->toArray(),
            'recovery_topics' => $this->owned(RecoveryTopic::class),
            'mental_nutrition_logs' => $this->owned(MentalNutritionLog::class),
            'comfort_experiences' => $this->owned(ComfortExperience::class),
            'diary_entries' => $this->owned(DiaryEntry::class),

            // Career
            'scholarships' => $this->owned(Scholarship::class),
            'scholarship_topics' => $this->owned(ScholarshipTopic::class),
            'volunteer_activities' => $this->owned(VolunteerActivity::class),
            'job_applications' => $this->owned(JobApplication::class),
            'job_prep_items' => JobPrepItem::whereIn('job_application_id', $jobIds)->get()->toArray(),
            'study_tracks' => $this->owned(StudyTrack::class),
            'career_settings' => CareerSetting::where('user_id', $id)->get()->toArray(),
            'marketing_posts' => $this->owned(MarketingPost::class),
            'cvs' => Cv::where('user_id', $id)->get(['id', 'title', 'target', 'original_name', 'size', 'created_at'])->toArray(),

            // Religion
            'prayer_days' => $this->owned(PrayerDay::class),
            'quran_logs' => $this->owned(QuranLog::class),
            'donations' => $this->owned(Donation::class),
            'duaas' => $this->owned(Duaa::class),

            // Finance & gamification
            'transactions' => $this->owned(Transaction::class),
            'achievements' => Achievement::where('user_id', $id)->get()->toArray(),
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @return array<int, mixed>
     */
    private function owned(string $model): array
    {
        return $model::query()->where('user_id', $this->user->id)->get()->toArray();
    }
}
