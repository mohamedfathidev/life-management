<?php

namespace App\Livewire\Planner;

use App\Models\Day;
use App\Models\DiaryEntry;
use App\Models\HabitLog;
use App\Models\PrayerDay;
use App\Models\QuranWirdDay;
use App\Models\RecoveryLog;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class DayOverview extends Component
{
    #[Url]
    public string $date = '';

    public function mount(?string $date = null): void
    {
        if ($date) {
            $this->date = $date;
        } elseif (empty($this->date)) {
            $this->date = Carbon::today()->toDateString();
        }
    }

    public function previousDay(): void
    {
        $this->date = Carbon::parse($this->date)->subDay()->toDateString();
    }

    public function nextDay(): void
    {
        $this->date = Carbon::parse($this->date)->addDay()->toDateString();
    }

    public function setToday(): void
    {
        $this->date = Carbon::today()->toDateString();
    }

    public function render(): View
    {
        $user = Auth::user();
        $targetDate = Carbon::parse($this->date);

        // Fetch Day model for closure info, mood/rating & time tracking
        $day = Day::ownedBy($user)->whereDate('date', $targetDate)->first();

        // Tasks for this date
        $tasks = $day
            ? $day->tasks
            : Task::query()
                ->ownedBy($user)
                ->whereHas('day', fn ($q) => $q->whereDate('date', $targetDate))
                ->orderBy('position')
                ->get();

        $completedTasksCount = $tasks->filter(fn ($t) => $t->isDone())->count();
        $totalTasksCount = $tasks->count();
        $completionPercent = $day ? $day->completionPercent() : ($totalTasksCount > 0 ? (int) round(($completedTasksCount / $totalTasksCount) * 100) : 0);

        // Recovery Log (Setback / Clean day + Night Routine)
        $recoveryLog = RecoveryLog::query()
            ->whereHas('recovery', fn ($q) => $q->where('user_id', $user->id))
            ->whereDate('date', $targetDate)
            ->latest()
            ->first();

        // Worship & Habits
        $prayerDay = PrayerDay::ownedBy($user)->whereDate('date', $targetDate)->first();
        $quranWirdDone = QuranWirdDay::ownedBy($user)->whereDate('date', $targetDate)->exists();
        $habitsLogged = HabitLog::query()
            ->whereHas('habit', fn ($q) => $q->where('user_id', $user->id))
            ->whereDate('date', $targetDate)
            ->with('habit')
            ->get();

        // Memoir / Diary Entry
        $diaryEntry = DiaryEntry::ownedBy($user)->whereDate('date', $targetDate)->first();

        // Smart Pattern Correlation Analysis
        $patternInsight = $this->generatePatternInsight(
            $recoveryLog,
            $completionPercent,
            $quranWirdDone,
            $prayerDay,
            $day
        );

        return view('livewire.planner.day-overview', [
            'targetDate' => $targetDate,
            'day' => $day,
            'tasks' => $tasks,
            'completedTasksCount' => $completedTasksCount,
            'totalTasksCount' => $totalTasksCount,
            'completionPercent' => $completionPercent,
            'recoveryLog' => $recoveryLog,
            'prayerDay' => $prayerDay,
            'quranWirdDone' => $quranWirdDone,
            'habitsLogged' => $habitsLogged,
            'diaryEntry' => $diaryEntry,
            'patternInsight' => $patternInsight,
        ]);
    }

    /**
     * Generate smart correlation narrative connecting sleep/night routine, relapse, tasks, and mood.
     *
     * @return array{type: string, title: string, text: string, bgClass: string, textClass: string, borderClass: string}
     */
    private function generatePatternInsight(
        ?RecoveryLog $recoveryLog,
        int $completionPercent,
        bool $quranWirdDone,
        ?PrayerDay $prayerDay,
        ?Day $day
    ): array {
        if ($recoveryLog && $recoveryLog->is_setback) {
            $factors = [];

            if ($recoveryLog->stayed_up_late) {
                $factors[] = 'السهر ليلة اليوم 🌙';
            }
            if ($recoveryLog->had_dinner === false) {
                $factors[] = 'عدم التغذية 🚫';
            }
            if ($recoveryLog->prepared_for_sleep === false) {
                $factors[] = 'عدم الاستعداد للنوم 🛏️';
            }
            if ($completionPercent < 60) {
                $factors[] = 'انخفاض نسبة إنجاز التاسكات ('.$completionPercent.'%)';
            }

            $factorsText = ! empty($factors) ? implode('، و', $factors) : 'الضغوطات اليومية';

            return [
                'type' => 'setback',
                'title' => 'تحليل نمط الانتكاسة لهذا اليوم',
                'text' => 'لوحظ حدوث انتكاسة في هذا اليوم بالتزامن مع: '.$factorsText.'. تؤكد هذه القراءة أن ليلة اليوم والنوم المبكر هما الخط الدفاعي الأول للحفاظ على تعافيك وإنجاز مهامك.',
                'bgClass' => 'bg-rose-500/10 dark:bg-rose-950/30',
                'textClass' => 'text-rose-700 dark:text-rose-300',
                'borderClass' => 'border-rose-500/30',
            ];
        }

        if ($recoveryLog && ! $recoveryLog->is_setback && $completionPercent >= 70 && $quranWirdDone) {
            return [
                'type' => 'excellent',
                'title' => 'قصة يوم مثالي ومبارك 🌟',
                'text' => 'يوم نظيف ونموذجي! ارتبط الالتزام بورد القرآن والنوم المنظم بإنجاز كفؤ في المهام بنسبة '.$completionPercent.'%. استمر على هذا النمط المشرق!',
                'bgClass' => 'bg-emerald-500/10 dark:bg-emerald-950/30',
                'textClass' => 'text-emerald-700 dark:text-emerald-300',
                'borderClass' => 'border-emerald-500/30',
            ];
        }

        if ($completionPercent >= 80) {
            return [
                'type' => 'productive',
                'title' => 'يوم إنتاجي عالي الهمة 🚀',
                'text' => 'نجحت في تحقيق نسبة إنجاز ممتازة بالمهام ('.$completionPercent.'%). الحفاظ على التوازن بين العمل والراحة يضمن لك الاستمرارية.',
                'bgClass' => 'bg-teal-500/10 dark:bg-teal-950/30',
                'textClass' => 'text-teal-700 dark:text-teal-300',
                'borderClass' => 'border-teal-500/30',
            ];
        }

        return [
            'type' => 'normal',
            'title' => 'ملخص توازن اليوم ⚖️',
            'text' => 'استعراض متكامل يربط بين نومك، عباداتك، وتاسكاتك. تذكر دائماً أن الانضباط في التفاصيل الصغرية يصنع الفرق الكبير في التعافي والإنتاجية.',
            'bgClass' => 'bg-primary/10 dark:bg-primary-dark/20',
            'textClass' => 'text-primary dark:text-primary-dark',
            'borderClass' => 'border-primary/20 dark:border-primary-dark/30',
        ];
    }
}
