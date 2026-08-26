<?php

namespace App\Livewire\Recovery;

use App\Models\RecoveryHardMoment;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * "أصعب اللحظات" — recurring trigger scenarios, each with a coping plan
 * written ahead of time (in the Show page), so it's ready before the moment hits.
 */
#[Layout('layouts.app')]
class HardMoments extends Component
{
    use WithPagination;

    public ?int $editingId = null;

    public string $title = '';

    public ?string $description = null;

    public function save(): void
    {
        $data = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ], attributes: ['title' => 'اللحظة', 'description' => 'الوصف']);

        if ($this->editingId) {
            $moment = RecoveryHardMoment::ownedBy(Auth::user())->findOrFail($this->editingId);
            $moment->update($data);
        } else {
            RecoveryHardMoment::create($data + ['user_id' => Auth::id()]);
        }

        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $moment = RecoveryHardMoment::ownedBy(Auth::user())->findOrFail($id);
        $this->editingId = $moment->id;
        $this->title = $moment->title;
        $this->description = $moment->description;
    }

    public function delete(int $id): void
    {
        RecoveryHardMoment::ownedBy(Auth::user())->where('id', $id)->delete();

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
        return view('livewire.recovery.hard-moments', [
            'moments' => RecoveryHardMoment::ownedBy(Auth::user())->latest()->paginate(12),
        ]);
    }
}
