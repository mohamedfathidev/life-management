<?php

namespace App\Livewire\Scholarships;

use App\Enums\ScholarshipStage;
use App\Livewire\Forms\ScholarshipForm;
use App\Models\Scholarship;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageScholarship extends Component
{
    public ScholarshipForm $form;

    public bool $open = false;

    #[On('create-scholarship')]
    public function openForCreate(): void
    {
        $this->form->reset();
        $this->resetValidation();
        $this->open = true;
    }

    #[On('edit-scholarship')]
    public function openForEdit(Scholarship $scholarship): void
    {
        $this->authorize('update', $scholarship);
        $this->resetValidation();
        $this->form->setScholarship($scholarship);
        $this->open = true;
    }

    public function save(): void
    {
        if ($this->form->scholarship) {
            $this->authorize('update', $this->form->scholarship);
        }

        $this->form->persist(Auth::id());

        $this->open = false;
        $this->dispatch('scholarship-saved');
    }

    public function close(): void
    {
        $this->open = false;
        $this->form->reset();
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.scholarships.manage-scholarship', [
            'stages' => ScholarshipStage::cases(),
        ]);
    }
}
