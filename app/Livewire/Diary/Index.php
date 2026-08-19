<?php

namespace App\Livewire\Diary;

use App\Models\DiaryEntry;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    #[Url]
    public string $tag = '';

    #[Url]
    public string $search = '';

    #[On('diary-saved')]
    public function refreshList(): void
    {
        //
    }

    public function render(): View
    {
        $entries = DiaryEntry::query()
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
            ->orderByDesc('date')
            ->latest()
            ->get();

        $allTags = DiaryEntry::query()
            ->ownedBy(Auth::user())
            ->pluck('tags')
            ->flatten()
            ->filter()
            ->map(fn ($t) => ltrim(trim($t), '#'))
            ->unique()
            ->sort()
            ->values();

        return view('livewire.diary.index', [
            'entries' => $entries,
            'allTags' => $allTags,
        ]);
    }
}
