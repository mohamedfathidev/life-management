<?php

namespace App\Livewire\Planner;

use App\Models\Task;
use App\Services\PlannerService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Pool extends Component
{
    /** Target date for reassigning a task, shared input (defaults to today). */
    public string $assignDate = '';

    public function mount(): void
    {
        $this->assignDate = Carbon::today()->toDateString();
    }

    #[On('task-saved')]
    public function refreshPool(): void
    {
        // presence triggers re-render
    }

    /** Move a pooled task onto a real day, honouring any linked goal's window. */
    public function assign(int $taskId, PlannerService $planner): void
    {
        $task = Task::ownedBy(Auth::user())->findOrFail($taskId);
        $this->authorize('update', $task);

        $date = Carbon::parse($this->assignDate);

        if ($task->goal && ! $task->goal->acceptsDate($date)) {
            $this->addError('assignDate', 'لا يمكن نقل التاسك: '.$task->goal->rangeMessage($date));

            return;
        }

        $day = $planner->resolveDay(Auth::user(), $date);
        $task->day_id = $day->id;
        $task->save();

        $this->dispatch('task-saved');
    }

    public function render(): View
    {
        $tasks = Task::query()
            ->ownedBy(Auth::user())
            ->inPool()
            ->with('goal:id,title,start_date,target_date')
            ->latest()
            ->get();

        return view('livewire.planner.pool', [
            'tasks' => $tasks,
        ]);
    }
}
