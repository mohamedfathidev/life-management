<?php

namespace App\Livewire\Recovery;

use App\Models\RecoveryMistake;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageMistake extends Component
{
    public ?RecoveryMistake $mistake = null;

    public bool $open = false;

    public string $title = '';

    public ?string $note = null;

    public int $weight = 50;

    #[On('edit-mistake')]
    public function openForEdit(int $mistake): void
    {
        $this->mistake = RecoveryMistake::ownedBy(Auth::user())->findOrFail($mistake);
        $this->title = $this->mistake->title;
        $this->note = $this->mistake->note;
        $this->weight = $this->mistake->weight;
        $this->resetValidation();
        $this->open = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
            'weight' => ['required', 'integer', 'between:0,100'],
        ], attributes: [
            'title' => 'الخطأ',
            'note' => 'الملاحظة والتفاصيل',
            'weight' => 'النسبة',
        ]);

        $this->mistake->update($data);

        $this->open = false;
        $this->dispatch('mistake-saved');
    }

    public function close(): void
    {
        $this->open = false;
        $this->reset('mistake', 'title', 'note', 'weight');
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.recovery.manage-mistake');
    }
}
