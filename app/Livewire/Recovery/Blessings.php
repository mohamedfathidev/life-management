<?php

namespace App\Livewire\Recovery;

use App\Models\RecoveryBlessing;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * "النعم التي أغفل عنها" — a simple, user-written list of blessings not to
 * take for granted. Opened from the health-blessing reminder on "ببساطة".
 */
#[Layout('layouts.app')]
class Blessings extends Component
{
    public ?int $editingId = null;

    public string $text = '';

    public function save(): void
    {
        $data = $this->validate([
            'text' => ['required', 'string', 'max:1000'],
        ], attributes: ['text' => 'النعمة']);

        if ($this->editingId) {
            RecoveryBlessing::ownedBy(Auth::user())->where('id', $this->editingId)->update($data);
        } else {
            RecoveryBlessing::create([
                'user_id' => Auth::id(),
                'text' => $data['text'],
                'position' => (int) RecoveryBlessing::ownedBy(Auth::user())->max('position') + 1,
            ]);
        }

        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $blessing = RecoveryBlessing::ownedBy(Auth::user())->findOrFail($id);

        $this->editingId = $blessing->id;
        $this->text = $blessing->text;
    }

    public function delete(int $id): void
    {
        RecoveryBlessing::ownedBy(Auth::user())->where('id', $id)->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'text']);
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.recovery.blessings', [
            'blessings' => RecoveryBlessing::ownedBy(Auth::user())->orderBy('position')->latest()->get(),
        ]);
    }
}
