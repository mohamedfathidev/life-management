<?php

namespace App\Livewire\Religion;

use App\Enums\PrayerState;
use App\Models\PrayerDay;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Prayers extends Component
{
    /** The day being viewed/logged (Y-m-d) — lets you go back and fix a missed day. */
    #[Url]
    public string $date = '';

    public function mount(): void
    {
        if ($this->date === '') {
            $this->date = Carbon::today()->toDateString();
        }
    }

    /** Move to another day (never into the future). */
    public function changeDay(int $dir): void
    {
        $new = Carbon::parse($this->date)->addDays($dir);
        if ($new->gt(Carbon::today())) {
            return;
        }
        $this->date = $new->toDateString();
    }

    public function goToday(): void
    {
        $this->date = Carbon::today()->toDateString();
    }

    /** Set a prayer's state for the viewed day. */
    public function setPrayer(string $prayer, string $state): void
    {
        if (! in_array($prayer, PrayerDay::PRAYERS, true)) {
            return;
        }

        $state = PrayerState::tryFrom($state) ?? PrayerState::None;

        $day = PrayerDay::firstOrCreate(['user_id' => Auth::id(), 'date' => $this->date]);
        $day->update([$prayer => $state->value]);
    }

    public function render(): View
    {
        $today = PrayerDay::firstOrCreate(['user_id' => Auth::id(), 'date' => $this->date]);

        // Current-month stats up to today.
        $monthStart = Carbon::today()->startOfMonth();
        $daysElapsed = Carbon::today()->day;

        $monthDays = PrayerDay::query()
            ->ownedBy(Auth::user())
            ->whereBetween('date', [$monthStart->toDateString(), Carbon::today()->toDateString()])
            ->get();

        $doneTotal = $monthDays->sum(fn (PrayerDay $d) => $d->doneCount());
        $onTimeTotal = $monthDays->sum(fn (PrayerDay $d) => $d->onTimeCount());
        $jamaahTotal = $monthDays->sum(fn (PrayerDay $d) => $d->jamaahCount());
        $possible = max(1, $daysElapsed * 5);

        return view('livewire.religion.prayers', [
            'today' => $today,
            'prayers' => PrayerDay::PRAYERS,
            'labels' => PrayerDay::LABELS,
            'states' => PrayerState::cases(),
            'completionPercent' => (int) round($doneTotal / $possible * 100),
            'onTimePercent' => (int) round($onTimeTotal / $possible * 100),
            'jamaahPercent' => (int) round($jamaahTotal / $possible * 100),
            'daysElapsed' => $daysElapsed,
            'dateLabel' => Carbon::parse($this->date)->translatedFormat('l، j F Y'),
            'isToday' => $this->date === Carbon::today()->toDateString(),
        ]);
    }
}
