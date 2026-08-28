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
        $this->form->avoidance_reasons = [''];
        $this->form->protection_actions = [''];
        $this->open = true;
    }

    #[On('edit-recovery-log')]
    public function openForEdit(RecoveryLog $log): void
    {
        $this->authorize('update', $log->recovery);
        $this->resetValidation();
        $this->form->setLog($log);

        if ($this->form->avoidance_reasons === []) {
            $this->form->avoidance_reasons = [''];
        }
        if ($this->form->protection_actions === []) {
            $this->form->protection_actions = [''];
        }

        $this->open = true;
    }

    public function addAvoidanceReason(): void
    {
        $this->form->avoidance_reasons[] = '';
    }

    public function removeAvoidanceReason(int $index): void
    {
        unset($this->form->avoidance_reasons[$index]);
        $this->form->avoidance_reasons = array_values($this->form->avoidance_reasons);
    }

    public function addProtectionAction(): void
    {
        $this->form->protection_actions[] = '';
    }

    public function removeProtectionAction(int $index): void
    {
        unset($this->form->protection_actions[$index]);
        $this->form->protection_actions = array_values($this->form->protection_actions);
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
