<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * A quick "task done" modal to capture rating + expected/actual time when a
 * task is checked complete. Opened by the `complete-task` event from anywhere.
 */
class CompleteModal extends Component
{
    public ?Task $task = null;
    public bool $open = false;

    public ?int $rating = null;
    public ?int $estimatedMinutes = null;
    public bool $actualIsAuto = true;
    public ?int $actualMinutes = null;
    public int $focusMinutes = 0;

    #[On('complete-task')]
    public function openFor(Task $task): void
    {
        $this->authorize('update', $task);
        $this->resetValidation();

        $this->task = $task;
        $this->focusMinutes = (int) round($task->focusSecondsTotal() / 60);
        $this->rating = $task->rating;
        $this->estimatedMinutes = $task->estimated_minutes ?? $task->durationMinutes();
        $this->actualIsAuto = $task->actual_minutes === null;
        $this->actualMinutes = $task->actualMinutes();

        $this->open = true;
    }

    public function complete(): void
    {
        $this->authorize('update', $this->task);

        $validated = $this->validate([
            'rating' => ['nullable', 'integer', 'between:0,10'],
            'estimatedMinutes' => ['nullable', 'integer', 'min:0', 'max:10080'],
            'actualMinutes' => ['nullable', 'integer', 'min:0', 'max:10080'],
        ], attributes: ['rating' => 'التقييم', 'estimatedMinutes' => 'الوقت المتوقّع', 'actualMinutes' => 'الوقت الفعلي']);

        $this->task->update([
            'estimated_minutes' => $validated['estimatedMinutes'],
            'actual_minutes' => $this->actualIsAuto ? null : $validated['actualMinutes'],
            'rating' => $validated['rating'],
        ]);
        $this->task->setProgress(100);
        $this->task->save();

        $this->open = false;
        $this->dispatch('task-saved');
    }

    /** Mark done without filling the details. */
    public function quickDone(): void
    {
        $this->authorize('update', $this->task);
        $this->task->setProgress(100);
        $this->task->save();

        $this->open = false;
        $this->dispatch('task-saved');
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function render(): View
    {
        return view('livewire.tasks.complete-modal');
    }
}
