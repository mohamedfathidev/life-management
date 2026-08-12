<?php

namespace App\Livewire\Planner;

use App\Models\Day;
use App\Models\DayBreak;
use App\Models\Task;
use App\Services\DayService;
use App\Services\PlannerService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class DayShow extends Component
{
    public Day $day;

    /** Bound to the start-time input (HH:MM) so the user sets their own start. */
    public string $startTimeInput = '';

    public function mount(PlannerService $planner, ?string $date = null): void
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();

        $this->day = $planner->resolveDay(Auth::user(), $date);
        $this->authorize('view', $this->day);

        $this->startTimeInput = $this->day->started_at?->format('H:i') ?? now()->format('H:i');
    }

    #[On('task-saved')]
    #[On('day-updated')]
    public function refreshDay(): void
    {
        $this->day->refresh();
    }

    /** Record the start time the user typed (on the day's date). */
    public function setStart(DayService $service): void
    {
        $this->authorize('update', $this->day);

        $this->validate(
            ['startTimeInput' => ['required', 'date_format:H:i']],
            attributes: ['startTimeInput' => 'وقت البداية'],
        );

        $at = $this->day->date->copy()->setTimeFromTimeString($this->startTimeInput);
        $service->setStartedAt($this->day, $at);
        $this->day->refresh();
    }

    /** Quick action: start the day at the current time. */
    public function startNow(DayService $service): void
    {
        $this->authorize('update', $this->day);
        $service->setStartedAt($this->day, now());
        $this->day->refresh();
        $this->startTimeInput = $this->day->started_at->format('H:i');
    }

    public function deleteBreak(int $breakId): void
    {
        $this->authorize('update', $this->day);

        $break = DayBreak::where('day_id', $this->day->id)->findOrFail($breakId);
        $break->delete();

        $this->day->refresh();
    }

    public function toggleBreak(DayService $service): void
    {
        $this->authorize('update', $this->day);

        $running = $this->day->breaks()->whereNull('ended_at')->first();

        if ($running) {
            $service->endBreak($running);
        } else {
            $service->startBreak($this->day);
        }

        $this->day->refresh();
    }

    public function setTaskProgress(int $taskId, int $progress): void
    {
        $task = Task::findOrFail($taskId);
        $this->authorize('update', $task);

        $task->setProgress($progress);
        $task->save();

        $this->day->refresh();
    }

    public function addTask(): void
    {
        $this->dispatch('create-task', dayId: $this->day->id);
    }

    public function requestCloseDay(): void
    {
        $this->authorize('update', $this->day);
        $this->dispatch('close-day', day: $this->day->id);
    }

    public function render(): View
    {
        $this->day->load(['tasks.goal', 'breaks']);

        // Arrange the day as a timeline: scheduled tasks (by start time) first, then unscheduled.
        $tasks = $this->day->tasks
            ->sortBy(fn (Task $t) => $t->start_time ?: '99:99:99')
            ->values();

        $scheduled = $this->day->tasks->filter(fn (Task $t) => $t->start_time);
        $planStart = $scheduled->min('start_time');
        $planEnd = $this->day->tasks->filter(fn (Task $t) => $t->end_time)->max('end_time');
        $plannedMinutes = (int) $this->day->tasks->sum(fn (Task $t) => $t->durationMinutes() ?? 0);

        return view('livewire.planner.day-show', [
            'tasks' => $tasks,
            'planStartLabel' => $planStart ? Carbon::parse($planStart)->format('g:i A') : null,
            'planEndLabel' => $planEnd ? Carbon::parse($planEnd)->format('g:i A') : null,
            'plannedLabel' => Task::minutesToLabel($plannedMinutes),
            'ongoingBreak' => $this->day->breaks->firstWhere('ended_at', null),
            'completion' => $this->day->completionPercent(),
            'workedLabel' => $this->day->workedHoursLabel(),
            'prevDate' => $this->day->date->copy()->subDay()->toDateString(),
            'nextDate' => $this->day->date->copy()->addDay()->toDateString(),
        ]);
    }
}
