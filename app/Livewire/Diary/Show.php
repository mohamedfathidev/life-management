<?php

namespace App\Livewire\Diary;

use App\Models\DiaryEntry;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public DiaryEntry $entry;

    public function mount(DiaryEntry $entry): void
    {
        $this->authorize('view', $entry);
        $this->entry = $entry;
    }

    #[On('diary-saved')]
    public function refreshEntry(): void
    {
        $this->entry->refresh();
    }

    public function delete(): void
    {
        $this->authorize('delete', $this->entry);
        $this->entry->delete();

        $this->redirect(route('diary.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.diary.show', [
            'entry' => $this->entry,
        ]);
    }
}
