<?php

namespace App\Livewire\Scholarships;

use App\Models\Scholarship;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    #[On('scholarship-saved')]
    public function refreshList(): void
    {
        //
    }

    public function render(): View
    {
        $scholarships = Scholarship::query()
            ->ownedBy(Auth::user())
            ->orderByRaw('apply_to is null') // ones with a deadline first
            ->orderBy('apply_to')
            ->latest()
            ->get();

        return view('livewire.scholarships.index', [
            'scholarships' => $scholarships,
        ]);
    }
}
