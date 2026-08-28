<?php

namespace App\Livewire\Concerns;

/**
 * Turns a smooth, S-curve SVG path through an alternating zigzag of points
 * (the "Duolingo-style" road used by the recovery road pages) — shared so
 * every road page draws the same way.
 */
trait BuildsWindingRoad
{
    private function buildPathD(array $nodes): string
    {
        if (count($nodes) < 2) {
            return '';
        }

        $d = sprintf('M %d,%d ', $nodes[0]['x'], $nodes[0]['y']);

        for ($i = 1; $i < count($nodes); $i++) {
            $p0 = $nodes[$i - 1];
            $p1 = $nodes[$i];
            $midY = ($p0['y'] + $p1['y']) / 2;
            $d .= sprintf('C %d,%d %d,%d %d,%d ', $p0['x'], $midY, $p1['x'], $midY, $p1['x'], $p1['y']);
        }

        return trim($d);
    }
}
