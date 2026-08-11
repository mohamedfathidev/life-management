<?php

namespace App\Livewire\Goals;

use App\Enums\GoalStatus;
use App\Models\Goal;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $statusFilter = '';

    /** Re-render the list after a goal is created or updated elsewhere. */
    #[On('goal-saved')]
    public function refreshList(): void
    {
        // Attribute presence is enough to trigger a re-render.
    }

    public function delete(Goal $goal): void
    {
        $this->authorize('delete', $goal);
        $goal->delete();

        $this->dispatch('goal-saved');
    }

    public function render(): View
    {
        $goals = Goal::query()
            ->ownedBy(Auth::user())
            ->topLevel()
            ->withCount('children')
            ->when(
                $this->statusFilter !== '',
                fn ($q) => $q->where('status', $this->statusFilter),
            )
            ->latest()
            ->paginate(12);

        return view('livewire.goals.index', [
            'goals' => $goals,
            'statuses' => GoalStatus::cases(),
        ]);
    }
}
