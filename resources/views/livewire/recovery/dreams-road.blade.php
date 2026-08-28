<div class="min-h-screen bg-gradient-to-b from-bg-light via-bg-light to-primary/5 dark:from-bg-dark dark:via-bg-dark dark:to-primary-dark/10">
    <div class="max-w-2xl mx-auto px-4 py-8 sm:py-12">

        <a href="{{ route('recovery.dreams') }}" wire:navigate class="inline-block text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark mb-6">← رجوع لأحلام التعافي</a>

        {{-- Header --}}
        <div class="text-center mb-4">
            <div class="inline-block px-4 py-1.5 rounded-full bg-primary/15 dark:bg-primary-dark/20 text-primary dark:text-primary-dark text-xs font-semibold mb-3">
                مش بس هتهرب من حاجة — إنت ماشي لحاجة
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-primary dark:text-primary-dark mb-2">
                🌟 طريق أحلامك
            </h1>
            @if ($startDate)
                <p class="text-xs sm:text-sm text-ink-soft dark:text-ink-dark-soft">
                    من {{ $startDate->translatedFormat('j M Y') }} لحد النهاردة {{ now()->translatedFormat('j M Y') }}
                </p>
            @endif
        </div>

        <div class="text-center mb-8">
            <span class="inline-block px-4 py-1.5 rounded-full bg-success/10 dark:bg-success-dark/10 text-success dark:text-success-dark text-xs sm:text-sm font-medium">
                🏆 حققت {{ $achievedCount }} من {{ $totalCount }} حلم
            </span>
            <button type="button" wire:click="$dispatch('create-dream')" class="ms-2 inline-block px-4 py-1.5 rounded-full bg-primary/10 dark:bg-primary-dark/10 text-primary dark:text-primary-dark text-xs sm:text-sm font-medium hover:opacity-80 transition">
                + حلم جديد
            </button>
        </div>

        @if ($totalCount === 0)
            <div class="text-center py-16 rounded-3xl border-2 border-dashed border-primary/25 dark:border-primary-dark/25">
                <p class="text-5xl mb-3">🌅</p>
                <p class="text-ink-soft dark:text-ink-dark-soft">لسه مفيش أحلام على الطريق. ابدأ بحلم واحد.</p>
            </div>
        @else
            {{-- The drawn road --}}
            <div class="relative mx-auto mb-4" style="max-width: 460px; aspect-ratio: 320 / {{ $height }};">
                <svg viewBox="0 0 320 {{ $height }}" class="absolute inset-0 w-full h-full" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
                    <path d="{{ $pathD }}" fill="none" class="stroke-primary dark:stroke-primary-dark" stroke-width="12" stroke-linecap="round" opacity="0.2" />
                    <path d="{{ $pathD }}" fill="none" stroke="currentColor" class="text-white dark:text-black" stroke-width="2" stroke-dasharray="7 11" stroke-linecap="round" opacity="0.7" />

                    @foreach ($nodes as $node)
                        @if ($node['type'] === 'you-are-here')
                            <circle cx="{{ $node['x'] }}" cy="{{ $node['y'] }}" r="7" class="fill-ink dark:fill-ink-dark" />
                        @elseif ($node['type'] === 'horizon')
                            <circle cx="{{ $node['x'] }}" cy="{{ $node['y'] }}" r="10" class="fill-primary dark:fill-primary-dark" />
                        @else
                            <circle cx="{{ $node['x'] }}" cy="{{ $node['y'] }}" r="7" class="{{ $node['achieved'] ? 'fill-success dark:fill-success-dark' : 'fill-primary dark:fill-primary-dark' }}" />
                        @endif
                    @endforeach
                </svg>

                @foreach ($nodes as $node)
                    @php $topPct = $height > 0 ? ($node['y'] / $height * 100) : 0; @endphp

                    @if ($node['type'] === 'you-are-here')
                        <div class="absolute text-center" style="top: {{ $topPct }}%; left: 50%; transform: translate(-50%, -50%); width: 60%;">
                            <span class="inline-block px-3 py-1 rounded-full bg-ink dark:bg-ink-dark text-white dark:text-ink text-xs font-bold shadow whitespace-nowrap">📍 {{ $node['text'] }}</span>
                        </div>
                    @elseif ($node['type'] === 'horizon')
                        <div class="absolute text-center" style="top: {{ $topPct }}%; left: 50%; transform: translate(-50%, -50%); width: 80%;">
                            <span class="inline-block px-4 py-2 rounded-2xl bg-primary dark:bg-primary-dark text-white text-sm font-bold shadow-lg leading-relaxed">{{ $node['text'] }}</span>
                        </div>
                    @else
                        @php $dream = $node['dream']; @endphp
                        <div class="absolute group z-10" style="top: {{ $topPct }}%; {{ $node['side'] === 'right' ? 'right: 2%;' : 'left: 2%;' }} width: 46%; transform: translateY(-50%);">
                            <div class="flex items-center gap-2 {{ $node['side'] === 'right' ? 'flex-row-reverse' : '' }} cursor-default">
                                <span class="shrink-0 w-9 h-9 rounded-full flex items-center justify-center text-lg shadow-sm {{ $node['achieved'] ? 'bg-success/15 dark:bg-success-dark/20' : 'bg-primary/10 dark:bg-primary-dark/15' }}">
                                    {{ $dream->icon ?: ($node['achieved'] ? '🏆' : '🌅') }}
                                </span>
                                <span class="text-xs sm:text-sm font-medium text-ink dark:text-ink-dark leading-snug {{ $node['side'] === 'right' ? 'text-right' : 'text-left' }}">
                                    {{ $dream->title }}
                                </span>
                            </div>

                            {{-- Details on hover/focus. `pt-2` (not `mt-2`) so the buffer zone
                                 above the card stays part of the hoverable box. --}}
                            <div tabindex="0" class="absolute inset-0 outline-none"></div>
                            <div class="hidden group-hover:block group-focus-within:block absolute z-20 top-full pt-2 {{ $node['side'] === 'right' ? 'right-0' : 'left-0' }} w-64 max-w-[75vw]">
                                <div class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-xl border border-gray-100 dark:border-gray-800 p-4 text-right">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xl">{{ $dream->icon ?: '🌅' }}</span>
                                        <span class="font-bold text-sm text-ink dark:text-ink-dark">{{ $dream->title }}</span>
                                    </div>
                                    <p class="text-[11px] text-ink-soft dark:text-ink-dark-soft mb-2">
                                        @if ($node['achieved'])
                                            🏆 اتحقق يوم {{ optional($dream->achieved_at)->translatedFormat('j M Y') }}
                                        @else
                                            🌱 مضاف من {{ $dream->created_at->translatedFormat('j M Y') }}
                                        @endif
                                        @if ($dream->recovery)
                                            · في {{ $dream->recovery->title }}
                                        @endif
                                    </p>
                                    @if (! empty($dream->benefits))
                                        <ul class="space-y-1 mb-3">
                                            @foreach ($dream->benefits as $benefit)
                                                <li class="text-xs text-ink-soft dark:text-ink-dark-soft flex items-start gap-1.5">
                                                    <span class="text-primary dark:text-primary-dark mt-0.5">✦</span>
                                                    <span class="leading-relaxed">{{ $benefit }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    <div class="flex items-center gap-3 pt-2 border-t border-gray-100 dark:border-gray-800">
                                        <button type="button" wire:click="$dispatch('edit-dream', { dream: {{ $dream->id }} })" class="text-xs text-primary dark:text-primary-dark hover:underline">✎ عدّل</button>
                                        <button type="button" wire:click="toggleAchieved({{ $dream->id }})" class="text-xs text-success dark:text-success-dark hover:underline">{{ $node['achieved'] ? '↩ رجّعه نشط' : '🏁 تحقق الحلم' }}</button>
                                        <button type="button" wire:click="$dispatch('delete-dream', { dream: {{ $dream->id }} })" wire:confirm="حذف هذا الحلم؟" class="text-xs text-danger hover:underline">حذف</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    <livewire:recovery.manage-dream />
</div>
