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
            ->when($this->search !== '', fn ($q) => $q->where('title', 'like', '%'.$this->search.'%'))
            ->orderByDesc('date')
            ->latest()
            ->get();

        $allTags = DiaryEntry::query()
            ->ownedBy(Auth::user())
            ->pluck('tags')->flatten()->filter()->unique()->sort()->values();

        return view('livewire.diary.index', [
            'entries' => $entries,
            'allTags' => $allTags,
        ]);
    }
}
