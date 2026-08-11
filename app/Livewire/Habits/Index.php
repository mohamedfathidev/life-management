<?php

namespace App\Livewire\Habits;

use App\Models\Habit;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    #[On('habit-saved')]
    public function refreshList(): void
    {
        //
    }

    /** Toggle a habit's completion on a given date (presence-based). */
    public function toggle(int $habitId, string $date): void
    {
        $habit = Habit::findOrFail($habitId);
        $this->authorize('update', $habit);

        $existing = $habit->logs()->whereDate('date', $date)->first();

        if ($existing) {
            $existing->delete();
        } else {
            $habit->logs()->create(['date' => $date]);
        }
    }

    public function render(): View
    {
        $habits = Habit::query()
            ->ownedBy(Auth::user())
            ->active()
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        return view('livewire.habits.index', [
            'habits' => $habits,
            'today' => Carbon::today()->toDateString(),
        ]);
    }
}
