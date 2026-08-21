<?php

namespace App\Livewire\Lab;

use App\Enums\ProjectStatus;
use App\Livewire\Forms\ProjectForm;
use App\Models\Goal;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageProject extends Component
{
    public ProjectForm $form;

    public bool $open = false;

    #[On('create-project')]
    public function openForCreate(): void
    {
        $this->resetValidation();
        $this->form->prepareForCreate();
        $this->open = true;
    }

    #[On('edit-project')]
    public function openForEdit(Project $project): void
    {
        $this->authorize('update', $project);
        $this->resetValidation();
        $this->form->setProject($project);
        $this->open = true;
    }

    public function save(): void
    {
        if ($this->form->project) {
            $this->authorize('update', $this->form->project);
        }

        $this->form->persist(Auth::id());

        $this->open = false;
        $this->dispatch('project-saved');
    }

    public function close(): void
    {
        $this->open = false;
        $this->form->reset();
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.lab.manage-project', [
            'statuses' => ProjectStatus::cases(),
            'goals' => Goal::query()->ownedBy(Auth::user())->orderBy('title')->get(['id', 'title', 'parent_id']),
        ]);
    }
}
