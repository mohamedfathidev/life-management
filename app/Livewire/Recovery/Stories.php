<?php

namespace App\Livewire\Recovery;

use App\Models\Recovery;
use App\Models\RecoveryStory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Stories extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public ?int $recoveryId = null;

    #[On('story-saved')]
    public function refreshList(): void
    {
        //
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRecoveryId(): void
    {
        $this->resetPage();
    }

    public function createStory(?int $recoveryId = null): void
    {
        $this->dispatch('create-story', recoveryId: $recoveryId);
    }

    public function toggleFeatured(int $storyId): void
    {
        $story = RecoveryStory::ownedBy(Auth::user())->findOrFail($storyId);
        $story->update(['is_featured' => ! $story->is_featured]);
    }

    public function render(): View
    {
        $user = Auth::user();

        $stories = RecoveryStory::query()
            ->ownedBy($user)
            ->with('recovery:id,title')
            ->when($this->recoveryId, fn ($q) => $q->where('recovery_id', $this->recoveryId))
            ->when($this->search !== '', function ($q) {
                $term = trim($this->search);
                $q->where(function ($sub) use ($term) {
                    $sub->where('title', 'like', '%'.$term.'%')
                        ->orWhere('content', 'like', '%'.$term.'%');
                });
            })
            ->orderByDesc('is_featured')
            ->latest('date')
            ->latest()
            ->paginate(12);

        return view('livewire.recovery.stories', [
            'stories' => $stories,
            'recoveries' => Recovery::query()->ownedBy($user)->latest('start_date')->get(['id', 'title']),
        ]);
    }
}
