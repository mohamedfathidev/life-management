<?php

namespace App\Livewire\Planner;

use App\Enums\ChallengeStatus;
use App\Models\Challenge;
use App\Models\Day;
use App\Models\Habit;
use App\Models\MentalNutritionLog;
use App\Models\PrayerDay;
use App\Models\QuranWirdDay;
use App\Models\RecoveryTopic;
use App\Services\DayService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class CloseDay extends Component
{
    public ?Day $day = null;

    public bool $open = false;

    public ?int $rating = null;

    public ?int $mood = null;

    public ?string $reflection = null;

    /** taskId => 'carry' | 'pool' */
    public array $decisions = [];

    #[On('close-day')]
    public function openFor(Day $day): void
    {
        $this->authorize('update', $day);
        $this->resetValidation();

        $this->day = $day;
        $this->rating = $day->rating;
        $this->mood = null; // Will be fetched from daily log if exists
        $this->reflection = $day->reflection;

        // Try to load existing mood from daily log
        $existingLog = $day->user->dailyLogs()->whereDate('date', $day->date)->first();
        if ($existingLog) {
            $this->mood = $existingLog->mood;
        }

        // Default every unfinished task to "carry to next day".
        $this->decisions = $this->incompleteTasks()
            ->mapWithKeys(fn ($task) => [$task->id => 'carry'])
            ->all();

        $this->open = true;
    }

    /** @return Collection<int, \App\Models\Task> */
    public function incompleteTasks(): Collection
    {
        if (! $this->day) {
            return collect();
        }

        return $this->day->tasks()->incomplete()->get();
    }

    public function save(DayService $service): void
    {
        $this->authorize('update', $this->day);

        $this->validate([
            'rating' => ['required', 'integer', 'between:1,10'],
            'mood' => ['nullable', 'integer', 'between:1,10'],
            'reflection' => ['nullable', 'string', 'max:5000'],
            'decisions.*' => [Rule::in(['carry', 'pool'])],
        ], attributes: ['rating' => 'التقييم', 'mood' => 'المزاج', 'reflection' => 'انعكاس اليوم']);

        $service->close($this->day, $this->rating, $this->mood, $this->reflection, $this->decisions);

        $this->open = false;
        $this->dispatch('day-updated');
    }

    public function close(): void
    {
        $this->open = false;
    }

    /**
     * What wasn't done on the day being closed — habits / prayers / quran / etc.
     * (Tasks are handled separately in this modal.)
     *
     * @return array<int, string>
     */
    public function missedItems(): array
    {
        if (! $this->day) {
            return [];
        }

        $user = Auth::user();
        $date = $this->day->date;
        $missed = [];

        $habits = Habit::query()->ownedBy($user)->active()
            ->with(['logs' => fn ($q) => $q->whereDate('date', $date)])->get()
            ->filter(fn (Habit $h) => $h->isActiveOn($date));
        foreach ($habits as $habit) {
            if ($habit->logs->isEmpty()) {
                $missed[] = '🔁 عادة: '.$habit->title;
            }
        }

        $challenges = Challenge::query()->where('user_id', $user->id)
            ->where('status', ChallengeStatus::Active)
            ->with(['logs' => fn ($q) => $q->whereDate('date', $date)])->get();
        foreach ($challenges as $challenge) {
            if ($challenge->logs->isEmpty()) {
                $missed[] = '🔥 تحدٍّ: '.$challenge->title;
            }
        }

        $prayerDone = PrayerDay::query()->where('user_id', $user->id)->whereDate('date', $date)->first()?->doneCount() ?? 0;
        if ($prayerDone < 5) {
            $missed[] = '🕌 صلوات ناقصة ('.$prayerDone.'/5)';
        }

        if (! QuranWirdDay::query()->where('user_id', $user->id)->whereDate('date', $date)->exists()) {
            $missed[] = '📖 ورد القرآن';
        }

        if (RecoveryTopic::query()->where('user_id', $user->id)->exists()
            && ! MentalNutritionLog::query()->where('user_id', $user->id)->whereDate('date', $date)->exists()) {
            $missed[] = '🧠 التغذية الذهنية';
        }

        return $missed;
    }

    public function render(): View
    {
        return view('livewire.planner.close-day', [
            'incompleteTasks' => $this->incompleteTasks(),
            'missedItems' => $this->missedItems(),
        ]);
    }
}
