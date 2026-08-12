<?php

namespace App\Livewire\Recovery;

use App\Models\RecoveryMistake;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * "أخطاء التعافي" — the user's own mistakes, each weighted by how much it keeps
 * them in "the prison". Sorted by weight so the biggest offenders surface first.
 */
#[Layout('layouts.app')]
class Mistakes extends Component
{
    public ?int $editingId = null;

    public string $title = '';
    public ?string $note = null;
    public int $weight = 50;

    public function save(): void
    {
        $data = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:5000'],
            'weight' => ['required', 'integer', 'between:0,100'],
        ], attributes: ['title' => 'الخطأ', 'weight' => 'النسبة']);

        if ($this->editingId) {
            $mistake = RecoveryMistake::ownedBy(Auth::user())->findOrFail($this->editingId);
            $mistake->update($data);
        } else {
            RecoveryMistake::create($data + ['user_id' => Auth::id()]);
        }

        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $mistake = RecoveryMistake::ownedBy(Auth::user())->findOrFail($id);
        $this->editingId = $mistake->id;
        $this->title = $mistake->title;
        $this->note = $mistake->note;
        $this->weight = $mistake->weight;
    }

    public function delete(int $id): void
    {
        RecoveryMistake::ownedBy(Auth::user())->where('id', $id)->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }
    }

    public function resetForm(): void
    {
        $this->reset('editingId', 'title', 'note', 'weight');
        $this->weight = 50;
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.recovery.mistakes', [
            'mistakes' => RecoveryMistake::ownedBy(Auth::user())
                ->orderByDesc('weight')->latest()->get(),
        ]);
    }
}
