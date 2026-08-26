<?php

namespace App\Livewire\Diary;

use App\Models\DiaryChange;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

/** "إيه اللي غيّرني؟" — a running log of realizations that actually changed something, not just talk. */
#[Layout('layouts.app')]
class Changes extends Component
{
    public ?int $editingId = null;

    public string $body = '';

    public function save(): void
    {
        $data = $this->validate([
            'body' => ['required', 'string', 'max:2000'],
        ], attributes: ['body' => 'اللي غيّرك']);

        if ($this->editingId) {
            DiaryChange::ownedBy(Auth::user())->findOrFail($this->editingId)->update($data);
        } else {
            DiaryChange::create([...$data, 'user_id' => Auth::id()]);
        }

        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $change = DiaryChange::ownedBy(Auth::user())->findOrFail($id);
        $this->editingId = $change->id;
        $this->body = $change->body;
    }

    public function delete(int $id): void
    {
        DiaryChange::ownedBy(Auth::user())->where('id', $id)->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }
    }

    /** Mark/unmark a change as one of the most important ones ("نجمة"). */
    public function toggleImportant(int $id): void
    {
        $change = DiaryChange::ownedBy(Auth::user())->findOrFail($id);
        $change->update(['is_important' => ! $change->is_important]);
    }

    public function resetForm(): void
    {
        $this->reset('editingId', 'body');
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.diary.changes', [
            'changes' => DiaryChange::ownedBy(Auth::user())->latest()->get(),
        ]);
    }
}
