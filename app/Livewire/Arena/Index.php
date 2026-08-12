<?php

namespace App\Livewire\Arena;

use App\Models\SharedChallenge;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.arena')]
class Index extends Component
{
    public string $joinCode = '';

    /** Prefilled from an invite link (?code=XXX). */
    #[Url]
    public ?string $code = null;

    public function mount(): void
    {
        if ($this->code) {
            $this->joinCode = $this->code;
        }
    }

    public function join()
    {
        $code = Str::upper(trim($this->joinCode));

        $challenge = SharedChallenge::where('join_code', $code)->first();

        if (! $challenge) {
            $this->addError('joinCode', 'الكود غير صحيح.');

            return;
        }

        $user = Auth::user();
        if (! $challenge->isJoinedBy($user)) {
            $challenge->participants()->attach($user->id);
        }

        return $this->redirectRoute('arena.challenges.show', $challenge, navigate: true);
    }

    public function render(): View
    {
        $user = Auth::user();

        return view('livewire.arena.index', [
            'isOwner' => $user->isOwner(),
            'joined' => $user->sharedChallenges()->withCount('participants')->latest()->get(),
        ]);
    }
}
