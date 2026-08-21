<?php

namespace App\Livewire\Recovery;

use App\Models\RecoveryDream;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * "أحلام التعافي وفوائدها" — a vision board of what recovery is *for*,
 * not just what it's away from.
 */
#[Layout('layouts.app')]
class Dreams extends Component
{
    #[On('dream-saved')]
    public function refreshList(): void
    {
        //
    }

    public function createDream(): void
    {
        $this->dispatch('create-dream');
    }

    public function toggleAchieved(int $id): void
    {
        $dream = RecoveryDream::ownedBy(Auth::user())->findOrFail($id);

        $dream->update([
            'is_achieved' => ! $dream->is_achieved,
            'achieved_at' => $dream->is_achieved ? null : now()->toDateString(),
        ]);
    }

    public function render(): View
    {
        $dreams = RecoveryDream::query()
            ->ownedBy(Auth::user())
            ->orderBy('is_achieved')
            ->latest()
            ->get();

        return view('livewire.recovery.dreams', [
            'activeDreams' => $dreams->where('is_achieved', false)->values(),
            'achievedDreams' => $dreams->where('is_achieved', true)->values(),
        ]);
    }
}
