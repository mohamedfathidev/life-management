<?php

namespace App\Livewire\Planner;

use App\Enums\TaskKind;
use App\Livewire\Forms\TaskForm;
use App\Models\Goal;
use App\Models\Task;
use App\Services\PlannerService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageTask extends Component
{
    public TaskForm $form;

    public bool $open = false;

    /**
     * Open the task modal, optionally pre-filled by whichever module launched it.
     * `today: true` drops the task straight into today's plan (no dayId needed),
     * so any page across the app can collect a task into "today's tasks".
     */
    #[On('create-task')]
    public function openForCreate(
        ?int $dayId = null,
        ?int $goalId = null,
        ?int $studyTrackId = null,
        ?string $title = null,
        ?string $kind = null,
        bool $today = false,
    ): void {
        $this->form->reset();
        $this->resetValidation();

        if ($today && ! $dayId) {
            $dayId = app(PlannerService::class)->resolveDay(Auth::user(), Carbon::today())->id;
        }

        $this->form->day_id = $dayId;
        $this->form->goal_id = $goalId;
        $this->form->study_track_id = $studyTrackId;

        if ($title) {
            $this->form->title = $title;
        }

        if ($studyTrackId) {
            $this->form->kind = TaskKind::Study->value;
        } elseif ($kind && TaskKind::tryFrom($kind)) {
            $this->form->kind = $kind;
        }

        $this->open = true;
    }

    #[On('edit-task')]
    public function openForEdit(Task $task): void
    {
        $this->authorize('update', $task);
        $this->resetValidation();
        $this->form->setTask($task);
        $this->open = true;
    }

    #[On('delete-task')]
    public function delete(Task $task): void
    {
        $this->authorize('delete', $task);
        $task->delete();

        $this->dispatch('task-saved');
    }

    public function save(): void
    {
        if ($this->form->task) {
            $this->authorize('update', $this->form->task);
        }

        $this->form->persist(Auth::id());

        $this->open = false;
        $this->dispatch('task-saved');
    }

    public function close(): void
    {
        $this->open = false;
        $this->form->reset();
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.planner.manage-task', [
            'kinds' => TaskKind::cases(),
            'goals' => Goal::query()
                ->ownedBy(Auth::user())
                ->topLevel()
                ->with(['children' => fn ($q) => $q->orderBy('title')])
                ->orderBy('title')
                ->get(['id', 'title', 'parent_id']),
        ]);
    }
}
