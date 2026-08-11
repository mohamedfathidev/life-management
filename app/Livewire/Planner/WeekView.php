<?php

namespace App\Livewire\Planner;

use App\Models\Day;
use App\Models\Week;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class WeekView extends Component
{
    public string $anchor;

    public function mount(?string $date = null): void
    {
        $this->anchor = ($date ? Carbon::parse($date) : Carbon::today())->toDateString();
    }

    public function render(): View
    {
        $anchor = Carbon::parse($this->anchor);
        [$start, $end] = Week::boundariesFor($anchor);

        // Existing days in this week, keyed by date for quick lookup.
        $days = Day::query()
            ->ownedBy(Auth::user())
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->withCount('tasks')
            ->with('tasks:id,day_id,progress')
            ->get()
            ->keyBy(fn (Day $day) => $day->date->toDateString());

        // Build the 7-day strip (Sat → Fri), each entry has the Day or null.
        $strip = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $start->copy()->addDays($i);
            $key = $date->toDateString();
            $strip[] = ['date' => $date, 'day' => $days->get($key)];
        }

        return view('livewire.planner.week-view', [
            'start' => $start,
            'end' => $end,
            'strip' => $strip,
            'prevWeek' => $start->copy()->subWeek()->toDateString(),
            'nextWeek' => $start->copy()->addWeek()->toDateString(),
            'isCurrentWeek' => Carbon::today()->between($start, $end),
        ]);
    }
}
