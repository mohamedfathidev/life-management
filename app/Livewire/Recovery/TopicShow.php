<?php

namespace App\Livewire\Recovery;

use App\Models\RecoveryTopic;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class TopicShow extends Component
{
    public RecoveryTopic $topic;

    public function mount(RecoveryTopic $topic): void
    {
        $this->authorize('view', $topic);
        $this->topic = $topic;
    }

    #[On('topic-saved')]
    public function refreshTopic(): void
    {
        $this->topic->refresh();
    }

    public function delete(): void
    {
        $this->authorize('delete', $this->topic);
        $this->topic->delete();

        $this->redirect(route('recovery.topics'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.recovery.topic-show', [
            'topic' => $this->topic,
        ]);
    }
}
