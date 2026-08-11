<?php

namespace App\Livewire\Statistics;

use App\Models\GoalReview;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    public function render(): View
    {
        // Closed big-goal reviews in chronological order.
        $reviews = GoalReview::query()
            ->where('user_id', Auth::id())
            ->whereHas('goal', fn ($q) => $q->whereNull('parent_id'))
            ->with('goal:id,title,color')
            ->orderBy('closed_on')
            ->orderBy('id')
            ->get();

        // Compare each goal's improvement to the previously-closed goal.
        $previous = null;
        $rows = $reviews->map(function (GoalReview $review) use (&$previous) {
            $delta = $previous === null ? null : $review->improvement_percent - $previous;
            $trend = match (true) {
                $previous === null => 'first',
                $delta > 0 => 'up',
                $delta < 0 => 'down',
                default => 'same',
            };
            $previous = $review->improvement_percent;

            return ['review' => $review, 'delta' => $delta, 'trend' => $trend];
        });

        return view('livewire.statistics.index', [
            // newest first for display; trend already computed chronologically
            'rows' => $rows->reverse()->values(),
            'averageImprovement' => $reviews->isNotEmpty()
                ? (int) round($reviews->avg('improvement_percent'))
                : null,
            // chronological points for the improvement-trend chart
            'chartPoints' => $reviews->map(fn (GoalReview $r) => [
                'label' => $r->goal->title,
                'value' => $r->improvement_percent,
            ])->all(),
        ]);
    }
}
