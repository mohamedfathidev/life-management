<?php

namespace App\Livewire\Review;

use App\Models\Week;
use App\Services\ReviewService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    #[Url]
    public string $period = 'week'; // week | month

    /** Anchor date inside the viewed period (Y-m-d). */
    #[Url]
    public string $anchor = '';

    public function mount(): void
    {
        if ($this->anchor === '') {
            $this->anchor = Carbon::today()->toDateString();
        }
    }

    public function setPeriod(string $period): void
    {
        $this->period = $period === 'month' ? 'month' : 'week';
    }

    public function shift(int $direction): void
    {
        $anchor = Carbon::parse($this->anchor);
        $anchor = $this->period === 'month'
            ? $anchor->addMonths($direction)
            : $anchor->addWeeks($direction);

        $this->anchor = $anchor->toDateString();
    }

    public function goCurrent(): void
    {
        $this->anchor = Carbon::today()->toDateString();
    }

    /** @return array{0: Carbon, 1: Carbon} */
    private function range(): array
    {
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

        $summary = (new ReviewService(Auth::user(), $start, $end))->summary();

        return view('livewire.review.index', [
            'summary' => $summary,
            'rangeLabel' => $this->period === 'month'
                ? $start->translatedFormat('F Y')
                : $start->translatedFormat('j M').' — '.$end->translatedFormat('j M Y'),
        ]);
    }
}
