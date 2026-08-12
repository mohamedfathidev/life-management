<?php

namespace App\Livewire\Dreams;

use App\Models\Dream;
use App\Models\DreamMilestone;
use App\Models\DreamPath;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public Dream $dream;

    public string $newPathTitle = '';

    /** @var array<int, string> per-path new milestone input */
    public array $newMilestone = [];

    public function mount(Dream $dream): void
    {
        $this->authorize('view', $dream);
        $this->dream = $dream;
    }

    #[On('dream-saved')]
    public function refreshDream(): void
    {
        $this->dream->refresh();
    }

    public function editDream(): void
    {
        $this->dispatch('edit-dream', dream: $this->dream->id);
    }

    public function delete()
    {
        $this->authorize('delete', $this->dream);
        $this->dream->delete();

        return $this->redirectRoute('dreams.index', navigate: true);
    }

    // --- Paths (branches) --------------------------------------------------

    public function addPath(): void
    {
        $this->authorize('update', $this->dream);
        $title = trim($this->newPathTitle);

        if ($title !== '') {
            $this->dream->paths()->create([
                'title' => $title,
                'position' => (int) $this->dream->paths()->max('position') + 1,
            ]);
            $this->newPathTitle = '';
        }
    }

    public function deletePath(int $pathId): void
    {
        $this->authorize('update', $this->dream);
        DreamPath::where('dream_id', $this->dream->id)->where('id', $pathId)->delete();
    }

    // --- Milestones (stations) ---------------------------------------------

    private function ownedPath(int $pathId): DreamPath
    {
        return DreamPath::where('dream_id', $this->dream->id)->findOrFail($pathId);
    }

    public function addMilestone(int $pathId): void
    {
        $this->authorize('update', $this->dream);
        $path = $this->ownedPath($pathId);
        $title = trim($this->newMilestone[$pathId] ?? '');

        if ($title !== '') {
            $path->milestones()->create([
                'title' => $title,
                'position' => (int) $path->milestones()->max('position') + 1,
            ]);
            $this->newMilestone[$pathId] = '';
        }
    }

    public function toggleMilestone(int $milestoneId): void
    {
        $this->authorize('update', $this->dream);
        $milestone = $this->milestone($milestoneId);
        $milestone->update(['is_done' => ! $milestone->is_done]);
    }

    public function deleteMilestone(int $milestoneId): void
    {
        $this->authorize('update', $this->dream);
        $this->milestone($milestoneId)->delete();
    }

    private function milestone(int $id): DreamMilestone
    {
        return DreamMilestone::whereHas('path', fn ($q) => $q->where('dream_id', $this->dream->id))
            ->findOrFail($id);
    }

    public function render(): View
    {
        $paths = $this->dream->paths()->with('milestones')->get();

        return view('livewire.dreams.show', [
            'paths' => $paths,
            'progress' => $this->dream->progressPercent(),
        ]);
    }
}
