@props([
    'points' => [],   // array of ['label' => string, 'value' => int]
    'max' => 100,
    'suffix' => '%',
])

@php
    $w = 640; $h = 220;
    $padX = 40; $padTop = 20; $padBottom = 40;
    $count = count($points);
    $innerW = $w - $padX * 2;
    $innerH = $h - $padTop - $padBottom;
    $max = max(1, (int) $max);

    $coords = [];
    foreach (array_values($points) as $i => $p) {
        $x = $count <= 1 ? $padX + $innerW / 2 : $padX + ($i * $innerW / ($count - 1));
        $y = $padTop + $innerH - (($p['value'] / $max) * $innerH);
        $coords[] = ['x' => round($x, 1), 'y' => round($y, 1), 'p' => $p];
    }

    $linePoints = implode(' ', array_map(fn ($c) => "{$c['x']},{$c['y']}", $coords));
    $areaPath = $count
        ? "M {$coords[0]['x']},".($padTop + $innerH).' '
            .implode(' ', array_map(fn ($c) => "L {$c['x']},{$c['y']}", $coords))
            ." L {$coords[$count - 1]['x']},".($padTop + $innerH).' Z'
        : '';
@endphp

<div {{ $attributes->merge(['class' => 'w-full overflow-x-auto']) }}>
    <svg viewBox="0 0 {{ $w }} {{ $h }}" class="w-full min-w-[420px]" role="img" preserveAspectRatio="xMidYMid meet">
        {{-- gridlines at 0/25/50/75/100 --}}
        @foreach ([0, 25, 50, 75, 100] as $g)
            @php($gy = $padTop + $innerH - (($g / 100) * $innerH))
            <line x1="{{ $padX }}" y1="{{ $gy }}" x2="{{ $w - $padX }}" y2="{{ $gy }}"
                  class="text-ink-soft/15 dark:text-ink-dark-soft/15" stroke="currentColor" stroke-width="1" />
            <text x="{{ $padX - 8 }}" y="{{ $gy + 4 }}" text-anchor="end" font-size="11"
                  class="text-ink-soft dark:text-ink-dark-soft" fill="currentColor">{{ $g }}</text>
        @endforeach

        @if ($count)
            {{-- area + line --}}
            <path d="{{ $areaPath }}" class="text-primary dark:text-primary-dark" fill="currentColor" opacity="0.12" />
            <polyline points="{{ $linePoints }}" fill="none" stroke-width="2.5"
                      class="text-primary dark:text-primary-dark" stroke="currentColor"
                      stroke-linejoin="round" stroke-linecap="round" />

            {{-- dots + value + label --}}
            @foreach ($coords as $c)
                <circle cx="{{ $c['x'] }}" cy="{{ $c['y'] }}" r="4"
                        class="text-primary dark:text-primary-dark" fill="currentColor" />
                <text x="{{ $c['x'] }}" y="{{ $c['y'] - 10 }}" text-anchor="middle" font-size="11" font-weight="600"
                      class="text-ink dark:text-ink-dark" fill="currentColor">{{ $c['p']['value'] }}{{ $suffix }}</text>
                <text x="{{ $c['x'] }}" y="{{ $h - 16 }}" text-anchor="middle" font-size="11"
                      class="text-ink-soft dark:text-ink-dark-soft" fill="currentColor">{{ \Illuminate\Support\Str::limit($c['p']['label'], 10) }}</text>
            @endforeach
        @endif
    </svg>
</div>
