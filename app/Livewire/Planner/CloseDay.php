<?php

namespace App\Livewire\Planner;

use App\Models\Day;
use App\Services\DayService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class CloseDay extends Component
{
    public ?Day $day = null;

    public bool $open = false;

    public ?int $rating = null;

    public ?string $reflection = null;

    /** taskId => 'carry' | 'pool' */
    public array $decisions = [];

    #[On('close-day')]
    public function openFor(Day $day): void
    {
        $this->authorize('update', $day);
        $this->resetValidation();

        $this->day = $day;
        $this->rating = $day->rating;
        $this->reflection = $day->reflection;

        // Default every unfinished task to "carry to next day".
        $this->decisions = $this->incompleteTasks()
            ->mapWithKeys(fn ($task) => [$task->id => 'carry'])
            ->all();

        $this->open = true;
    }

    /** @return Collection<int, \App\Models\Task> */
    public function incompleteTasks(): Collection
    {
        if (! $this->day) {
            return collect();
        }

        return $this->day->tasks()->incomplete()->get();
    }

    public function save(DayService $service): void
    {
        $this->authorize('update', $this->day);

        $this->validate([
            'rating' => ['required', 'integer', 'between:1,10'],
            'reflection' => ['nullable', 'string', 'max:5000'],
            'decisions.*' => [Rule::in(['carry', 'pool'])],
        ], attributes: ['rating' => 'التقييم', 'reflection' => 'انعكاس اليوم']);

        $service->close($this->day, $this->rating, $this->reflection, $this->decisions);

        $this->open = false;
        $this->dispatch('day-updated');
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function render(): View
    {
        return view('livewire.planner.close-day', [
            'incompleteTasks' => $this->incompleteTasks(),
        ]);
    }
}
