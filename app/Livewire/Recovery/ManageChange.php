<?php

namespace App\Livewire\Recovery;

use App\Livewire\Forms\RecoveryChangeForm;
use App\Models\Recovery;
use App\Models\RecoveryChange;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageChange extends Component
{
    public RecoveryChangeForm $form;

    public bool $open = false;

    #[On('create-change')]
    public function openForCreate(): void
    {
        $this->resetValidation();
        $this->form->prepareForCreate();
        $this->open = true;
    }

    #[On('edit-change')]
    public function openForEdit(RecoveryChange $change): void
    {
        $this->authorize('update', $change);
        $this->resetValidation();
        $this->form->setChange($change);
        $this->open = true;
    }

    #[On('delete-change')]
    public function delete(RecoveryChange $change): void
    {
        $this->authorize('delete', $change);
        $change->delete();

        $this->dispatch('change-saved');
    }

    public function save(): void
    {
        if ($this->form->change) {
            $this->authorize('update', $this->form->change);
        }

        $this->form->persist(Auth::id());

        $this->open = false;
        $this->dispatch('change-saved');
    }

    public function close(): void
    {
        $this->open = false;
        $this->form->reset();
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.recovery.manage-change', [
            'recoveries' => Recovery::query()->ownedBy(Auth::user())->orderByDesc('start_date')->get(['id', 'title', 'start_date', 'end_date']),
        ]);
    }
}
