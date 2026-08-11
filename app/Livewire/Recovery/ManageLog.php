<?php

namespace App\Livewire\Recovery;

use App\Livewire\Forms\RecoveryLogForm;
use App\Models\Recovery;
use App\Models\RecoveryLog;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageLog extends Component
{
    public RecoveryLogForm $form;

    public bool $open = false;

    #[On('create-recovery-log')]
    public function openForCreate(int $recoveryId, bool $setback = false): void
    {
        $recovery = Recovery::findOrFail($recoveryId);
        $this->authorize('update', $recovery);

        $this->resetValidation();
        $this->form->prepareForCreate($recoveryId);
        $this->form->is_setback = $setback;
        $this->open = true;
    }

    #[On('edit-recovery-log')]
    public function openForEdit(RecoveryLog $log): void
    {
        $this->authorize('update', $log->recovery);
        $this->resetValidation();
        $this->form->setLog($log);
        $this->open = true;
    }

    #[On('delete-recovery-log')]
    public function delete(RecoveryLog $log): void
    {
        $this->authorize('update', $log->recovery);
        $log->delete();

        $this->dispatch('recovery-log-saved');
    }

    public function save(): void
    {
        $recovery = Recovery::findOrFail($this->form->recovery_id);
        $this->authorize('update', $recovery);

        $this->form->persist();

        $this->open = false;
        $this->dispatch('recovery-log-saved');
    }

    public function close(): void
    {
        $this->open = false;
        $this->form->reset();
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.recovery.manage-log');
    }
}
