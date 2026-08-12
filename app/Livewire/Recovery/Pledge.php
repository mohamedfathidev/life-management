<?php

namespace App\Livewire\Recovery;

use App\Models\RecoveryPledge;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * "تعهد أمام الله" — the user's personal commitment pledge (one per user),
 * a free-text vow they write for themselves as part of their recovery.
 */
#[Layout('layouts.app')]
class Pledge extends Component
{
    public string $body = '';

    public bool $editing = false;

    public function mount(): void
    {
        $pledge = RecoveryPledge::ownedBy(Auth::user())->first();

        $this->body = $pledge?->body ?? '';
        $this->editing = blank($this->body);
    }

    public function save(): void
    {
        $data = $this->validate([
            'body' => ['required', 'string', 'max:5000'],
        ], [], ['body' => 'التعهد']);

        RecoveryPledge::updateOrCreate(
            ['user_id' => Auth::id()],
            ['body' => $data['body']],
        );

        $this->editing = false;
        $this->dispatch('pledge-saved');
    }

    public function edit(): void
    {
        $this->editing = true;
    }

    public function render(): View
    {
        $pledge = RecoveryPledge::ownedBy(Auth::user())->first();

        return view('livewire.recovery.pledge', [
            'savedAt' => $pledge?->updated_at,
            'hasPledge' => filled($pledge?->body),
        ]);
    }
}
