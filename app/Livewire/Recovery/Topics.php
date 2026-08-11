<?php

namespace App\Livewire\Recovery;

use App\Models\RecoveryTopic;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Topics extends Component
{
    #[Url]
    public string $tag = '';

    #[On('topic-saved')]
    public function refreshList(): void
    {
        //
    }

    public function render(): View
    {
        $topics = RecoveryTopic::query()
            ->ownedBy(Auth::user())
            ->when($this->tag !== '', fn ($q) => $q->withTag($this->tag))
            ->latest()
            ->get();

        // Distinct tags across the user's topics, for the filter bar.
        $allTags = RecoveryTopic::query()
            ->ownedBy(Auth::user())
            ->pluck('tags')
            ->flatten()
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('livewire.recovery.topics', [
            'topics' => $topics,
            'allTags' => $allTags,
        ]);
    }
}
