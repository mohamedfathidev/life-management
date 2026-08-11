<?php

namespace App\Livewire\Marketing;

use App\Enums\MarketingStatus;
use App\Models\MarketingPost;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    #[On('post-saved')]
    public function refreshList(): void
    {
        //
    }

    /** Move a post to the next stage (idea → draft → scheduled → published). */
    public function advance(int $postId): void
    {
        $post = MarketingPost::findOrFail($postId);
        $this->authorize('update', $post);

        if ($next = $post->status->next()) {
            $post->update(['status' => $next->value]);
        }
    }

    public function delete(int $postId): void
    {
        $post = MarketingPost::findOrFail($postId);
        $this->authorize('delete', $post);
        $post->delete();
    }

    public function render(): View
    {
        $posts = MarketingPost::query()
            ->ownedBy(Auth::user())
            ->orderBy('scheduled_for')
            ->latest()
            ->get()
            ->groupBy(fn (MarketingPost $p) => $p->status->value);

        return view('livewire.marketing.index', [
            'statuses' => MarketingStatus::cases(),
            'posts' => $posts,
        ]);
    }
}
