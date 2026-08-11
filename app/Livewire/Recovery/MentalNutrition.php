<?php

namespace App\Livewire\Recovery;

use App\Models\MentalNutritionLog;
use App\Models\RecoveryTopic;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class MentalNutrition extends Component
{
    public ?string $reflection = null;

    /** Optional manual override of the suggested topic ("موضوع تاني"). */
    public ?int $suggestedTopicId = null;

    /** Pick a different random topic than the current suggestion. */
    public function shuffleSuggestion(): void
    {
        $ids = RecoveryTopic::ownedBy(Auth::user())->pluck('id');
        $others = $ids->reject(fn ($id) => $id === $this->suggestedTopicId);

        $this->suggestedTopicId = ($others->isNotEmpty() ? $others : $ids)->random();
    }

    /** Mark today's topic as consumed (read + reflected on). */
    public function markConsumed(int $topicId): void
    {
        $topic = RecoveryTopic::ownedBy(Auth::user())->findOrFail($topicId);

        MentalNutritionLog::updateOrCreate(
            ['user_id' => Auth::id(), 'date' => Carbon::today()->toDateString()],
            ['recovery_topic_id' => $topic->id, 'reflection' => $this->reflection],
        );

        $this->reflection = null;
    }

    /**
     * The topic to read today: an explicit override, else the least-recently
     * read topic (never-read first), breaking ties by highest importance.
     */
    private function suggestTopic(Collection $topics): ?RecoveryTopic
    {
        if ($this->suggestedTopicId) {
            $override = $topics->firstWhere('id', $this->suggestedTopicId);
            if ($override) {
                return $override;
            }
        }

        $lastRead = MentalNutritionLog::query()
            ->where('user_id', Auth::id())
            ->whereNotNull('recovery_topic_id')
            ->selectRaw('recovery_topic_id, MAX(date) as last_date')
            ->groupBy('recovery_topic_id')
            ->pluck('last_date', 'recovery_topic_id');

        return $topics
            ->sortBy(fn (RecoveryTopic $t) => [
                $lastRead[$t->id] ?? '0000-00-00',
                -$t->importance->weight(),
            ])
            ->first();
    }

    /** Current consecutive-day streak of mental-nutrition, ending today or yesterday. */
    private function currentStreak(): int
    {
        $dates = MentalNutritionLog::query()
            ->where('user_id', Auth::id())
            ->orderByDesc('date')
            ->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->unique()
            ->values();

        if ($dates->isEmpty()) {
            return 0;
        }

        $today = Carbon::today();
        // Anchor to today if done, otherwise to yesterday (streak not yet broken).
        $cursor = $dates->contains($today->toDateString())
            ? $today->copy()
            : $today->copy()->subDay();

        $streak = 0;
        while ($dates->contains($cursor->toDateString())) {
            $streak++;
            $cursor->subDay();
        }

        return $streak;
    }

    public function render(): View
    {
        $topics = RecoveryTopic::ownedBy(Auth::user())->get();

        $todayLog = MentalNutritionLog::query()
            ->where('user_id', Auth::id())
            ->whereDate('date', Carbon::today())
            ->with('topic')
            ->first();

        $recent = MentalNutritionLog::query()
            ->where('user_id', Auth::id())
            ->with('topic')
            ->orderByDesc('date')
            ->limit(7)
            ->get();

        return view('livewire.recovery.mental-nutrition', [
            'hasTopics' => $topics->isNotEmpty(),
            'todayLog' => $todayLog,
            'suggested' => $todayLog?->topic ?? $this->suggestTopic($topics),
            'streak' => $this->currentStreak(),
            'recent' => $recent,
        ]);
    }
}
