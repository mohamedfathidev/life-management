<?php

namespace App\Livewire\Recovery;

use App\Models\RecoveryCommitment;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

/** "حاجات لازم تلتزم بيها" — a flat list of personal rules/commitments. */
#[Layout('layouts.app')]
class Commitments extends Component
{
    public ?int $editingId = null;

    public string $title = '';

    public ?string $description = null;

    public function save(): void
    {
        $data = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ], attributes: ['title' => 'الالتزام', 'description' => 'التفاصيل']);

        if ($this->editingId) {
            RecoveryCommitment::ownedBy(Auth::user())->findOrFail($this->editingId)->update($data);
        } else {
            $maxOrder = RecoveryCommitment::ownedBy(Auth::user())->max('sort_order');
            RecoveryCommitment::create($data + ['user_id' => Auth::id(), 'sort_order' => ((int) $maxOrder) + 1]);
        }

        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $commitment = RecoveryCommitment::ownedBy(Auth::user())->findOrFail($id);
        $this->editingId = $commitment->id;
        $this->title = $commitment->title;
        $this->description = $commitment->description;
    }

    public function delete(int $id): void
    {
        RecoveryCommitment::ownedBy(Auth::user())->where('id', $id)->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }
    }

    public function resetForm(): void
    {
        $this->reset('editingId', 'title', 'description');
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.recovery.commitments', [
            'commitments' => RecoveryCommitment::ownedBy(Auth::user())->orderBy('sort_order')->get(),
        ]);
    }
}
