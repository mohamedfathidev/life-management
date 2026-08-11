<?php

namespace App\Livewire\Recovery;

use App\Models\Recovery;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public Recovery $recovery;

    public function mount(Recovery $recovery): void
    {
        $this->authorize('view', $recovery);
        $this->recovery = $recovery;
    }

    #[On('recovery-saved')]
    #[On('recovery-log-saved')]
    public function refreshRecovery(): void
    {
        $this->recovery->refresh();
    }

    public function editRecovery(): void
    {
        $this->dispatch('edit-recovery', recovery: $this->recovery->id);
    }

    public function addLog(bool $setback = false): void
    {
        $this->dispatch('create-recovery-log', recoveryId: $this->recovery->id, setback: $setback);
    }

    public function render(): View
    {
        $logs = $this->recovery->logs()->get();

        return view('livewire.recovery.show', [
            'logs' => $logs,
            'streakDays' => $this->recovery->currentStreakDays(),
            'streakSince' => $this->recovery->streakSince(),
            'bestStreak' => $this->recovery->bestStreakDays(),
            'setbackCount' => $this->recovery->setbackCount(),
        ]);
    }
}
