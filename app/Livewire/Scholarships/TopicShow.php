<?php

namespace App\Livewire\Scholarships;

use App\Models\ScholarshipTopic;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class TopicShow extends Component
{
    public ScholarshipTopic $topic;

    public function mount(ScholarshipTopic $topic): void
    {
        abort_unless($topic->user_id === Auth::id(), 403);
        $this->topic = $topic;
    }

    #[On('scholarship-topic-saved')]
    public function refreshTopic(): void
    {
        $this->topic->refresh();
    }

    public function editTopic(): void
    {
        $this->dispatch('edit-scholarship-topic', topic: $this->topic->id);
    }

    public function delete()
    {
        abort_unless($this->topic->user_id === Auth::id(), 403);
        $this->topic->delete();

        return $this->redirectRoute('scholarships.topics', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.scholarships.topic-show');
    }
}
