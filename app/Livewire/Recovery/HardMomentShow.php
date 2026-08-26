<?php

namespace App\Livewire\Recovery;

use App\Models\RecoveryHardMoment;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class HardMomentShow extends Component
{
    public RecoveryHardMoment $moment;

    public function mount(RecoveryHardMoment $moment): void
    {
        if ($moment->user_id !== Auth::id()) {
            abort(403);
        }

        $this->moment = $moment;
    }

    public function editMoment(): void
    {
        $this->dispatch('edit-hard-moment', moment: $this->moment->id);
    }

    #[On('hard-moment-saved')]
    public function refresh(): void
    {
        $this->moment = $this->moment->fresh();
    }

    public function delete(): void
    {
        if ($this->moment->user_id !== Auth::id()) {
            abort(403);
        }

        $this->moment->delete();
        $this->redirect(route('recovery.hard-moments'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.recovery.hard-moment-show', [
            'moment' => $this->moment,
        ]);
    }
}
