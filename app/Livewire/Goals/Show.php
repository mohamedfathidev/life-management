<?php

namespace App\Livewire\Goals;

use App\Models\Goal;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public Goal $goal;

    #[Url]
    public string $tab = 'overview';

    /** Tabs available on the goal detail page. Stubbed ones fill in later phases. */
    public array $tabs = [
        'overview' => 'نظرة عامة',
        'logs' => 'السجلات اليومية',
        'challenges' => 'التحديات',
        'module' => 'الوحدة المرتبطة',
        'timeline' => 'الخط الزمني',
    ];

    public function mount(Goal $goal): void
    {
        $this->authorize('view', $goal);
        $this->goal = $goal;
    }

    #[On('goal-saved')]
    public function refreshGoal(): void
    {
        $this->goal->refresh();
    }

    public function editGoal(): void
    {
        $this->dispatch('edit-goal', goal: $this->goal->id);
    }

    public function addSubGoal(): void
    {
        $this->dispatch('create-goal', parentId: $this->goal->id);
    }

    public function closeGoal(): void
    {
        $this->dispatch('close-goal', goal: $this->goal->id);
    }

    public function render(): View
    {
        $children = $this->goal->children()
            ->withCount('children')
            ->latest()
            ->get();

        return view('livewire.goals.show', [
            'children' => $children,
            'parent' => $this->goal->parent,
        ]);
    }
}
