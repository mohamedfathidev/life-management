<?php

namespace App\Livewire\Recovery;

use App\Models\RecoveryPrisonReflection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * "ماذا لو لم أدخل هذا السجن؟" — one reflection per user (mirrors Pledge):
 * how many years the cycle has cost, and what they'd write, in their own
 * words, about the life they might have had without it.
 */
#[Layout('layouts.app')]
class Prison extends Component
{
    public ?int $prisonYears = null;

    public string $body = '';

    public bool $editing = false;

    public function mount(): void
    {
        $reflection = RecoveryPrisonReflection::ownedBy(Auth::user())->first();

        $this->prisonYears = $reflection?->prison_years;
        $this->body = $reflection?->body ?? '';
        $this->editing = blank($this->body);
    }

    public function save(): void
    {
        $data = $this->validate([
            'prisonYears' => ['nullable', 'integer', 'between:0,80'],
            'body' => ['required', 'string', 'max:5000'],
        ], [], [
            'prisonYears' => 'عدد السنين',
            'body' => 'النص',
        ]);

        RecoveryPrisonReflection::updateOrCreate(
            ['user_id' => Auth::id()],
            ['prison_years' => $data['prisonYears'], 'body' => $data['body']],
        );

        $this->editing = false;
        $this->dispatch('prison-reflection-saved');
    }

    public function edit(): void
    {
        $this->editing = true;
    }

    public function render(): View
    {
        $reflection = RecoveryPrisonReflection::ownedBy(Auth::user())->first();

        return view('livewire.recovery.prison', [
            'savedAt' => $reflection?->updated_at,
            'hasReflection' => filled($reflection?->body),
        ]);
    }
}
