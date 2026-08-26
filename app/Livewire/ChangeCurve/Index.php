<?php

namespace App\Livewire\ChangeCurve;

use App\Models\DiaryChange;
use App\Models\Recovery;
use App\Models\RecoveryLog;
use App\Models\Week;
use App\Services\DailyScoreService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * "منحنى التغيير" — a composite day-by-day score built from concrete signals
 * (recovery status, tasks/habits/challenges done, prayers, Quran wird, mental
 * nutrition), not a subjective rating, over a week/month/custom period.
 */
#[Layout('layouts.app')]
class Index extends Component
{
    #[Url]
    public string $period = 'week'; // week | month | custom

    /** Anchor date inside the viewed week/month (Y-m-d). */
    #[Url]
    public string $anchor = '';

    #[Url]
    public string $customFrom = '';

    #[Url]
    public string $customTo = '';

    public function mount(): void
    {
        if ($this->anchor === '') {
            $this->anchor = Carbon::today()->toDateString();
        }
        if ($this->customFrom === '') {
            $this->customFrom = Carbon::today()->subDays(6)->toDateString();
        }
        if ($this->customTo === '') {
            $this->customTo = Carbon::today()->toDateString();
        }
    }

    public function setPeriod(string $period): void
    {
        $this->period = in_array($period, ['week', 'month', 'custom'], true) ? $period : 'week';
    }

    public function shift(int $direction): void
    {
        $anchor = Carbon::parse($this->anchor);
        $anchor = $this->period === 'month' ? $anchor->addMonths($direction) : $anchor->addWeeks($direction);

        $this->anchor = $anchor->toDateString();
    }

    public function goCurrent(): void
    {
        $this->anchor = Carbon::today()->toDateString();
    }

    /** @return array{0: Carbon, 1: Carbon} */
    private function range(): array
    {
        if ($this->period === 'custom') {
            $from = Carbon::parse($this->customFrom);
            $to = Carbon::parse($this->customTo);

            return $to->lt($from) ? [$to, $from] : [$from, $to];
        }

        $anchor = Carbon::parse($this->anchor);

        if ($this->period === 'month') {
            return [$anchor->copy()->startOfMonth(), $anchor->copy()->endOfMonth()];
        }

        [$start, $end] = Week::boundariesFor($anchor); // Sat → Fri

        return [$start, $end];
    }

    public function render(): View
    {
        [$start, $end] = $this->range();
        $user = Auth::user();

        $days = (new DailyScoreService($user))->forRange($start, $end);

        $points = $days->map(fn (array $d) => [
            'label' => $d['date']->format('j'),
            'value' => $d['percent'],
        ])->all();

        $best = $days->isEmpty() ? null : $days->sortByDesc('percent')->first();
        $worst = $days->isEmpty() ? null : $days->sortBy('percent')->first();

        $changes = DiaryChange::where('user_id', $user->id)
            ->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->latest()->get();

        $recoveryIds = Recovery::query()->ownedBy($user)->pluck('id');
        $setbacks = RecoveryLog::query()->whereIn('recovery_id', $recoveryIds)
            ->where('is_setback', true)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->with('recovery:id,title')
            ->orderByDesc('date')
            ->get();

        return view('livewire.change-curve.index', [
            'points' => $points,
            'best' => $best,
            'worst' => $worst,
            'changes' => $changes,
            'setbacks' => $setbacks,
            'rangeLabel' => $this->period === 'month'
                ? $start->translatedFormat('F Y')
                : $start->translatedFormat('j M').' — '.$end->translatedFormat('j M Y'),
        ]);
    }
}
