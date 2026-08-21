<?php

namespace App\Livewire\Recovery;

use App\Models\RecoveryStory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class StoryShow extends Component
{
    public RecoveryStory $story;

    public function mount(RecoveryStory $story): void
    {
        $this->authorize('view', $story);
        $this->story = $story;
    }

    public function editStory(): void
    {
        $this->dispatch('edit-story', story: $this->story->id);
    }

    public function deleteStory(): void
    {
        $this->authorize('delete', $this->story);

        $this->dispatch('delete-story', story: $this->story->id);
    }

    public function render(): View
    {
        return view('livewire.recovery.story-show');
    }
}
