<?php

namespace App\Livewire\Scholarships;

use App\Livewire\Forms\VolunteerForm;
use App\Models\VolunteerActivity;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageVolunteer extends Component
{
    public VolunteerForm $form;

    public bool $open = false;

    #[On('create-volunteer')]
    public function openForCreate(): void
    {
        $this->form->reset();
        $this->resetValidation();
        $this->open = true;
    }

    #[On('edit-volunteer')]
    public function openForEdit(VolunteerActivity $activity): void
    {
        $this->authorize('update', $activity);
        $this->resetValidation();
        $this->form->setActivity($activity);
        $this->open = true;
    }

    #[On('delete-volunteer')]
    public function delete(VolunteerActivity $activity): void
    {
        $this->authorize('delete', $activity);
        $activity->delete();

        $this->dispatch('volunteer-saved');
    }

    public function save(): void
    {
        if ($this->form->activity) {
            $this->authorize('update', $this->form->activity);
        }

        $this->form->persist(Auth::id());

        $this->open = false;
        $this->dispatch('volunteer-saved');
    }

    public function close(): void
    {
        $this->open = false;
        $this->form->reset();
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.scholarships.manage-volunteer');
    }
}
