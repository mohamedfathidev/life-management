<?php

namespace App\Livewire\Recovery;

use App\Models\RecoveryMistake;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class MistakeShow extends Component
{
    public RecoveryMistake $mistake;

    public string $title = '';

    public ?string $note = null;

    public int $weight = 50;

    public bool $savedSuccessfully = false;

    public function mount(RecoveryMistake $mistake): void
    {
        if ($mistake->user_id !== Auth::id()) {
            abort(403);
        }

        $this->mistake = $mistake;
        $this->title = $mistake->title;
        $this->note = $mistake->note;
        $this->weight = $mistake->weight;
    }

    public function save(): void
    {
        if ($this->mistake->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
            'weight' => ['required', 'integer', 'between:0,100'],
        ], attributes: [
            'title' => 'الخطأ',
            'note' => 'الملاحظة والتفاصيل',
            'weight' => 'النسبة',
        ]);

        $this->mistake->update($data);
        $this->savedSuccessfully = true;
    }

    public function delete(): void
    {
        if ($this->mistake->user_id !== Auth::id()) {
            abort(403);
        }

        $this->mistake->delete();
        $this->redirect(route('recovery.mistakes'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.recovery.mistake-show', [
            'mistake' => $this->mistake,
        ]);
    }
}
