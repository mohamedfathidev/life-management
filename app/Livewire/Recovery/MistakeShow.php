<?php

namespace App\Livewire\Recovery;

use App\Models\RecoveryMistake;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class MistakeShow extends Component
{
    public RecoveryMistake $mistake;

    public function mount(RecoveryMistake $mistake): void
    {
        if ($mistake->user_id !== Auth::id()) {
            abort(403);
        }

        $this->mistake = $mistake;
    }

    public function editMistake(): void
    {
        $this->dispatch('edit-mistake', mistake: $this->mistake->id);
    }

    #[On('mistake-saved')]
    public function refresh(): void
    {
        $this->mistake = $this->mistake->fresh();
    }

    public function delete(): void
    {
        if ($this->mistake->user_id !== Auth::id()) {
            abort(403);
        }

        $this->mistake->delete();
        $this->redirect(route('recovery.mistakes'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.recovery.mistake-show', [
            'mistake' => $this->mistake,
        ]);
    }
}
