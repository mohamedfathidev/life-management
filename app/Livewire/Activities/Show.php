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

    public ?string $feedback = null;

    public function mount(Activity $activity): void
    {
        $this->authorize('view', $activity);
        $this->activity = $activity;
        $this->feedback = $activity->feedback;
    }

    #[On('activity-saved')]
    public function refresh(): void
    {
        $this->activity->refresh();
        $this->feedback = $this->activity->feedback;
    }

    /** Save my comment + the feedback I got (after interview / rejection). */
    public function saveFeedback(): void
    {
        $this->authorize('update', $this->activity);
        $this->activity->update(['feedback' => $this->feedback ?: null]);
        $this->activity->refresh();
        $this->dispatch('activity-feedback-saved');
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
