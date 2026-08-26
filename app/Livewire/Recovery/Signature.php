<?php

namespace App\Livewire\Recovery;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * "توقيع التغيير" — a fixed personal declaration marking a before/after
 * line in the recovery journey, presented like a signed document rather
 * than an editable form.
 */
#[Layout('layouts.app')]
class Signature extends Component
{
    public function render(): View
    {
        return view('livewire.recovery.signature');
    }
}
