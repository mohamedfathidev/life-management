<?php

namespace App\Livewire\Scholarships;

use App\Enums\ScholarshipStage;
use App\Models\Scholarship;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public Scholarship $scholarship;

    public string $rejectionReason = '';

    public function mount(Scholarship $scholarship): void
    {
        $this->authorize('view', $scholarship);
        $this->scholarship = $scholarship;
        $this->rejectionReason = (string) $scholarship->rejection_reason;
    }

    #[On('scholarship-saved')]
    public function refreshScholarship(): void
    {
        $this->scholarship->refresh();
        $this->rejectionReason = (string) $this->scholarship->rejection_reason;
    }

    // --- Stage advancement -------------------------------------------------

    private function moveTo(ScholarshipStage $stage, array $extra = []): void
    {
        $this->authorize('update', $this->scholarship);
        $this->scholarship->update(array_merge(['stage' => $stage->value], $extra));
        $this->scholarship->refresh();
    }

    public function markSubmitted(): void
    {
        $this->moveTo(ScholarshipStage::Submitted, [
            'submitted_on' => $this->scholarship->submitted_on ?? Carbon::today()->toDateString(),
        ]);
    }

    public function markWaiting(): void
    {
        $this->moveTo(ScholarshipStage::Waiting);
    }

    public function markAccepted(): void
    {
        $this->moveTo(ScholarshipStage::Accepted, [
            'decided_on' => $this->scholarship->decided_on ?? Carbon::today()->toDateString(),
        ]);
    }

    public function markRejected(): void
    {
        $this->moveTo(ScholarshipStage::Rejected, [
            'decided_on' => $this->scholarship->decided_on ?? Carbon::today()->toDateString(),
        ]);
    }

    public function reopen(): void
    {
        $this->moveTo(ScholarshipStage::Waiting, ['decided_on' => null]);
    }

    public function saveRejectionReason(): void
    {
        $this->authorize('update', $this->scholarship);
        $this->scholarship->update(['rejection_reason' => $this->rejectionReason ?: null]);
        $this->scholarship->refresh();
    }

    // --- Misc --------------------------------------------------------------

    public function editScholarship(): void
    {
        $this->dispatch('edit-scholarship', scholarship: $this->scholarship->id);
    }

    public function delete()
    {
        $this->authorize('delete', $this->scholarship);
        $this->scholarship->delete();

        return $this->redirectRoute('scholarships.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.scholarships.show', [
            'steps' => ScholarshipStage::steps(
                $this->scholarship->stage,
                $this->scholarship->submitted_on,
                $this->scholarship->decided_on,
            ),
        ]);
    }
}
