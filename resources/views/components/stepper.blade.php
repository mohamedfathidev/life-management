@props(['steps' => []])

{{-- Horizontal RTL stepper. Circles are fixed-size; connectors flex to fill
     the gaps so nothing overflows. Labels are absolutely placed under each circle. --}}
<div {{ $attributes->merge(['class' => 'flex items-center pb-9']) }} dir="rtl">
    @foreach ($steps as $i => $step)
        <div class="relative flex flex-col items-center shrink-0">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs {{ $step['circleClass'] }}">
                {{ $step['mark'] }}
            </div>
            <span class="absolute top-9 whitespace-nowrap text-xs {{ ($step['reached'] ?? false) ? 'text-ink dark:text-ink-dark font-medium' : 'text-ink-soft dark:text-ink-dark-soft' }}">
                {{ $step['label'] }}
            </span>
            @if (! empty($step['sub']))
                <span class="absolute top-[3.5rem] whitespace-nowrap text-[10px] text-ink-soft dark:text-ink-dark-soft">{{ $step['sub'] }}</span>
            @endif
        </div>

        @if (! $loop->last)
            <div class="flex-1 h-0.5 mx-1 {{ ($steps[$i + 1]['reached'] ?? false) ? ($steps[$i + 1]['lineColor'] ?? 'bg-primary') : 'bg-ink-soft/20' }}"></div>
        @endif
    @endforeach
</div>
