<?php

namespace App\Livewire\Recovery;

use App\Models\RecoveryIdea;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

/** "أفكار تراودني" — a running log of ideas/thoughts, captured before they slip. */
#[Layout('layouts.app')]
class Ideas extends Component
{
    public ?int $editingId = null;

    public string $body = '';

    public string $actionTaken = '';

    public function save(): void
    {
        $data = $this->validate([
            'body' => ['required', 'string', 'max:2000'],
            'actionTaken' => ['nullable', 'string', 'max:2000'],
        ], attributes: ['body' => 'الفكرة', 'actionTaken' => 'التعامل مع الفكرة']);

        $payload = [
            'body' => $data['body'],
            'action_taken' => $data['actionTaken'] ?: null,
        ];

        if ($this->editingId) {
            RecoveryIdea::ownedBy(Auth::user())->findOrFail($this->editingId)->update($payload);
        } else {
            RecoveryIdea::create([...$payload, 'user_id' => Auth::id()]);
        }

        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $idea = RecoveryIdea::ownedBy(Auth::user())->findOrFail($id);
        $this->editingId = $idea->id;
        $this->body = $idea->body;
        $this->actionTaken = $idea->action_taken ?? '';
    }

    public function delete(int $id): void
    {
        RecoveryIdea::ownedBy(Auth::user())->where('id', $id)->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }
    }

    public function resetForm(): void
    {
        $this->reset('editingId', 'body', 'actionTaken');
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.recovery.ideas', [
            'ideas' => RecoveryIdea::ownedBy(Auth::user())->latest()->get(),
        ]);
    }
}
