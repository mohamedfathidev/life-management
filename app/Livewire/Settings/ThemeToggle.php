<?php

namespace App\Livewire\Settings;

use App\Enums\Theme;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ThemeToggle extends Component
{
    public string $theme = 'system';

    public function mount(): void
    {
        $this->theme = Auth::user()->theme?->value ?? Theme::System->value;
    }

    /** Persist the chosen theme and let the browser apply it instantly. */
    public function setTheme(string $theme): void
    {
        $theme = Theme::tryFrom($theme) ?? Theme::System;

        $user = Auth::user();
        $user->theme = $theme;
        $user->save();

        $this->theme = $theme->value;

        $this->dispatch('theme-changed', theme: $theme->value);
    }

    public function render()
    {
        return view('livewire.settings.theme-toggle', [
            'themes' => Theme::cases(),
        ]);
    }
}
