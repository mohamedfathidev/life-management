<div class="min-h-screen bg-gradient-to-b from-bg-light via-bg-light to-primary/5 dark:from-bg-dark dark:via-bg-dark dark:to-primary-dark/10">
    <div class="max-w-2xl mx-auto px-4 py-8 sm:py-12">

        <a href="{{ route('dreams.show', $dream) }}" wire:navigate class="inline-block text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark mb-6">← رجوع لتفاصيل الحلم</a>

        {{-- Header --}}
        <div class="text-center mb-4">
            <div class="inline-block px-4 py-1.5 rounded-full bg-primary/15 dark:bg-primary-dark/20 text-primary dark:text-primary-dark text-xs font-semibold mb-3">
                المحطات الرئيسية
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-ink dark:text-ink-dark mb-2">
                🛣️ {{ $dream->title }}
            </h1>
            <p class="text-xs sm:text-sm text-ink-soft dark:text-ink-dark-soft">
                مرّر (hover) على أي محطة رئيسية عشان تشوف اللي جواها
            </p>
        </div>

        <div class="text-center mb-8">
            <span class="inline-block px-4 py-1.5 rounded-full bg-success/10 dark:bg-success-dark/10 text-success dark:text-success-dark text-xs sm:text-sm font-medium">
                التقدّم على الطريق: {{ $progress }}%
            </span>
        </div>

        @if (count($nodes) <= 2)
            <div class="text-center py-16 rounded-3xl border-2 border-dashed border-primary/25 dark:border-primary-dark/25">
                <p class="text-5xl mb-3">🛤️</p>
                <p class="text-ink-soft dark:text-ink-dark-soft">لسه مفيش محطات رئيسية (طرق) لهذا الحلم.</p>
            </div>
        @else
            <div class="relative mx-auto mb-8" style="max-width: 460px; aspect-ratio: 320 / {{ $height }};">
                <svg viewBox="0 0 320 {{ $height }}" class="absolute inset-0 w-full h-full" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
                    <path d="{{ $pathD }}" fill="none" stroke="{{ $dream->color ?: '#3F7D7A' }}" stroke-width="12" stroke-linecap="round" opacity="0.2" />
                    <path d="{{ $pathD }}" fill="none" stroke="currentColor" class="text-white dark:text-black" stroke-width="2" stroke-dasharray="7 11" stroke-linecap="round" opacity="0.7" />

                    @foreach ($nodes as $node)
                        @if ($node['type'] === 'start')
                            <circle cx="{{ $node['x'] }}" cy="{{ $node['y'] }}" r="7" class="fill-ink dark:fill-ink-dark" />
                        @elseif ($node['type'] === 'destination')
                            <circle cx="{{ $node['x'] }}" cy="{{ $node['y'] }}" r="12" fill="{{ $node['bg'] }}" />
                        @else
                            @php
                                $nodeColorClass = $node['total'] === 0
                                    ? 'fill-ink-soft dark:fill-ink-dark-soft'
                                    : ($node['done'] === $node['total'] ? 'fill-success dark:fill-success-dark' : 'fill-primary dark:fill-primary-dark');
                            @endphp
                            <circle cx="{{ $node['x'] }}" cy="{{ $node['y'] }}" r="8" class="{{ $nodeColorClass }}" />
                        @endif
                    @endforeach
                </svg>

                @foreach ($nodes as $node)
                    @php $topPct = $height > 0 ? ($node['y'] / $height * 100) : 0; @endphp

                    @if ($node['type'] === 'start')
                        <div class="absolute text-center" style="top: {{ $topPct }}%; left: 50%; transform: translate(-50%, -50%); width: 70%;">
                            <span class="inline-block px-3 py-1 rounded-full bg-ink dark:bg-ink-dark text-white dark:text-ink text-xs font-bold shadow whitespace-nowrap">{{ $node['text'] }}</span>
                        </div>
                    @elseif ($node['type'] === 'destination')
                        <div class="absolute text-center" style="top: {{ $topPct }}%; left: 50%; transform: translate(-50%, -50%); width: 78%;">
                            <span class="inline-block px-4 py-2.5 rounded-2xl text-sm font-bold shadow-lg leading-relaxed" style="background: {{ $node['bg'] }}; color: {{ $node['color'] }};">{{ $node['text'] }}</span>
                        </div>
                    @else
                        @php
                            $path = $node['path'];
                            $isDone = $node['total'] > 0 && $node['done'] === $node['total'];
                            $isStarted = $node['done'] > 0;
                        @endphp
                        <div class="absolute group z-10" style="top: {{ $topPct }}%; {{ $node['side'] === 'right' ? 'right: 2%;' : 'left: 2%;' }} width: 46%; transform: translateY(-50%);">
                            <div class="rounded-xl bg-surface-light dark:bg-surface-dark border shadow-sm px-3 py-2 cursor-default {{ $isDone ? 'border-success/40 dark:border-success-dark/40' : ($isStarted ? 'border-primary/40 dark:border-primary-dark/40' : 'border-ink-soft/20 dark:border-ink-dark-soft/20') }}">
                                <p class="text-xs sm:text-sm font-medium text-ink dark:text-ink-dark leading-snug {{ $node['side'] === 'right' ? 'text-right' : 'text-left' }}">
                                    🛤️ {{ $path->title }}
                                </p>
                                <p class="text-[11px] {{ $node['side'] === 'right' ? 'text-right' : 'text-left' }} {{ $isDone ? 'text-success dark:text-success-dark' : 'text-ink-soft dark:text-ink-dark-soft' }}">
                                    {{ $node['done'] }}/{{ $node['total'] }} محطة {{ $isDone ? '✓' : '' }}
                                </p>
                            </div>

                            {{-- Sub-stations on hover/focus. `pt-2` (not `mt-2`) so the buffer
                                 zone above the card is still part of the hoverable box — a real
                                 margin gap would break group-hover as the pointer crosses it. --}}
                            <div tabindex="0" class="absolute inset-0 outline-none"></div>
                            <div class="hidden group-hover:block group-focus-within:block absolute z-20 top-full pt-2 {{ $node['side'] === 'right' ? 'right-0' : 'left-0' }} w-72 max-w-[80vw]">
                                <div class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-xl border border-gray-100 dark:border-gray-800 p-4 text-right">
                                    <p class="font-bold text-sm text-ink dark:text-ink-dark mb-2">🛤️ {{ $path->title }}</p>
                                    @if ($path->milestones->isEmpty())
                                        <p class="text-xs text-ink-soft dark:text-ink-dark-soft">مفيش محطات فرعية لسه.</p>
                                    @else
                                        <ul class="space-y-1.5">
                                            @foreach ($path->milestones as $ms)
                                                <li class="flex items-center gap-2">
                                                    <button type="button" wire:click="toggleMilestone({{ $ms->id }})"
                                                        class="shrink-0 w-5 h-5 rounded-full flex items-center justify-center text-[10px] transition {{ $ms->is_done ? 'bg-success text-white' : 'bg-bg-light dark:bg-bg-dark text-ink-soft dark:text-ink-dark-soft border border-ink-soft/30' }}">
                                                        {{ $ms->is_done ? '✓' : '○' }}
                                                    </button>
                                                    <span class="text-xs {{ $ms->is_done ? 'line-through text-ink-soft dark:text-ink-dark-soft' : 'text-ink dark:text-ink-dark' }}">{{ $ms->title }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>
