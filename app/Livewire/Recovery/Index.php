<?php

namespace App\Livewire\Recovery;

use App\Models\Recovery;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

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
            ->paginate(12);

        return view('livewire.recovery.index', [
            'recoveries' => $recoveries,
        ]);
    }
}
