<?php

namespace App\Livewire\Dreams;

use App\Enums\DreamStatus;
use App\Enums\DurationUnit;
use App\Livewire\Forms\DreamForm;
use App\Models\Dream;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageDream extends Component
{
    public DreamForm $form;

    public bool $open = false;

    #[On('create-dream')]
    public function openForCreate(): void
    {
        $this->resetValidation();
        $this->form->prepareForCreate();
        $this->open = true;
    }

    #[On('edit-dream')]
    public function openForEdit(Dream $dream): void
    {
        $this->authorize('update', $dream);
        $this->resetValidation();
        $this->form->setDream($dream);
        $this->open = true;
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
        return view('livewire.dreams.manage-dream', [
            'statuses' => DreamStatus::cases(),
            'units' => DurationUnit::cases(),
        ]);
    }
}
