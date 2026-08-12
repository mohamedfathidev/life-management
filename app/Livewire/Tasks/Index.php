<?php

namespace App\Livewire\Tasks;

use App\Enums\TaskKind;
use App\Enums\TaskStatus;
use App\Models\Goal;
use App\Models\Task;
use App\Services\TodayAgendaService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * The unified task hub: every task collected from any module in one place,
 * filterable by scope (today / pool / all), kind, status and goal.
 */
#[Layout('layouts.app')]
class Index extends Component
{
    #[Url]
    public string $scope = 'today'; // today | pool | all

    #[Url]
    public string $kind = ''; // '' = all

    #[Url]
    public string $status = ''; // '' = all

    #[Url]
    public ?int $goalId = null;

    #[On('task-saved')]
    public function refresh(): void
    {
        // The render() re-runs and reflects the latest data.
    }

    public function toggleDone(Task $task): void
    {
        $this->authorize('update', $task);

        $task->setProgress($task->isDone() ? 0 : 100);
        $task->save();

        $this->dispatch('task-saved');
    }

    public function resetFilters(): void
    {
        $this->reset(['kind', 'status', 'goalId']);
        $this->scope = 'all';
    }

    public function render(): View
    {
        $user = Auth::user();
        $today = Carbon::today();

        $query = Task::query()
            ->ownedBy($user)
            ->with(['goal:id,title,parent_id', 'goal.parent:id,title', 'day:id,date'])
            ->orderByRaw('day_id IS NULL')       // dated tasks first, pool last
            ->orderByDesc('id');

        match ($this->scope) {
            'today' => $query->whereHas('day', fn ($q) => $q->whereDate('date', $today)),
            'pool' => $query->whereNull('day_id'),
            default => null,
        };

        if ($this->kind !== '') {
            $query->where('kind', $this->kind);
        }

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        if ($this->goalId) {
            $query->where('goal_id', $this->goalId);
        }

        return view('livewire.tasks.index', [
            'tasks' => $query->get(),
            // Comprehensive "what do I have today" agenda across modules (today tab only).
            'agenda' => $this->scope === 'today' ? TodayAgendaService::for($user)->groups() : [],
            'kinds' => TaskKind::cases(),
            'statuses' => TaskStatus::cases(),
            'goals' => Goal::query()
                ->ownedBy($user)
                ->with('parent:id,title')
                ->orderBy('title')
                ->get(['id', 'title', 'parent_id']),
            'counts' => [
                'today' => Task::ownedBy($user)->whereHas('day', fn ($q) => $q->whereDate('date', $today))->count(),
                'pool' => Task::ownedBy($user)->whereNull('day_id')->count(),
                'all' => Task::ownedBy($user)->count(),
            ],
        ]);
    }
}
