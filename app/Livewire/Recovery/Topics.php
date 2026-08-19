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

    #[Url]
    public string $search = '';

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
            ->when($this->search !== '', function ($q) {
                $term = trim($this->search);
                $cleanTerm = ltrim($term, '#');
                $likeFull = '%'.$term.'%';
                $cleanSpace = str_replace('_', ' ', $cleanTerm);
                $cleanUnderscore = str_replace(' ', '_', $cleanTerm);

                $q->where(function ($sub) use ($likeFull, $cleanTerm, $cleanSpace, $cleanUnderscore) {
                    $sub->where('title', 'like', $likeFull)
                        ->orWhereRaw("JSON_SEARCH(tags, 'one', ?) IS NOT NULL", ['%'.$cleanTerm.'%'])
                        ->orWhereRaw("JSON_SEARCH(tags, 'one', ?) IS NOT NULL", ['%'.$cleanSpace.'%'])
                        ->orWhereRaw("JSON_SEARCH(tags, 'one', ?) IS NOT NULL", ['%'.$cleanUnderscore.'%'])
                        ->orWhereJsonContains('tags', $cleanTerm)
                        ->orWhere('tags', 'like', '%'.$cleanTerm.'%');
                });
            })
            ->latest()
            ->get();

        // Distinct tags across the user's topics, for the filter bar.
        $allTags = RecoveryTopic::query()
            ->ownedBy(Auth::user())
            ->pluck('tags')
            ->flatten()
            ->filter()
            ->map(fn ($t) => ltrim(trim($t), '#'))
            ->unique()
            ->sort()
            ->values();

        return view('livewire.recovery.topics', [
            'topics' => $topics,
            'allTags' => $allTags,
        ]);
    }
}
