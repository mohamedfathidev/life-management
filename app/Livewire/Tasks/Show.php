<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public Task $task;

    public ?string $notes = null;
    public ?int $estimatedMinutes = null;
    public ?int $actualMinutes = null;
    public bool $actualIsAuto = true;
    public ?int $rating = null;

    public function mount(Task $task): void
    {
        $this->authorize('view', $task);
        $this->task = $task;
        $this->fillFromTask();
    }

    private function fillFromTask(): void
    {
        $this->notes = $this->task->notes;
        $this->estimatedMinutes = $this->task->estimated_minutes ?? $this->task->durationMinutes();
        $this->actualIsAuto = $this->task->actual_minutes === null;
        $this->actualMinutes = $this->task->actualMinutes();
        $this->rating = $this->task->rating;
    }

    #[On('task-saved')]
    public function refreshTask(): void
    {
        $this->task->refresh();
        $this->fillFromTask();
    }

    public function save(): void
    {
        $this->authorize('update', $this->task);

        $validated = $this->validate([
            'notes' => ['nullable', 'string', 'max:20000'],
            'estimatedMinutes' => ['nullable', 'integer', 'min:0', 'max:10080'],
            'actualMinutes' => ['nullable', 'integer', 'min:0', 'max:10080'],
            'rating' => ['nullable', 'integer', 'between:0,10'],
        ], attributes: [
            'estimatedMinutes' => 'الوقت المتوقّع',
            'actualMinutes' => 'الوقت الفعلي',
            'rating' => 'التقييم',
        ]);

        $this->task->update([
            'notes' => $validated['notes'],
            'estimated_minutes' => $validated['estimatedMinutes'],
            // Auto → null (falls back to focus totals); manual → the entered value.
            'actual_minutes' => $this->actualIsAuto ? null : $validated['actualMinutes'],
            'rating' => $validated['rating'],
        ]);

        $this->task->refresh();
        $this->fillFromTask();

        $this->dispatch('task-detail-saved');
    }

    public function setProgress(int $progress): void
    {
        $this->authorize('update', $this->task);
        $this->task->setProgress($progress);
        $this->task->save();
        $this->task->refresh();
    }

    public function edit(): void
    {
        $this->dispatch('edit-task', task: $this->task->id);
    }

    public function delete()
    {
        $this->authorize('delete', $this->task);
        $this->task->delete();

        return $this->redirectRoute('tasks.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.tasks.show', [
            'focusMinutes' => (int) round($this->task->focusSecondsTotal() / 60),
        ]);
    }
}
