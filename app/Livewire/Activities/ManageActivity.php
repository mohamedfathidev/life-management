<?php

namespace App\Livewire\Activities;

use App\Enums\ActivityStage;
use App\Enums\ActivityType;
use App\Livewire\Forms\ActivityForm;
use App\Models\Activity;
use App\Models\Goal;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageActivity extends Component
{
    public ActivityForm $form;

    public bool $open = false;

    #[On('create-activity')]
    public function openForCreate(): void
    {
        $this->form->reset();
        $this->resetValidation();
        $this->open = true;
    }

    #[On('edit-activity')]
    public function openForEdit(Activity $activity): void
    {
        $this->authorize('update', $activity);
        $this->resetValidation();
        $this->form->setActivity($activity);
        $this->open = true;
    }

    public function addReason(): void
    {
        $this->form->reasons[] = '';
    }

    public function removeReason(int $index): void
    {
        unset($this->form->reasons[$index]);
        $this->form->reasons = array_values($this->form->reasons);
    }

    public function save(): void
    {
        if ($this->form->activity) {
            $this->authorize('update', $this->form->activity);
        }

        $this->form->persist(Auth::id());

        $this->open = false;
        $this->dispatch('activity-saved');
    }

    public function close(): void
    {
        $this->open = false;
        $this->form->reset();
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.activities.manage-activity', [
            'types' => ActivityType::cases(),
            'stages' => ActivityStage::cases(),
            'goals' => Goal::query()->ownedBy(Auth::user())->orderBy('title')->get(['id', 'title', 'parent_id']),
        ]);
    }
}
