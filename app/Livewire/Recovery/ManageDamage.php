<?php

namespace App\Livewire\Recovery;

use App\Livewire\Forms\RecoveryDamageForm;
use App\Models\RecoveryDamage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageDamage extends Component
{
    public RecoveryDamageForm $form;

    public bool $open = false;

    #[On('create-damage')]
    public function openForCreate(?int $parentId = null): void
    {
        $this->resetValidation();
        $this->form->prepareForCreate($parentId);
        $this->open = true;
    }

    #[On('edit-damage')]
    public function openForEdit(RecoveryDamage $damage): void
    {
        $this->authorize('update', $damage);
        $this->resetValidation();
        $this->form->setDamage($damage);
        $this->open = true;
    }

    #[On('delete-damage')]
    public function delete(RecoveryDamage $damage): void
    {
        $this->authorize('delete', $damage);
        $damage->delete(); // sub-damages cascade via FK

        $this->dispatch('damage-saved');
    }

    public function save(): void
    {
        if ($this->form->damage) {
            $this->authorize('update', $this->form->damage);
        }

        $this->form->persist(Auth::id());

        $this->open = false;
        $this->dispatch('damage-saved');
    }

    public function close(): void
    {
        $this->open = false;
        $this->form->reset();
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.recovery.manage-damage', [
            'mainDamages' => RecoveryDamage::query()
                ->ownedBy(Auth::user())
                ->main()
                ->when($this->form->damage?->exists, fn ($q) => $q->where('id', '!=', $this->form->damage->id))
                ->orderBy('title')
                ->get(['id', 'title']),
        ]);
    }
}
