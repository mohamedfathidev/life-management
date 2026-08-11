<?php

namespace App\Livewire\Recovery;

use App\Enums\RecoveryStatus;
use App\Livewire\Forms\RecoveryForm;
use App\Models\Goal;
use App\Models\Recovery;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageRecovery extends Component
{
    public RecoveryForm $form;

    public bool $open = false;

    #[On('create-recovery')]
    public function openForCreate(): void
    {
        $this->resetValidation();
        $this->form->prepareForCreate();
        $this->open = true;
    }

    #[On('edit-recovery')]
    public function openForEdit(Recovery $recovery): void
    {
        $this->authorize('update', $recovery);
        $this->resetValidation();
        $this->form->setRecovery($recovery);
        $this->open = true;
    }

    public function save(): void
    {
        if ($this->form->recovery) {
            $this->authorize('update', $this->form->recovery);
        }

        $this->form->persist(Auth::id());

        $this->open = false;
        $this->dispatch('recovery-saved');
    }

    public function close(): void
    {
        $this->open = false;
        $this->form->reset();
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.recovery.manage-recovery', [
            'statuses' => RecoveryStatus::cases(),
            'goals' => Goal::query()->ownedBy(Auth::user())->orderBy('title')->get(['id', 'title', 'parent_id']),
        ]);
    }
}
