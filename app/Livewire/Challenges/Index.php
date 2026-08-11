<?php

namespace App\Livewire\Challenges;

use App\Models\Challenge;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    #[On('challenge-saved')]
    public function refreshList(): void
    {
        //
    }

    /** Toggle today's success for a challenge. */
    public function toggleToday(int $challengeId): void
    {
        $challenge = Challenge::findOrFail($challengeId);
        $this->authorize('update', $challenge);

        $today = Carbon::today()->toDateString();
        $existing = $challenge->logs()->whereDate('date', $today)->first();

        if ($existing) {
            $existing->delete();
        } else {
            $challenge->logs()->create(['date' => $today]);
        }
    }

    public function render(): View
    {
        $challenges = Challenge::query()
            ->ownedBy(Auth::user())
            ->with('logs')
            ->orderByRaw("status = 'active' desc")
            ->latest()
            ->get();

        return view('livewire.challenges.index', [
            'challenges' => $challenges,
            'today' => Carbon::today()->toDateString(),
        ]);
    }
}
