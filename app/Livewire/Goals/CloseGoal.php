<?php

namespace App\Livewire\Goals;

use App\Enums\GoalStatus;
use App\Models\Goal;
use App\Models\GoalReview;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class CloseGoal extends Component
{
    public ?Goal $goal = null;

    public bool $open = false;

    /** @var array<int, string> */
    public array $shortcomings = [''];

    /** @var array<int, string> */
    public array $strengths = [''];

    public int $improvement = 0;

    public ?string $note = null;

    #[On('close-goal')]
    public function openFor(Goal $goal): void
    {
        $this->authorize('update', $goal);

        // Only big goals, from their last day onward.
        if (! $goal->canClose()) {
            return;
        }

        $this->resetValidation();
        $this->goal = $goal;
        $this->shortcomings = [''];
        $this->strengths = [''];
        $this->improvement = 0;
        $this->note = null;
        $this->open = true;
    }

    public function addShortcoming(): void
    {
        $this->shortcomings[] = '';
    }

    public function removeShortcoming(int $index): void
    {
        unset($this->shortcomings[$index]);
        $this->shortcomings = array_values($this->shortcomings);
    }

    public function addStrength(): void
    {
        $this->strengths[] = '';
    }

    public function removeStrength(int $index): void
    {
        unset($this->strengths[$index]);
        $this->strengths = array_values($this->strengths);
    }

    public function save(): void
    {
        $this->authorize('update', $this->goal);

        $this->validate([
            'improvement' => ['required', 'integer', 'between:0,100'],
            'note' => ['nullable', 'string', 'max:5000'],
            'shortcomings.*' => ['nullable', 'string', 'max:500'],
            'strengths.*' => ['nullable', 'string', 'max:500'],
        ], attributes: ['improvement' => 'نسبة التحسن']);

        DB::transaction(function () {
            GoalReview::updateOrCreate(
                ['goal_id' => $this->goal->id],
                [
                    'user_id' => Auth::id(),
                    'improvement_percent' => $this->improvement,
                    'shortcomings' => $this->cleanList($this->shortcomings),
                    'strengths' => $this->cleanList($this->strengths),
                    'note' => $this->note,
                    'closed_on' => Carbon::today()->toDateString(),
                ],
            );

            $this->goal->update(['status' => GoalStatus::Completed->value]);
        });

        $this->open = false;
        $this->dispatch('goal-saved');
    }

    public function close(): void
    {
        $this->open = false;
    }

    /**
     * @param  array<int, string>  $items
     * @return array<int, string>
     */
    private function cleanList(array $items): array
    {
        return array_values(array_filter(array_map('trim', $items), fn ($v) => $v !== ''));
    }

    public function render(): View
    {
        return view('livewire.goals.close-goal');
    }
}
