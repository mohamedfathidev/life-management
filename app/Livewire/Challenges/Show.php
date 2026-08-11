<?php

namespace App\Livewire\Challenges;

use App\Enums\ChallengeStatus;
use App\Models\Challenge;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public Challenge $challenge;

    public function mount(Challenge $challenge): void
    {
        $this->authorize('view', $challenge);
        $this->challenge = $challenge;
    }

    #[On('challenge-saved')]
    public function refreshChallenge(): void
    {
        $this->challenge->refresh();
    }

    public function toggle(string $date): void
    {
        $this->authorize('update', $this->challenge);

        $existing = $this->challenge->logs()->whereDate('date', $date)->first();
        if ($existing) {
            $existing->delete();
        } else {
            $this->challenge->logs()->create(['date' => $date]);
        }
        $this->challenge->refresh();
    }

    public function setStatus(string $status): void
    {
        $this->authorize('update', $this->challenge);
        $status = ChallengeStatus::tryFrom($status) ?? ChallengeStatus::Active;
        $this->challenge->update(['status' => $status->value]);
        $this->challenge->refresh();
    }

    public function editChallenge(): void
    {
        $this->dispatch('edit-challenge', challenge: $this->challenge->id);
    }

    public function delete()
    {
        $this->authorize('delete', $this->challenge);
        $this->challenge->delete();

        return $this->redirectRoute('challenges.index', navigate: true);
    }

    public function render(): View
    {
        $today = Carbon::today();
        $start = $this->challenge->start_date->copy();
        $end = $this->challenge->endDate();

        $this->challenge->load('logs');
        $done = $this->challenge->logs->map(fn ($l) => $l->date->toDateString())->flip();

        $cells = [];
        for ($i = 0, $d = $start->copy(); $i < $this->challenge->duration_days; $i++, $d->addDay()) {
            $key = $d->toDateString();
            $cells[] = [
                'date' => $key,
                'n' => $i + 1,
                'done' => $done->has($key),
                'isToday' => $d->isSameDay($today),
                'isFuture' => $d->gt($today),
            ];
        }

        return view('livewire.challenges.show', [
            'cells' => $cells,
            'endDate' => $end,
        ]);
    }
}
