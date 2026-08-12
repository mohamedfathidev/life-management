<?php

namespace App\Livewire\Activities;

use App\Enums\ActivityStage;
use App\Models\Activity;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public Activity $activity;

    public function mount(Activity $activity): void
    {
        $this->authorize('view', $activity);
        $this->activity = $activity;
    }

    #[On('activity-saved')]
    public function refresh(): void
    {
        $this->activity->refresh();
    }

    public function editActivity(): void
    {
        $this->dispatch('edit-activity', activity: $this->activity->id);
    }

    /** Advance one step along the pipeline. */
    public function advance(): void
    {
        $this->authorize('update', $this->activity);

        if ($next = $this->activity->stage->next()) {
            $extra = $next === ActivityStage::Applied && ! $this->activity->start_date
                ? ['start_date' => now()->toDateString()]
                : [];
            $this->activity->update(array_merge(['stage' => $next->value], $extra));
            $this->activity->refresh();
        }
    }

    public function markRejected(): void
    {
        $this->authorize('update', $this->activity);
        $this->activity->update(['stage' => ActivityStage::Rejected->value]);
        $this->activity->refresh();
    }

    public function reopen(): void
    {
        $this->authorize('update', $this->activity);
        $this->activity->update(['stage' => ActivityStage::Interested->value]);
        $this->activity->refresh();
    }

    public function delete()
    {
        $this->authorize('delete', $this->activity);
        $this->activity->delete();

        return $this->redirectRoute('activities.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.activities.show');
    }
}
