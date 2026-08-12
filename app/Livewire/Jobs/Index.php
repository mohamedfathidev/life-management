<?php

namespace App\Livewire\Jobs;

use App\Enums\JobStage;
use App\Models\JobApplication;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    #[Url]
    public string $stage = ''; // '' = all

    #[On('job-saved')]
    public function refreshList(): void
    {
        //
    }

    public function resetFilters(): void
    {
        $this->reset('stage');
    }

    public function render(): View
    {
        $jobs = JobApplication::query()
            ->ownedBy(Auth::user())
            ->when($this->stage !== '', fn ($q) => $q->where('stage', $this->stage))
            ->orderByRaw("FIELD(stage, 'offer', 'rejected') ASC") // open ones first
            ->latest()
            ->get();

        return view('livewire.jobs.index', [
            'jobs' => $jobs,
            'stages' => JobStage::cases(),
        ]);
    }
}
