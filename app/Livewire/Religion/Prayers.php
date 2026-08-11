<?php

namespace App\Livewire\Religion;

use App\Enums\PrayerState;
use App\Models\PrayerDay;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Prayers extends Component
{
    /** Set a prayer's state for today. */
    public function setPrayer(string $prayer, string $state): void
    {
        if (! in_array($prayer, PrayerDay::PRAYERS, true)) {
            return;
        }

        $state = PrayerState::tryFrom($state) ?? PrayerState::None;

        $day = PrayerDay::firstOrCreate(
            ['user_id' => Auth::id(), 'date' => Carbon::today()->toDateString()],
        );
        $day->update([$prayer => $state->value]);
    }

    public function render(): View
    {
        $today = PrayerDay::firstOrCreate(
            ['user_id' => Auth::id(), 'date' => Carbon::today()->toDateString()],
        );

        // Current-month stats up to today.
        $monthStart = Carbon::today()->startOfMonth();
        $daysElapsed = Carbon::today()->day;

        $monthDays = PrayerDay::query()
            ->ownedBy(Auth::user())
            ->whereBetween('date', [$monthStart->toDateString(), Carbon::today()->toDateString()])
            ->get();

        $doneTotal = $monthDays->sum(fn (PrayerDay $d) => $d->doneCount());
        $onTimeTotal = $monthDays->sum(fn (PrayerDay $d) => $d->onTimeCount());
        $possible = max(1, $daysElapsed * 5);

        return view('livewire.religion.prayers', [
            'today' => $today,
            'prayers' => PrayerDay::PRAYERS,
            'labels' => PrayerDay::LABELS,
            'states' => PrayerState::cases(),
            'completionPercent' => (int) round($doneTotal / $possible * 100),
            'onTimePercent' => (int) round($onTimeTotal / $possible * 100),
            'daysElapsed' => $daysElapsed,
        ]);
    }
}
