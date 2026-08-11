<?php

namespace App\Livewire\Jobs;

use App\Models\JobApplication;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    #[On('job-saved')]
    public function refreshList(): void
    {
        //
    }

    public function render(): View
    {
        $jobs = JobApplication::query()
            ->ownedBy(Auth::user())
            ->latest()
            ->get();

        return view('livewire.jobs.index', [
            'jobs' => $jobs,
        ]);
    }
}
