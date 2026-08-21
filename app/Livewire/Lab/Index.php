<?php

namespace App\Livewire\Lab;

use App\Enums\ProjectStatus;
use App\Models\Project;
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
    public string $status = ''; // '' = all

    #[On('project-saved')]
    public function refreshList(): void
    {
        //
    }

    public function resetFilters(): void
    {
        $this->reset(['status']);
    }

    public function render(): View
    {
        $projects = Project::query()
            ->ownedBy(Auth::user())
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
            ->withCount(['steps', 'steps as done_steps_count' => fn ($q) => $q->where('is_done', true)])
            ->latest()
            ->get();

        return view('livewire.lab.index', [
            'projects' => $projects,
            'statuses' => ProjectStatus::cases(),
        ]);
    }
}
