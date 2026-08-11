<?php

namespace App\Livewire\Privacy;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Unlock extends Component
{
    public string $pin = '';

    public function mount(): void
    {
        // Nothing to unlock if there is no PIN or already unlocked.
        if (! Auth::user()->hasPin() || session('privacy_unlocked')) {
            $this->redirectIntended(route('dashboard'), navigate: false);
        }
    }

    public function unlock(): void
    {
        $this->validate(
            ['pin' => ['required', 'string']],
            attributes: ['pin' => 'رمز الحماية'],
        );

        if (! Hash::check($this->pin, Auth::user()->pin)) {
            $this->addError('pin', 'رمز الحماية غير صحيح.');

            return;
        }

        session(['privacy_unlocked' => true]);

        $this->redirectIntended(route('dashboard'), navigate: false);
    }

    public function render(): View
    {
        return view('livewire.privacy.unlock');
    }
}
