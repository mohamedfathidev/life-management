<?php

namespace App\Livewire\Dreams;

use App\Models\Dream;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    #[On('dream-saved')]
    public function refreshList(): void
    {
        //
    }

    public function render(): View
    {
        $dreams = Dream::query()
            ->ownedBy(Auth::user())
            ->orderBy('position')
            ->latest()
            ->get();

        return view('livewire.dreams.index', [
            'dreams' => $dreams,
        ]);
    }
}
