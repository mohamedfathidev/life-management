<?php

namespace App\Livewire\Settings;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class PrivacyPin extends Component
{
    public string $current_pin = '';
    public string $pin = '';
    public string $pin_confirmation = '';

    public function setPin(): void
    {
        $user = Auth::user();

        // Changing an existing PIN requires the current one.
        if ($user->hasPin()) {
            if (! Hash::check($this->current_pin, $user->pin)) {
                $this->addError('current_pin', 'رمز الحماية الحالي غير صحيح.');

                return;
            }
        }

        $this->validate([
            'pin' => ['required', 'digits_between:4,12', 'confirmed'],
        ], attributes: ['pin' => 'رمز الحماية']);

        $user->pin = $this->pin; // hashed via cast
        $user->save();

        // Keep this session unlocked so the user isn't immediately gated.
        session(['privacy_unlocked' => true]);

        $this->reset('current_pin', 'pin', 'pin_confirmation');
        $this->dispatch('pin-updated');
    }

    public function removePin(): void
    {
        $user = Auth::user();

        if (! Hash::check($this->current_pin, $user->pin)) {
            $this->addError('current_pin', 'رمز الحماية الحالي غير صحيح.');

            return;
        }

        $user->pin = null;
        $user->save();
        session()->forget('privacy_unlocked');

        $this->reset('current_pin', 'pin', 'pin_confirmation');
        $this->dispatch('pin-updated');
    }

    public function render(): View
    {
        return view('livewire.settings.privacy-pin', [
            'hasPin' => Auth::user()->hasPin(),
        ]);
    }
}
