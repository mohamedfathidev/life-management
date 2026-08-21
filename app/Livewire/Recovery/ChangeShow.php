<?php

namespace App\Livewire\Recovery;

use App\Models\RecoveryChange;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ChangeShow extends Component
{
    public RecoveryChange $change;

    public string $newStep = '';

    public function mount(RecoveryChange $change): void
    {
        $this->authorize('view', $change);
        $this->change = $change;
    }

    public function addStep(): void
    {
        $this->authorize('update', $this->change);

        $this->validate(['newStep' => ['required', 'string', 'max:255']], attributes: ['newStep' => 'الخطوة']);

        $nextOrder = (int) $this->change->steps()->max('sort_order') + 1;

        $this->change->steps()->create([
            'title' => $this->newStep,
            'sort_order' => $nextOrder,
        ]);

        $this->newStep = '';
    }

    public function toggleStep(int $stepId): void
    {
        $this->authorize('update', $this->change);

        $step = $this->change->steps()->findOrFail($stepId);
        $step->update([
            'is_done' => ! $step->is_done,
            'done_at' => $step->is_done ? null : now()->toDateString(),
        ]);
    }

    public function deleteStep(int $stepId): void
    {
        $this->authorize('update', $this->change);

        $this->change->steps()->where('id', $stepId)->delete();
    }

    public function editChange(): void
    {
        $this->dispatch('edit-change', change: $this->change->id);
    }

    public function deleteChange(): void
    {
        $this->authorize('delete', $this->change);

        $this->change->delete();

        $this->redirect(route('recovery.changes'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.recovery.change-show', [
            'steps' => $this->change->steps,
            'progress' => $this->change->progressPercent(),
        ]);
    }
}
