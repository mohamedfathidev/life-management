<?php

namespace App\Livewire\Diary;

use App\Livewire\Forms\DiaryForm;
use App\Models\DiaryEntry;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageEntry extends Component
{
    public DiaryForm $form;

    public bool $open = false;

    #[On('create-diary-entry')]
    public function openForCreate(): void
    {
        $this->resetValidation();
        $this->form->prepareForCreate();
        $this->open = true;
    }

    #[On('edit-diary-entry')]
    public function openForEdit(DiaryEntry $entry): void
    {
        $this->authorize('update', $entry);
        $this->resetValidation();
        $this->form->setEntry($entry);
        $this->open = true;
    }

    #[On('delete-diary-entry')]
    public function delete(DiaryEntry $entry): void
    {
        $this->authorize('delete', $entry);
        $entry->delete();

        $this->dispatch('diary-saved');
    }

    public function save(): void
    {
        if ($this->form->entry) {
            $this->authorize('update', $this->form->entry);
        }

        $this->form->persist(Auth::id());

        $this->open = false;
        $this->dispatch('diary-saved');
    }

    public function close(): void
    {
        $this->open = false;
        $this->form->reset();
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.diary.manage-entry');
    }
}
