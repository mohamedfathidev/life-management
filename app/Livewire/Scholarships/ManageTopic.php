<?php

namespace App\Livewire\Scholarships;

use App\Livewire\Forms\ScholarshipTopicForm;
use App\Models\ScholarshipTopic;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageTopic extends Component
{
    public ScholarshipTopicForm $form;

    public bool $open = false;

    #[On('create-scholarship-topic')]
    public function openForCreate(): void
    {
        $this->form->reset();
        $this->resetValidation();
        $this->open = true;
    }

    #[On('edit-scholarship-topic')]
    public function openForEdit(ScholarshipTopic $topic): void
    {
        $this->authorize('update', $topic);
        $this->resetValidation();
        $this->form->setTopic($topic);
        $this->open = true;
    }

    #[On('delete-scholarship-topic')]
    public function delete(ScholarshipTopic $topic): void
    {
        $this->authorize('delete', $topic);
        $topic->delete();

        $this->dispatch('scholarship-topic-saved');
    }

    public function save(): void
    {
        if ($this->form->topic) {
            $this->authorize('update', $this->form->topic);
        }

        $this->form->persist(Auth::id());

        $this->open = false;
        $this->dispatch('scholarship-topic-saved');
    }

    public function close(): void
    {
        $this->open = false;
        $this->form->reset();
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.scholarships.manage-topic');
    }
}
