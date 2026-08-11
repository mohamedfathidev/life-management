<?php

namespace App\Livewire\Jobs;

use App\Enums\JobStage;
use App\Models\JobApplication;
use App\Models\JobPrepItem;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public JobApplication $job;

    public string $research = '';
    public string $rejectionReason = '';
    public string $newPrepItem = '';

    public function mount(JobApplication $job): void
    {
        $this->authorize('view', $job);
        $this->job = $job;
        $this->research = (string) $job->company_research;
        $this->rejectionReason = (string) $job->rejection_reason;
    }

    #[On('job-saved')]
    public function refreshJob(): void
    {
        $this->job->refresh();
        $this->research = (string) $this->job->company_research;
        $this->rejectionReason = (string) $this->job->rejection_reason;
    }

    // --- Stage advancement -------------------------------------------------

    private function moveTo(JobStage $stage, array $extra = []): void
    {
        $this->authorize('update', $this->job);
        $this->job->update(array_merge(['stage' => $stage->value], $extra));
        $this->job->refresh();
    }

    public function markApplied(): void
    {
        $this->moveTo(JobStage::Applied, [
            'applied_on' => $this->job->applied_on ?? Carbon::today()->toDateString(),
        ]);
    }

    public function markInterview(): void
    {
        $this->moveTo(JobStage::Interview);
    }

    public function markOffer(): void
    {
        $this->moveTo(JobStage::Offer);
    }

    public function markRejected(): void
    {
        $this->moveTo(JobStage::Rejected);
    }

    public function reopen(): void
    {
        $this->moveTo(JobStage::Applied);
    }

    public function saveRejectionReason(): void
    {
        $this->authorize('update', $this->job);
        $this->job->update(['rejection_reason' => $this->rejectionReason ?: null]);
        $this->job->refresh();
    }

    // --- Interview prep ----------------------------------------------------

    public function saveResearch(): void
    {
        $this->authorize('update', $this->job);
        $this->job->update(['company_research' => $this->research ?: null]);
        $this->job->refresh();
    }

    public function addPrepItem(): void
    {
        $this->authorize('update', $this->job);
        $title = trim($this->newPrepItem);

        if ($title !== '') {
            $this->job->prepItems()->create([
                'title' => $title,
                'position' => (int) $this->job->prepItems()->max('position') + 1,
            ]);
            $this->newPrepItem = '';
            $this->job->refresh();
        }
    }

    public function togglePrepItem(int $itemId): void
    {
        $this->authorize('update', $this->job);
        $item = JobPrepItem::where('job_application_id', $this->job->id)->findOrFail($itemId);
        $item->update(['is_done' => ! $item->is_done]);
        $this->job->refresh();
    }

    public function deletePrepItem(int $itemId): void
    {
        $this->authorize('update', $this->job);
        JobPrepItem::where('job_application_id', $this->job->id)->where('id', $itemId)->delete();
        $this->job->refresh();
    }

    // --- Misc --------------------------------------------------------------

    public function editJob(): void
    {
        $this->dispatch('edit-job', job: $this->job->id);
    }

    public function delete()
    {
        $this->authorize('delete', $this->job);
        $this->job->delete();

        return $this->redirectRoute('jobs.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.jobs.show', [
            'steps' => JobStage::steps($this->job->stage, $this->job->applied_on),
            'prepItems' => $this->job->prepItems()->get(),
        ]);
    }
}
