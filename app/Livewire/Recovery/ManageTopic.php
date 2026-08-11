<?php

namespace App\Livewire\Recovery;

use App\Livewire\Forms\RecoveryTopicForm;
use App\Models\RecoveryTopic;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageTopic extends Component
{
    public RecoveryTopicForm $form;

    public bool $open = false;

    #[On('create-topic')]
    public function openForCreate(): void
    {
        $this->form->reset();
        $this->resetValidation();
        $this->open = true;
    }

    #[On('edit-topic')]
    public function openForEdit(RecoveryTopic $topic): void
    {
        $this->authorize('update', $topic);
        $this->resetValidation();
        $this->form->setTopic($topic);
        $this->open = true;
    }

    #[On('delete-topic')]
    public function delete(RecoveryTopic $topic): void
    {
        $this->authorize('delete', $topic);
        $topic->delete();

        $this->dispatch('topic-saved');
    }

    public function save(): void
    {
        if ($this->form->topic) {
            $this->authorize('update', $this->form->topic);
        }

        $this->form->persist(Auth::id());

        $this->open = false;
        $this->dispatch('topic-saved');
    }

    public function close(): void
    {
        $this->open = false;
        $this->form->reset();
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.recovery.manage-topic', [
            'importances' => \App\Enums\TopicImportance::cases(),
        ]);
    }
}
