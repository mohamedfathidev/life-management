<?php

namespace App\Livewire\Recovery;

use App\Models\RecoveryDamage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Damages extends Component
{
    use WithPagination;

    #[On('damage-saved')]
    public function refreshList(): void
    {
        //
    }

    public function render(): View
    {
        $damages = RecoveryDamage::query()
            ->ownedBy(Auth::user())
            ->main()
            ->with('children')
            ->orderByDesc('degree')
            ->paginate(12);

        return view('livewire.recovery.damages', [
            'damages' => $damages,
        ]);
    }
}
