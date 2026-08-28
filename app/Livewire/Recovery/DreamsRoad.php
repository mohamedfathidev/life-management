<?php

namespace App\Livewire\Recovery;

use App\Livewire\Concerns\BuildsWindingRoad;
use App\Models\RecoveryDream;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * "طريق أحلامك" — the same winding-road visual as "قبل الوقوع تذكر", but for
 * dreams: achieved dreams sit behind "إنت هنا", unachieved ones lie ahead —
 * so the road itself shows literal progress, not just a list.
 */
#[Layout('layouts.app')]
class DreamsRoad extends Component
{
    use BuildsWindingRoad;

    #[On('dream-saved')]
    public function refreshList(): void
    {
        //
    }

    public function toggleAchieved(int $id): void
    {
        $dream = RecoveryDream::ownedBy(Auth::user())->findOrFail($id);

        $dream->update([
            'is_achieved' => ! $dream->is_achieved,
            'achieved_at' => $dream->is_achieved ? null : now()->toDateString(),
        ]);
    }

    public function render(): View
    {
        $user = Auth::user();

        $achieved = RecoveryDream::ownedBy($user)->where('is_achieved', true)
            ->orderBy('achieved_at')->orderBy('created_at')->get();
        $unachieved = RecoveryDream::ownedBy($user)->where('is_achieved', false)
            ->orderBy('created_at')->get();

        $map = $this->buildRoadMap($achieved, $unachieved);

        $allDreams = $achieved->concat($unachieved);
        $startDate = $allDreams->min('created_at');

        return view('livewire.recovery.dreams-road', [
            'nodes' => $map['nodes'],
            'pathD' => $map['pathD'],
            'height' => $map['height'],
            'startDate' => $startDate,
            'achievedCount' => $achieved->count(),
            'totalCount' => $allDreams->count(),
        ]);
    }

    /**
     * @return array{nodes: array, pathD: string, height: int}
     */
    private function buildRoadMap(Collection $achieved, Collection $unachieved): array
    {
        $nodes = [];
        $y = 30;
        $stepY = 120;
        $side = 1;

        foreach ($achieved as $dream) {
            $x = 160 + ($side * 90);
            $nodes[] = [
                'type' => 'dream',
                'x' => $x,
                'y' => $y,
                'side' => $side > 0 ? 'right' : 'left',
                'achieved' => true,
                'dream' => $dream,
            ];
            $y += $stepY;
            $side *= -1;
        }

        $nodes[] = ['type' => 'you-are-here', 'x' => 160, 'y' => $y, 'text' => 'إنت هنا'];
        $y += $stepY;

        foreach ($unachieved as $dream) {
            $x = 160 + ($side * 90);
            $nodes[] = [
                'type' => 'dream',
                'x' => $x,
                'y' => $y,
                'side' => $side > 0 ? 'right' : 'left',
                'achieved' => false,
                'dream' => $dream,
            ];
            $y += $stepY;
            $side *= -1;
        }

        $nodes[] = [
            'type' => 'horizon',
            'x' => 160,
            'y' => $y,
            'text' => $unachieved->isEmpty() && $achieved->isNotEmpty() ? '🎉 حققت كل أحلامك… لسه فيه تاني؟' : '🌅 ولسه في المشوار',
        ];

        return [
            'nodes' => $nodes,
            'pathD' => $this->buildPathD($nodes),
            'height' => $y + 60,
        ];
    }
}
