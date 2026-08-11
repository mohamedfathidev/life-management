<?php

namespace App\Livewire\Goals;

use App\Enums\GoalCategory;
use App\Enums\GoalStatus;
use App\Livewire\Forms\GoalForm;
use App\Models\Goal;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageGoal extends Component
{
    public GoalForm $form;

    public bool $open = false;

    #[On('create-goal')]
    public function openForCreate(?int $parentId = null): void
    {
        $this->form->reset();
        $this->resetValidation();
        $this->form->parent_id = $parentId;
        $this->open = true;
    }

    #[On('edit-goal')]
    public function openForEdit(Goal $goal): void
    {
        $this->authorize('update', $goal);
        $this->resetValidation();
        $this->form->setGoal($goal);
        $this->open = true;
    }

    public function save(): void
    {
        if ($this->form->goal) {
            $this->authorize('update', $this->form->goal);
        }

        $this->form->persist(Auth::id());

        $this->open = false;
        $this->dispatch('goal-saved');
    }

    public function close(): void
    {
        $this->open = false;
        $this->form->reset();
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.goals.manage-goal', [
            'categories' => GoalCategory::cases(),
            'statuses' => GoalStatus::cases(),
        ]);
    }
}
