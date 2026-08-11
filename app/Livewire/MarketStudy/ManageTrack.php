<?php

namespace App\Livewire\MarketStudy;

use App\Livewire\Forms\StudyTrackForm;
use App\Models\StudyTrack;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageTrack extends Component
{
    public StudyTrackForm $form;

    public bool $open = false;

    #[On('create-track')]
    public function openForCreate(): void
    {
        $this->form->reset();
        $this->resetValidation();
        $this->open = true;
    }

    #[On('edit-track')]
    public function openForEdit(StudyTrack $track): void
    {
        $this->authorize('update', $track);
        $this->resetValidation();
        $this->form->setTrack($track);
        $this->open = true;
    }

    public function save(): void
    {
        if ($this->form->track) {
            $this->authorize('update', $this->form->track);
        }

        $this->form->persist(Auth::id());

        $this->open = false;
        $this->dispatch('track-saved');
    }

    public function close(): void
    {
        $this->open = false;
        $this->form->reset();
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.market-study.manage-track');
    }
}
