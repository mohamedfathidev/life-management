<?php

namespace App\Livewire\Scholarships;

use App\Enums\ScholarshipStage;
use App\Models\VolunteerActivity;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Volunteering extends Component
{
    #[Url]
    public string $tab = 'current'; // 'current' | 'applications'

    #[On('volunteer-saved')]
    public function refreshList(): void
    {
        //
    }

    // --- Pipeline advancement ---------------------------------------------

    private function advance(int $id, ScholarshipStage $stage, array $extra = []): void
    {
        $activity = VolunteerActivity::ownedBy(Auth::user())->findOrFail($id);
        $this->authorize('update', $activity);
        $activity->update(array_merge(['stage' => $stage->value], $extra));
    }

    public function markSubmitted(int $id): void
    {
        $activity = VolunteerActivity::ownedBy(Auth::user())->findOrFail($id);
        $this->authorize('update', $activity);
        $activity->update([
            'stage' => ScholarshipStage::Submitted->value,
            'submitted_on' => $activity->submitted_on ?? Carbon::today()->toDateString(),
        ]);
    }

    public function markWaiting(int $id): void
    {
        $this->advance($id, ScholarshipStage::Waiting);
    }

    public function markInterview(int $id): void
    {
        $this->advance($id, ScholarshipStage::Interview);
    }

    public function markAccepted(int $id): void
    {
        $activity = VolunteerActivity::ownedBy(Auth::user())->findOrFail($id);
        $this->authorize('update', $activity);
        $activity->update([
            'stage' => ScholarshipStage::Accepted->value,
            'decided_on' => $activity->decided_on ?? Carbon::today()->toDateString(),
        ]);
    }

    public function markRejected(int $id): void
    {
        $activity = VolunteerActivity::ownedBy(Auth::user())->findOrFail($id);
        $this->authorize('update', $activity);
        $activity->update([
            'stage' => ScholarshipStage::Rejected->value,
            'decided_on' => $activity->decided_on ?? Carbon::today()->toDateString(),
        ]);
    }

    public function render(): View
    {
        $applications = VolunteerActivity::query()
            ->ownedBy(Auth::user())
            ->applications()
            ->latest()
            ->get();

        $current = VolunteerActivity::query()
            ->ownedBy(Auth::user())
            ->current()
            ->latest()
            ->get();

        return view('livewire.scholarships.volunteering', [
            'applications' => $applications,
            'current' => $current,
            'totalHours' => (int) $current->sum('hours'),
        ]);
    }
}
