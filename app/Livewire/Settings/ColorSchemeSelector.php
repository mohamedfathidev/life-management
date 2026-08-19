<?php

namespace App\Livewire\Settings;

use App\Enums\ColorScheme;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class ColorSchemeSelector extends Component
{
    public string $colorScheme = 'default';

    public function mount(): void
    {
        $this->colorScheme = Auth::user()->color_scheme?->value ?? ColorScheme::Default->value;
    }

    /** Apply the chosen color scheme instantly */
    public function setColorScheme(string $scheme): void
    {
        $colorScheme = ColorScheme::tryFrom($scheme) ?? ColorScheme::Default;

        $user = Auth::user();
        $user->color_scheme = $colorScheme;
        $user->save();

        $this->colorScheme = $colorScheme->value;

        // Dispatch event so JS can update the HTML attribute instantly
        $this->dispatch('color-scheme-changed', scheme: $colorScheme->value);
    }

    public function render(): View
    {
        return view('livewire.settings.color-scheme-selector', [
            'schemes' => ColorScheme::cases(),
        ]);
    }
}
