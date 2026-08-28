<?php

namespace App\Livewire\Dreams;

use App\Livewire\Concerns\BuildsWindingRoad;
use App\Models\Dream;
use App\Models\DreamMilestone;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * "شوف المحطات الرئيسية" — the dream's paths drawn as one winding road, same
 * visual as "قبل الوقوع تذكر". Each node is a whole path (a "main station");
 * hovering it reveals its milestones ("sub-stations") and which are done.
 */
#[Layout('layouts.app')]
class Road extends Component
{
    use BuildsWindingRoad;

    public Dream $dream;

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

    public function toggleMilestone(int $milestoneId): void
    {
        $this->authorize('update', $this->dream);

        $milestone = DreamMilestone::whereHas('path', fn ($q) => $q->where('dream_id', $this->dream->id))
            ->findOrFail($milestoneId);

        $milestone->update(['is_done' => ! $milestone->is_done]);
    }

    public function render(): View
    {
        $paths = $this->dream->paths()->with('milestones')->get();
        $map = $this->buildRoadMap($paths);

        return view('livewire.dreams.road', [
            'nodes' => $map['nodes'],
            'pathD' => $map['pathD'],
            'height' => $map['height'],
            'progress' => $this->dream->progressPercent(),
        ]);
    }

    /**
     * @return array{nodes: array, pathD: string, height: int}
     */
    private function buildRoadMap(Collection $paths): array
    {
        $y = 30;
        $stepY = 130;
        $side = 1;

        $nodes = [[
            'type' => 'start',
            'x' => 160,
            'y' => $y,
            'text' => '📍 أنا هنا'.($this->dream->from_point ? ': '.$this->dream->from_point : ''),
        ]];

        foreach ($paths as $path) {
            $y += $stepY;
            $total = $path->milestones->count();
            $done = $path->milestones->where('is_done', true)->count();

            $nodes[] = [
                'type' => 'path',
                'x' => 160 + ($side * 90),
                'y' => $y,
                'side' => $side > 0 ? 'right' : 'left',
                'path' => $path,
                'done' => $done,
                'total' => $total,
            ];
            $side *= -1;
        }

        $y += $stepY;
        $destBg = $this->dream->darkerColor();
        $nodes[] = [
            'type' => 'destination',
            'x' => 160,
            'y' => $y,
            'text' => '🏁 الحلم'.($this->dream->to_point ? ': '.$this->dream->to_point : ''),
            'bg' => $destBg,
            'color' => $this->dream->contrastText($destBg),
        ];

        return [
            'nodes' => $nodes,
            'pathD' => $this->buildPathD($nodes),
            'height' => $y + 60,
        ];
    }
}
