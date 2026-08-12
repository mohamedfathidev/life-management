<?php

namespace App\Livewire\Activities;

use App\Enums\ActivityType;
use App\Models\Activity;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    #[Url]
    public string $type = ''; // '' = all

    #[Url]
    public string $time = 'all'; // all | upcoming | today | past

    #[On('activity-saved')]
    public function refreshList(): void
    {
        //
    }

    public function resetFilters(): void
    {
        $this->reset(['type', 'time']);
    }

    public function render(): View
    {
        $today = Carbon::today();

        $activities = Activity::query()
            ->ownedBy(Auth::user())
            ->when($this->type !== '', fn ($q) => $q->where('type', $this->type))
            ->orderByRaw("FIELD(stage, 'done', 'rejected') ASC")
            ->orderByDesc('start_date')
            ->latest()
            ->get();

        // Time filter (in PHP — small dataset, readable classification).
        if ($this->time !== 'all') {
            $activities = $activities->filter(function (Activity $a) use ($today) {
                $start = $a->start_date;
                $end = $a->end_date;

                $isToday = ($start && $start->isSameDay($today))
                    || ($end && $end->isSameDay($today))
                    || ($start && $end && $today->between($start, $end));
                $isUpcoming = $start && $start->gt($today);
                $isPast = ($end && $end->lt($today)) || (! $end && $start && $start->lt($today));

                return match ($this->time) {
                    'today' => $isToday,
                    'upcoming' => $isUpcoming,
                    'past' => $isPast,
                    default => true,
                };
            })->values();
        }

        return view('livewire.activities.index', [
            'activities' => $activities,
            'types' => ActivityType::cases(),
        ]);
    }
}
