<?php

namespace App\Livewire\Scholarships;

use App\Models\ScholarshipTopic;
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

    #[On('scholarship-topic-saved')]
    public function refreshList(): void
    {
        //
    }

    public function render(): View
    {
        $topics = ScholarshipTopic::query()
            ->ownedBy(Auth::user())
            ->when($this->tag !== '', fn ($q) => $q->withTag($this->tag))
            ->latest()
            ->get();

        $allTags = ScholarshipTopic::query()
            ->ownedBy(Auth::user())
            ->pluck('tags')
            ->flatten()
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('livewire.scholarships.topics', [
            'topics' => $topics,
            'allTags' => $allTags,
        ]);
    }
}
