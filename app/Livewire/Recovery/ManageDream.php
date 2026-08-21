<?php

namespace App\Livewire\Recovery;

use App\Livewire\Forms\RecoveryDreamForm;
use App\Models\Recovery;
use App\Models\RecoveryDream;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageDream extends Component
{
    public RecoveryDreamForm $form;

    public bool $open = false;

    #[On('create-dream')]
    public function openForCreate(): void
    {
        $this->resetValidation();
        $this->form->prepareForCreate();
        $this->open = true;
    }

    #[On('edit-dream')]
    public function openForEdit(RecoveryDream $dream): void
    {
        $this->authorize('update', $dream);
        $this->resetValidation();
        $this->form->setDream($dream);
        $this->open = true;
    }

    #[On('delete-dream')]
    public function delete(RecoveryDream $dream): void
    {
        $this->authorize('delete', $dream);
        $dream->delete();

        $this->dispatch('dream-saved');
    }

    public function save(): void
    {
        if ($this->form->dream) {
            $this->authorize('update', $this->form->dream);
        }

        $this->form->persist(Auth::id());

        $this->open = false;
        $this->dispatch('dream-saved');
    }

    public function close(): void
    {
        $this->open = false;
        $this->form->reset();
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.recovery.manage-dream', [
            'recoveries' => Recovery::query()->ownedBy(Auth::user())->orderByDesc('start_date')->get(['id', 'title', 'start_date', 'end_date']),
        ]);
    }
}
