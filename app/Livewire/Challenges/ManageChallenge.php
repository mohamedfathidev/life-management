<?php

namespace App\Livewire\Challenges;

use App\Enums\ChallengeStatus;
use App\Livewire\Forms\ChallengeForm;
use App\Models\Challenge;
use App\Models\Goal;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageChallenge extends Component
{
    public ChallengeForm $form;

    public bool $open = false;

    #[On('create-challenge')]
    public function openForCreate(): void
    {
        $this->resetValidation();
        $this->form->prepareForCreate();
        $this->open = true;
    }

    #[On('edit-challenge')]
    public function openForEdit(Challenge $challenge): void
    {
        $this->authorize('update', $challenge);
        $this->resetValidation();
        $this->form->setChallenge($challenge);
        $this->open = true;
    }

    public function save(): void
    {
        if ($this->form->challenge) {
            $this->authorize('update', $this->form->challenge);
        }

        $this->form->persist(Auth::id());

        $this->open = false;
        $this->dispatch('challenge-saved');
    }

    public function close(): void
    {
        $this->open = false;
        $this->form->reset();
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.challenges.manage-challenge', [
            'statuses' => ChallengeStatus::cases(),
            'goals' => Goal::query()->ownedBy(Auth::user())->orderBy('title')->get(['id', 'title', 'parent_id']),
        ]);
    }
}
