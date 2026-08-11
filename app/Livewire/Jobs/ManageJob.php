<?php

namespace App\Livewire\Jobs;

use App\Enums\JobStage;
use App\Livewire\Forms\JobForm;
use App\Models\Goal;
use App\Models\JobApplication;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageJob extends Component
{
    public JobForm $form;

    public bool $open = false;

    #[On('create-job')]
    public function openForCreate(): void
    {
        $this->form->reset();
        $this->resetValidation();
        $this->open = true;
    }

    #[On('edit-job')]
    public function openForEdit(JobApplication $job): void
    {
        $this->authorize('update', $job);
        $this->resetValidation();
        $this->form->setJob($job);
        $this->open = true;
    }

    public function save(): void
    {
        if ($this->form->job) {
            $this->authorize('update', $this->form->job);
        }

        $this->form->persist(Auth::id());

        $this->open = false;
        $this->dispatch('job-saved');
    }

    public function close(): void
    {
        $this->open = false;
        $this->form->reset();
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.jobs.manage-job', [
            'stages' => JobStage::cases(),
            'goals' => Goal::query()->ownedBy(Auth::user())->orderBy('title')->get(['id', 'title', 'parent_id']),
        ]);
    }
}
