<?php

namespace App\Livewire\Recovery;

use App\Models\Recovery;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    #[On('recovery-saved')]
    public function refreshList(): void
    {
        //
    }

    public function render(): View
    {
        $recoveries = Recovery::query()
            ->ownedBy(Auth::user())
            ->latest()
            ->get();

        return view('livewire.recovery.index', [
            'recoveries' => $recoveries,
        ]);
    }
}
