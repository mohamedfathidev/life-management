<?php

namespace App\Livewire\Recovery;

use App\Enums\RecoveryStatus;
use App\Models\RecoveryChange;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * "تغييرات جذرية في شخصيتي" — index of ongoing and completed changes,
 * each tracked by a checklist of concrete steps rather than a vague intent.
 */
#[Layout('layouts.app')]
class Changes extends Component
{
    #[On('change-saved')]
    public function refreshList(): void
    {
        //
    }

    public function createChange(): void
    {
        $this->dispatch('create-change');
    }

    public function render(): View
    {
        $changes = RecoveryChange::query()
            ->ownedBy(Auth::user())
            ->withCount([
                'steps as total_steps',
                'steps as done_steps' => fn ($q) => $q->where('is_done', true),
            ])
            ->latest('started_at')
            ->get()
            ->each(function (RecoveryChange $change) {
                $change->progress = $change->total_steps > 0
                    ? (int) round($change->done_steps / $change->total_steps * 100)
                    : 0;
            });

        return view('livewire.recovery.changes', [
            'activeChanges' => $changes->where('status', '!=', RecoveryStatus::Completed)->values(),
            'completedChanges' => $changes->where('status', RecoveryStatus::Completed)->values(),
        ]);
    }
}
