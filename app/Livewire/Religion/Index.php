<?php

namespace App\Livewire\Religion;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    public function render(): View
    {
        return view('livewire.religion.index');
    }
}
