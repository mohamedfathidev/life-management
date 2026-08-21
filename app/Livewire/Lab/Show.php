<?php

namespace App\Livewire\Lab;

use App\Models\Project;
use App\Models\ProjectStep;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public Project $project;

    public string $newStepTitle = '';

    public string $newStepDescription = '';

    public function mount(Project $project): void
    {
        $this->authorize('view', $project);
        $this->project = $project;
    }

    #[On('project-saved')]
    public function refreshProject(): void
    {
        $this->project->refresh();
    }

    public function editProject(): void
    {
        $this->dispatch('edit-project', project: $this->project->id);
    }

    public function delete()
    {
        $this->authorize('delete', $this->project);
        $this->project->delete();

        return $this->redirectRoute('lab.index', navigate: true);
    }

    public function addStep(): void
    {
        $this->authorize('update', $this->project);
        $title = trim($this->newStepTitle);

        if ($title === '') {
            return;
        }

        $this->project->steps()->create([
            'title' => $title,
            'description' => trim($this->newStepDescription) ?: null,
            'position' => (int) $this->project->steps()->max('position') + 1,
        ]);

        $this->newStepTitle = '';
        $this->newStepDescription = '';
    }

    public function toggleStep(int $stepId): void
    {
        $this->authorize('update', $this->project);
        $step = $this->ownedStep($stepId);
        $step->update(['is_done' => ! $step->is_done]);
    }

    public function deleteStep(int $stepId): void
    {
        $this->authorize('update', $this->project);
        $this->ownedStep($stepId)->delete();
    }

    private function ownedStep(int $stepId): ProjectStep
    {
        return ProjectStep::where('project_id', $this->project->id)->findOrFail($stepId);
    }

    public function render(): View
    {
        return view('livewire.lab.show', [
            'steps' => $this->project->steps,
            'progress' => $this->project->progressPercent(),
        ]);
    }
}
