<?php

namespace App\Livewire\Recovery;

use App\Livewire\Forms\RecoveryStoryForm;
use App\Models\Recovery;
use App\Models\RecoveryStory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageStory extends Component
{
    public RecoveryStoryForm $form;

    public bool $open = false;

    #[On('create-story')]
    public function openForCreate(?int $recoveryId = null): void
    {
        $this->resetValidation();
        $this->form->prepareForCreate($recoveryId);
        $this->open = true;
    }

    #[On('edit-story')]
    public function openForEdit(RecoveryStory $story): void
    {
        $this->authorize('update', $story);
        $this->resetValidation();
        $this->form->setStory($story);
        $this->open = true;
    }

    #[On('delete-story')]
    public function delete(RecoveryStory $story): void
    {
        $this->authorize('delete', $story);
        $story->delete();

        $this->dispatch('story-saved');
    }

    public function save(): void
    {
        if ($this->form->story) {
            $this->authorize('update', $this->form->story);
        }

        $this->form->persist(Auth::id());

        $this->open = false;
        $this->dispatch('story-saved');
    }

    public function close(): void
    {
        $this->open = false;
        $this->form->reset();
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.recovery.manage-story', [
            'recoveries' => Recovery::query()->ownedBy(Auth::user())->orderByDesc('start_date')->get(['id', 'title', 'start_date', 'end_date']),
        ]);
    }
}
