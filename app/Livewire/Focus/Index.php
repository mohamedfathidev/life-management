<?php

namespace App\Livewire\Focus;

use App\Enums\ChallengeStatus;
use App\Models\Challenge;
use App\Models\Habit;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Focus mode: pick one of today's items (task / habit / challenge) and run a
 * distraction-free stopwatch, saving the time spent on it.
 */
#[Layout('layouts.app')]
class Index extends Component
{
    /** @var array<string, class-string<Model>> */
    private const MODELS = [
        'task' => Task::class,
        'habit' => Habit::class,
        'challenge' => Challenge::class,
    ];

    #[Url]
    public ?string $focusType = null;

    #[Url]
    public ?int $focusId = null;

    public function select(string $type, int $id): void
    {
        if (! isset(self::MODELS[$type])) {
            return;
        }

        $item = $this->resolve($type, $id);
        if (! $item) {
            return;
        }

        $this->focusType = $type;
        $this->focusId = $id;
    }

    public function clearSelection(): void
    {
        $this->focusType = null;
        $this->focusId = null;
    }

    /** Persist a completed stopwatch sitting (seconds) for the selected item. */
    public function saveFocus(int $seconds): void
    {
        if ($seconds < 1 || ! $this->focusType || ! $this->focusId) {
            return;
        }

        $item = $this->resolve($this->focusType, $this->focusId);
        if (! $item) {
            return;
        }

        $item->focusSessions()->create([
            'user_id' => Auth::id(),
            'date' => Carbon::today()->toDateString(),
            'seconds' => min($seconds, 86400),
        ]);
    }

    /** Mark the selected item as done for today. */
    public function markDone(): void
    {
        $item = $this->focusType && $this->focusId ? $this->resolve($this->focusType, $this->focusId) : null;
        if (! $item) {
            return;
        }

        $today = Carbon::today()->toDateString();

        match ($this->focusType) {
            'task' => tap($item, fn (Task $t) => $t->setProgress(100))->save(),
            'habit' => $item->isDoneOn($today) ? null : $item->logs()->create(['date' => $today]),
            'challenge' => $item->isDoneOn($today) ? null : $item->logs()->create(['date' => $today]),
            default => null,
        };
    }

    /** Resolve an owned model of the given focus type. */
    private function resolve(string $type, int $id): ?Model
    {
        $model = self::MODELS[$type] ?? null;

        return $model ? $model::query()->where('user_id', Auth::id())->find($id) : null;
    }

    private function secondsLabel(int $seconds): ?string
    {
        if ($seconds <= 0) {
            return null;
        }

        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        if (! $hours && ! $minutes) {
            return $seconds.' ث';
        }

        $parts = [];
        if ($hours) {
            $parts[] = $hours.' س';
        }
        if ($minutes) {
            $parts[] = $minutes.' د';
        }

        return implode(' ', $parts);
    }

    public function render(): View
    {
        $user = Auth::user();
        $today = Carbon::today();

        // Selected item (focus screen)
        $selected = null;
        if ($this->focusType && $this->focusId) {
            $item = $this->resolve($this->focusType, $this->focusId);
            if ($item) {
                $secs = $item->focusSecondsOn($today);
                $selected = [
                    'type' => $this->focusType,
                    'id' => $item->id,
                    'title' => $item->title,
                    'emoji' => $this->emojiFor($this->focusType, $item),
                    'seconds' => $secs,
                    'secondsLabel' => $this->secondsLabel($secs),
                    'done' => $this->isDone($this->focusType, $item, $today),
                ];
            } else {
                $this->clearSelection();
            }
        }

        return view('livewire.focus.index', [
            'selected' => $selected,
            'groups' => $selected ? [] : $this->todayGroups($user, $today),
        ]);
    }

    private function emojiFor(string $type, Model $item): string
    {
        return match ($type) {
            'task' => $item->kind->emoji(),
            'habit' => '🔁',
            'challenge' => '🔥',
            default => '🎯',
        };
    }

    private function isDone(string $type, Model $item, Carbon $today): bool
    {
        return match ($type) {
            'task' => $item->isDone(),
            'habit', 'challenge' => $item->isDoneOn($today),
            default => false,
        };
    }

    /** @return array<int, array{title:string, items:array<int, array>}> */
    private function todayGroups(\App\Models\User $user, Carbon $today): array
    {
        $groups = [];

        // Today's pending planner tasks
        $tasks = Task::query()->ownedBy($user)->incomplete()
            ->whereHas('day', fn ($q) => $q->whereDate('date', $today))
            ->orderByRaw('start_time IS NULL')->orderBy('start_time')->get();
        $taskItems = $tasks->map(fn (Task $t) => $this->itemArray('task', $t, $t->kind->emoji(), $today))->all();
        if ($taskItems) {
            $groups[] = ['title' => 'تاسكات النهاردة', 'items' => $taskItems];
        }

        // Habits due today
        $habits = Habit::query()->ownedBy($user)->active()
            ->with(['logs' => fn ($q) => $q->whereDate('date', $today)])
            ->orderBy('position')->get()
            ->filter(fn (Habit $h) => $h->isActiveOn($today));
        $habitItems = $habits->map(fn (Habit $h) => $this->itemArray('habit', $h, '🔁', $today))->values()->all();
        if ($habitItems) {
            $groups[] = ['title' => 'العادات', 'items' => $habitItems];
        }

        // Active challenges
        $challenges = Challenge::query()->where('user_id', $user->id)
            ->where('status', ChallengeStatus::Active)
            ->with(['logs' => fn ($q) => $q->whereDate('date', $today)])->get();
        $challengeItems = $challenges->map(fn (Challenge $c) => $this->itemArray('challenge', $c, '🔥', $today))->all();
        if ($challengeItems) {
            $groups[] = ['title' => 'التحديات', 'items' => $challengeItems];
        }

        return $groups;
    }

    /** @return array<string, mixed> */
    private function itemArray(string $type, Model $item, string $emoji, Carbon $today): array
    {
        $secs = $item->focusSecondsOn($today);

        return [
            'type' => $type,
            'id' => $item->id,
            'title' => $item->title,
            'emoji' => $emoji,
            'done' => $this->isDone($type, $item, $today),
            'focusLabel' => $this->secondsLabel($secs),
        ];
    }
}
