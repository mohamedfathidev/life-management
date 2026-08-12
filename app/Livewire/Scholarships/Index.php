<?php

namespace App\Livewire\Scholarships;

use App\Enums\ScholarshipStage;
use App\Models\Scholarship;
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

    #[On('scholarship-saved')]
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
        $scholarships = Scholarship::query()
            ->ownedBy(Auth::user())
            ->when($this->stage !== '', fn ($q) => $q->where('stage', $this->stage))
            ->orderByRaw('apply_to is null') // ones with a deadline first
            ->orderBy('apply_to')
            ->latest()
            ->get();

        return view('livewire.scholarships.index', [
            'scholarships' => $scholarships,
            'stages' => ScholarshipStage::cases(),
        ]);
    }
}
