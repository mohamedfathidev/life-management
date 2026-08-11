<?php

namespace App\Livewire\MarketStudy;

use App\Models\StudyTrack;
use App\Services\PlannerService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public StudyTrack $track;

    public function mount(StudyTrack $track): void
    {
        $this->authorize('view', $track);
        $this->track = $track;
    }

    #[On('track-saved')]
    #[On('task-saved')]
    public function refreshTrack(): void
    {
        $this->track->refresh();
    }

    public function editTrack(): void
    {
        $this->dispatch('edit-track', track: $this->track->id);
    }

    public function addStudyTask(PlannerService $planner): void
    {
        // Study tasks go straight into today's plan, not the backlog pool.
        $day = $planner->resolveDay(Auth::user(), Carbon::today());

        $this->dispatch('create-task', dayId: $day->id, studyTrackId: $this->track->id);
    }

    public function toggleCompleted(): void
    {
        $this->authorize('update', $this->track);
        $this->track->update(['is_completed' => ! $this->track->is_completed]);
        $this->track->refresh();
    }

    public function delete()
    {
        $this->authorize('delete', $this->track);
        $this->track->delete();

        return $this->redirectRoute('market-study.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.market-study.show', [
            'tasks' => $this->track->tasks()->with('day')->latest()->get(),
        ]);
    }
}
