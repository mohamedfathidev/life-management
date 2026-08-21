<?php

namespace App\Livewire\Recovery;

use App\Models\RecoveryDamage;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DamageShow extends Component
{
    public RecoveryDamage $damage;

    public function mount(RecoveryDamage $damage): void
    {
        $this->authorize('view', $damage);
        $this->damage = $damage->load('children');
    }

    public function render(): View
    {
        return view('livewire.recovery.damage-show');
    }
}
