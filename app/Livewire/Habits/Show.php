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
class Show extends Component
{
    public Habit $habit;

    public function mount(Habit $habit): void
    {
        $this->authorize('view', $habit);
        $this->habit = $habit;
    }

    #[On('habit-saved')]
    public function refreshHabit(): void
    {
        $this->habit->refresh();
    }

    public function editHabit(): void
    {
        $this->dispatch('edit-habit', habit: $this->habit->id);
    }

    public function toggle(string $date): void
    {
        $this->authorize('update', $this->habit);

        $existing = $this->habit->logs()->whereDate('date', $date)->first();

        if ($existing) {
            $existing->delete();
        } else {
            $this->habit->logs()->create(['date' => $date]);
        }

        $this->habit->refresh();
    }

    public function delete()
    {
        $this->authorize('delete', $this->habit);
        $this->habit->delete();

        return $this->redirectRoute('habits.index', navigate: true);
    }

    public function render(): View
    {
        $today = Carbon::today();

        // Grid window: whole period for intermittent, last ~112 days for recurring.
        $gridEnd = $this->habit->isIntermittent() && $this->habit->end_date
            ? $this->habit->end_date->copy()
            : $today->copy();

        $gridStart = $this->habit->start_date->copy();
        if ($gridStart->diffInDays($gridEnd) > 111) {
            $gridStart = $gridEnd->copy()->subDays(111);
        }

        $doneDates = $this->habit->logs()
            ->whereBetween('date', [$gridStart->toDateString(), $gridEnd->toDateString()])
            ->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->flip();

        $cells = [];
        for ($d = $gridStart->copy(); $d->lte($gridEnd); $d->addDay()) {
            $key = $d->toDateString();
            $cells[] = [
                'date' => $key,
                'day' => $d->format('j'),
                'done' => $doneDates->has($key),
                'isToday' => $d->isSameDay($today),
                'isFuture' => $d->gt($today),
            ];
        }

        return view('livewire.habits.show', [
            'adherence' => $this->habit->adherencePercent(),
            'doneCount' => $this->habit->doneCount(),
            'missedCount' => $this->habit->missedCount(),
            'applicableDays' => $this->habit->applicableDays(),
            'currentStreak' => $this->habit->currentStreak(),
            'bestStreak' => $this->habit->bestStreak(),
            'periodDays' => $this->habit->periodDays(),
            'cells' => $cells,
        ]);
    }
}
