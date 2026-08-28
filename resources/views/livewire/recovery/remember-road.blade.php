<div class="min-h-screen bg-gradient-to-b from-bg-light via-bg-light to-{{ $roadEnum->color() }}/5 dark:from-bg-dark dark:via-bg-dark dark:to-{{ $roadEnum->color() }}-dark/10">
    <div class="max-w-2xl mx-auto px-4 py-8 sm:py-12">

        <a href="{{ route('recovery.remember') }}" wire:navigate class="inline-block text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark mb-6">← رجوع للمفترق</a>

        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="inline-block px-4 py-1.5 rounded-full bg-{{ $roadEnum->color() }}/15 dark:bg-{{ $roadEnum->color() }}-dark/20 text-{{ $roadEnum->color() }} dark:text-{{ $roadEnum->color() }}-dark text-xs font-semibold mb-3">
                {{ $roadEnum === \App\Enums\RecoveryRoad::Destruction ? 'اتفرّج على نهايته الأول' : 'اتفرّج على نهايته الحلوة' }}
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-{{ $roadEnum->color() }} dark:text-{{ $roadEnum->color() }}-dark mb-2">
                {{ $roadEnum->emoji() }} {{ $roadEnum->label() }}
            </h1>
        </div>

        @if ($roadEnum === \App\Enums\RecoveryRoad::Destruction)
            {{-- الساقية: بتلف حوالين نفسها ولا بتوصل لحته جديد — نفس السيناريو بيتكرر --}}
            <div class="flex flex-col items-center justify-center mb-8 select-none" aria-hidden="true">
                <svg viewBox="0 0 100 100" class="w-16 h-16 sm:w-20 sm:h-20">
                    <g class="text-danger dark:text-danger-dark" fill="none" stroke="currentColor" stroke-width="4">
                        <circle cx="50" cy="50" r="42" />
                        <g stroke-linecap="round">
                            <line x1="50" y1="8" x2="50" y2="92" />
                            <line x1="8" y1="50" x2="92" y2="50" />
                            <line x1="20" y1="20" x2="80" y2="80" />
                            <line x1="80" y1="20" x2="20" y2="80" />
                        </g>
                        <circle cx="50" cy="50" r="6" fill="currentColor" />
                        <animateTransform attributeName="transform" type="rotate" from="0 50 50" to="360 50 50" dur="6s" repeatCount="indefinite" />
                    </g>
                </svg>
                <p class="text-xs sm:text-sm text-ink-soft dark:text-ink-dark-soft mt-2 text-center max-w-xs leading-relaxed">
                    🔄 زي الساقية بالظبط — تلف تلف تلف وترجع لنفس النقطة. نفس السيناريو بيتكرر كل يوم لو ما وقفتوش
                </p>
            </div>
        @endif

        {{-- The drawn road --}}
        <div class="relative mx-auto mb-8" style="max-width: 460px; aspect-ratio: 320 / {{ $height }};">
            <svg viewBox="0 0 320 {{ $height }}" class="absolute inset-0 w-full h-full" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
                <path d="{{ $pathD }}" fill="none" class="stroke-{{ $roadEnum->color() }} dark:stroke-{{ $roadEnum->color() }}-dark" stroke-width="12" stroke-linecap="round" opacity="0.25" />
                <path d="{{ $pathD }}" fill="none" stroke="currentColor" class="text-white dark:text-black" stroke-width="2" stroke-dasharray="7 11" stroke-linecap="round" opacity="0.7" />

                @foreach ($nodes as $node)
                    @if ($node['type'] === 'you-are-here')
                        <circle cx="{{ $node['x'] }}" cy="{{ $node['y'] }}" r="7" class="fill-ink dark:fill-ink-dark" />
                    @elseif ($node['type'] === 'divider')
                        <circle cx="{{ $node['x'] }}" cy="{{ $node['y'] }}" r="4" class="fill-ink-soft dark:fill-ink-dark-soft" />
                    @elseif ($node['type'] === 'destination')
                        <circle cx="{{ $node['x'] }}" cy="{{ $node['y'] }}" r="15" class="fill-{{ $roadEnum->color() }} dark:fill-{{ $roadEnum->color() }}-dark" />
                    @else
                        <circle cx="{{ $node['x'] }}" cy="{{ $node['y'] }}" r="6" class="fill-{{ $roadEnum->color() }} dark:fill-{{ $roadEnum->color() }}-dark" />
                    @endif
                @endforeach
            </svg>

            @foreach ($nodes as $node)
                @php $topPct = $height > 0 ? ($node['y'] / $height * 100) : 0; @endphp

                @if ($node['type'] === 'you-are-here')
                    <div class="absolute text-center" style="top: {{ $topPct }}%; left: 50%; transform: translate(-50%, -50%); width: 60%;">
                        <span class="inline-block px-3 py-1 rounded-full bg-ink dark:bg-ink-dark text-white dark:text-ink text-xs font-bold shadow whitespace-nowrap">📍 {{ $node['text'] }}</span>
                    </div>
                @elseif ($node['type'] === 'divider')
                    <div class="absolute text-center" style="top: {{ $topPct }}%; left: 50%; transform: translate(-50%, -50%);">
                        <span class="text-ink-soft dark:text-ink-dark-soft text-xs">{{ $node['text'] }}</span>
                    </div>
                @elseif ($node['type'] === 'destination')
                    <div class="absolute text-center" style="top: {{ $topPct }}%; left: 50%; transform: translate(-50%, -50%); width: 78%;">
                        <span class="inline-block px-4 py-2.5 rounded-2xl bg-{{ $roadEnum->color() }} dark:bg-{{ $roadEnum->color() }}-dark text-white text-sm font-bold shadow-lg leading-relaxed">{{ $node['text'] }}</span>
                    </div>
                @else
                    <div class="absolute" style="top: {{ $topPct }}%; {{ $node['side'] === 'right' ? 'right: 2%;' : 'left: 2%;' }} width: 46%; transform: translateY(-50%);">
                        <div class="relative rounded-xl bg-surface-light dark:bg-surface-dark border border-{{ $roadEnum->color() }}/30 dark:border-{{ $roadEnum->color() }}-dark/30 shadow px-3 py-2 text-xs sm:text-sm text-ink dark:text-ink-dark leading-snug {{ $node['side'] === 'right' ? 'text-right' : 'text-left' }}">
                            @if ($node['identifier'] && $editingItem === $node['identifier'])
                                <form wire:submit="saveEdit" class="space-y-1.5" dir="rtl">
                                    <input type="text" wire:model="editingText" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark text-xs sm:text-sm focus:border-primary focus:ring-primary" />
                                    <x-input-error :messages="$errors->get('editingText')" class="mt-1" />
                                    <div class="flex gap-1.5 justify-end">
                                        <button type="button" wire:click="cancelEdit" class="text-[11px] px-2 py-0.5 rounded text-ink-soft dark:text-ink-dark-soft hover:bg-ink-soft/10">إلغاء</button>
                                        <button type="submit" class="text-[11px] px-2 py-0.5 rounded bg-{{ $roadEnum->color() }} dark:bg-{{ $roadEnum->color() }}-dark text-white">حفظ</button>
                                    </div>
                                </form>
                            @else
                                {{ $node['text'] }}
                                @if ($node['identifier'])
                                    <div class="absolute -top-2 {{ $node['side'] === 'right' ? '-left-2' : '-right-2' }} flex gap-1">
                                        <button type="button" wire:click="startEdit('{{ $node['identifier'] }}', @js($node['text']))" title="عدّل"
                                            class="w-5 h-5 rounded-full bg-ink-soft/20 dark:bg-ink-dark-soft/30 text-ink-soft dark:text-ink-dark-soft text-[10px] flex items-center justify-center hover:bg-primary/20 hover:text-primary transition">✎</button>
                                        <button type="button" wire:click="removeItem('{{ $node['identifier'] }}')" title="شيله من الطريق"
                                            class="w-5 h-5 rounded-full bg-ink-soft/20 dark:bg-ink-dark-soft/30 text-ink-soft dark:text-ink-dark-soft text-xs leading-none flex items-center justify-center hover:bg-danger/20 hover:text-danger transition">×</button>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        @if ($footerStat)
            <div class="text-center mb-8">
                <span class="inline-block px-4 py-2 rounded-full bg-{{ $roadEnum->color() }}/10 dark:bg-{{ $roadEnum->color() }}-dark/10 text-{{ $roadEnum->color() }} dark:text-{{ $roadEnum->color() }}-dark text-xs sm:text-sm font-medium">
                    {{ $footerStat }}
                </span>
            </div>
        @endif

        {{-- Dramatic finale: the full, uncapped damage reveal at the end of طريق الهلاك --}}
        @if ($finale !== null)
            <div class="relative rounded-2xl border-2 border-danger bg-gradient-to-b from-black via-red-950 to-black p-6 sm:p-8 mb-8 overflow-hidden shadow-2xl shadow-danger/30">
                <div class="relative text-center mb-6">
                    <div class="text-4xl mb-2 animate-pulse">☠️</div>
                    <h3 class="text-xl sm:text-2xl font-black text-danger tracking-tight">ده كل اللي ممكن يحصلك لو وقعت</h3>
                    <p class="text-xs text-white/40 mt-1">مش كلام مبالغ فيه — ده سجّلته إنت بنفسك</p>
                </div>

                <div class="relative space-y-3">
                    @forelse ($finale as $item)
                        <div class="flex items-center gap-3 bg-white/[0.04] rounded-lg px-3 py-2.5 border border-danger/20">
                            <span class="text-lg shrink-0">🔥</span>

                            @if ($item['identifier'] && $editingItem === $item['identifier'])
                                <form wire:submit="saveEdit" class="flex-1 flex items-center gap-2" dir="rtl">
                                    <input type="text" wire:model="editingText" class="block w-full rounded-md border-gray-600 bg-gray-900 text-white text-sm focus:border-danger focus:ring-danger" />
                                    <button type="button" wire:click="cancelEdit" class="text-[11px] px-2 py-1 rounded text-white/50 hover:bg-white/10 shrink-0">إلغاء</button>
                                    <button type="submit" class="text-[11px] px-2 py-1 rounded bg-danger text-white shrink-0">حفظ</button>
                                </form>
                            @else
                                <span class="flex-1 text-sm sm:text-base text-white font-medium">{{ $item['text'] }}</span>
                                @if (isset($item['severity']))
                                    <div class="w-14 sm:w-20 h-2 rounded-full bg-white/15 border border-white/10 overflow-hidden shrink-0" title="درجة الخطورة {{ $item['severity'] }}%">
                                        <div class="h-full bg-danger" style="width: {{ $item['severity'] }}%"></div>
                                    </div>
                                @endif
                                @if ($item['identifier'])
                                    <div class="flex gap-1 shrink-0">
                                        <button type="button" wire:click="startEdit('{{ $item['identifier'] }}', @js($item['text']))" title="عدّل"
                                            class="w-5 h-5 rounded-full bg-white/10 text-white/40 text-[10px] flex items-center justify-center hover:bg-white/20 hover:text-white transition">✎</button>
                                        <button type="button" wire:click="removeItem('{{ $item['identifier'] }}')" title="شيله"
                                            class="w-5 h-5 rounded-full bg-white/10 text-white/40 text-xs leading-none flex items-center justify-center hover:bg-danger/40 hover:text-white transition">×</button>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @empty
                        <p class="text-center text-sm text-white/40">لسه مفيش أضرار مسجّلة — ضيف اللي انت متأكد إنه هيحصل لو وقعت</p>
                    @endforelse
                </div>

                <form wire:submit="addNote('finale')" class="relative flex gap-2 mt-4" dir="rtl">
                    <input type="text" wire:model="newFinaleNote" placeholder="ضيف حاجة تانية ممكن تحصلك..." class="block w-full rounded-md border-gray-700 bg-gray-900 text-white text-sm placeholder:text-white/30 focus:border-danger focus:ring-danger" />
                    <button type="submit" class="shrink-0 px-3 py-2 rounded-md bg-danger text-white text-sm hover:opacity-90">+</button>
                </form>
                <x-input-error :messages="$errors->get('newFinaleNote')" class="relative mt-1" />
            </div>
        @endif

        {{-- Victory finale: everything worth resisting for, at the end of طريق النجاة --}}
        @if ($victory)
            <div class="relative rounded-2xl border-2 border-success bg-gradient-to-b from-black via-emerald-950 to-black p-6 sm:p-8 mb-8 overflow-hidden shadow-2xl shadow-success/30">
                <div class="relative text-center mb-6">
                    <div class="text-4xl mb-2 animate-pulse">🌟</div>
                    <h3 class="text-xl sm:text-2xl font-black text-success tracking-tight">ده كل اللي ممكن تكسبه لو قاومت</h3>
                    <p class="text-xs text-white/40 mt-1">مش كلام تحفيزي فاضي — ده انت اللي حددت إنه يستاهل</p>
                </div>

                @if ($victory['currentStreak'] > 0 || $victory['bestStreak'] > 0 || $victory['cleanDays'] > 0)
                    <div class="relative flex flex-wrap justify-center gap-2 mb-5">
                        @if ($victory['currentStreak'] > 0)
                            <span class="px-3 py-1.5 rounded-full bg-white/10 border border-success/30 text-white text-xs sm:text-sm font-semibold">🔥 {{ $victory['currentStreak'] }} يوم متواصل دلوقتي</span>
                        @endif
                        @if ($victory['bestStreak'] > 0)
                            <span class="px-3 py-1.5 rounded-full bg-white/10 border border-success/30 text-white text-xs sm:text-sm font-semibold">🏆 أطول سلسلة: {{ $victory['bestStreak'] }} يوم</span>
                        @endif
                        @if ($victory['cleanDays'] > 0)
                            <span class="px-3 py-1.5 rounded-full bg-white/10 border border-success/30 text-white text-xs sm:text-sm font-semibold">✅ {{ $victory['cleanDays'] }} يوم نضافة إجمالي</span>
                        @endif
                    </div>
                @endif

                @if ($victory['achievedCount'] > 0)
                    <div class="relative text-center mb-5">
                        <span class="inline-block px-4 py-2 rounded-full bg-success/15 border border-success/30 text-success text-xs sm:text-sm font-medium">
                            🏅 حقّقت {{ $victory['achievedCount'] }} {{ $victory['achievedCount'] === 1 ? 'حلم' : 'أحلام' }} قبل كده — يعني ده ممكن تكرره تاني
                        </span>
                    </div>
                @endif

                <div class="relative space-y-3">
                    @forelse ($victory['items'] as $item)
                        <div class="flex items-start gap-3 bg-white/[0.04] rounded-lg px-3 py-2.5 border border-success/20">
                            <span class="text-lg shrink-0">{{ $item['icon'] ?? '✨' }}</span>

                            @if ($item['identifier'] && $editingItem === $item['identifier'])
                                <form wire:submit="saveEdit" class="flex-1 flex items-center gap-2" dir="rtl">
                                    <input type="text" wire:model="editingText" class="block w-full rounded-md border-gray-600 bg-gray-900 text-white text-sm focus:border-success focus:ring-success" />
                                    <button type="button" wire:click="cancelEdit" class="text-[11px] px-2 py-1 rounded text-white/50 hover:bg-white/10 shrink-0">إلغاء</button>
                                    <button type="submit" class="text-[11px] px-2 py-1 rounded bg-success text-white shrink-0">حفظ</button>
                                </form>
                            @else
                                <div class="flex-1">
                                    <span class="text-sm sm:text-base text-white font-medium">{{ $item['text'] }}</span>
                                    @if (! empty($item['benefits']))
                                        <div class="mt-1.5 flex flex-wrap gap-1">
                                            @foreach ($item['benefits'] as $benefit)
                                                <span class="text-[10px] sm:text-xs px-2 py-0.5 rounded-full bg-white/10 text-white/60">{{ $benefit }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                @if ($item['identifier'])
                                    <div class="flex gap-1 shrink-0">
                                        <button type="button" wire:click="startEdit('{{ $item['identifier'] }}', @js($item['text']))" title="عدّل"
                                            class="w-5 h-5 rounded-full bg-white/10 text-white/40 text-[10px] flex items-center justify-center hover:bg-white/20 hover:text-white transition">✎</button>
                                        <button type="button" wire:click="removeItem('{{ $item['identifier'] }}')" title="شيله"
                                            class="w-5 h-5 rounded-full bg-white/10 text-white/40 text-xs leading-none flex items-center justify-center hover:bg-success/40 hover:text-white transition">×</button>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @empty
                        <p class="text-center text-sm text-white/40">لسه مفيش حاجة هنا — ضيف اللي بتقاوم عشانه تحت</p>
                    @endforelse
                </div>

                <form wire:submit="addNote('finale')" class="relative flex gap-2 mt-4" dir="rtl">
                    <input type="text" wire:model="newFinaleNote" placeholder="ضيف حاجة تانية بتستاهل تقاوم عشانها..." class="block w-full rounded-md border-gray-700 bg-gray-900 text-white text-sm placeholder:text-white/30 focus:border-success focus:ring-success" />
                    <button type="submit" class="shrink-0 px-3 py-2 rounded-md bg-success text-white text-sm hover:opacity-90">+</button>
                </form>
                <x-input-error :messages="$errors->get('newFinaleNote')" class="relative mt-1" />
            </div>
        @endif

        {{-- Add your own stops --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
            <div class="rounded-xl border border-ink-soft/15 dark:border-ink-dark-soft/15 p-4">
                <label for="rr_start" class="block text-xs font-semibold text-ink-soft dark:text-ink-dark-soft mb-2">ضيف حاجة لبداية الطريق</label>
                <form wire:submit="addNote('start')" class="flex gap-2">
                    <input id="rr_start" type="text" wire:model="newStartNote" placeholder="اكتب هنا..." class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" />
                    <button type="submit" class="shrink-0 px-3 py-2 rounded-md bg-{{ $roadEnum->color() }} dark:bg-{{ $roadEnum->color() }}-dark text-white text-sm hover:opacity-90">+</button>
                </form>
                <x-input-error :messages="$errors->get('newStartNote')" class="mt-1" />
            </div>
            <div class="rounded-xl border border-ink-soft/15 dark:border-ink-dark-soft/15 p-4">
                <label for="rr_harvest" class="block text-xs font-semibold text-ink-soft dark:text-ink-dark-soft mb-2">ضيف حاجة للحصاد</label>
                <form wire:submit="addNote('harvest')" class="flex gap-2">
                    <input id="rr_harvest" type="text" wire:model="newHarvestNote" placeholder="اكتب هنا..." class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" />
                    <button type="submit" class="shrink-0 px-3 py-2 rounded-md bg-{{ $roadEnum->color() }} dark:bg-{{ $roadEnum->color() }}-dark text-white text-sm hover:opacity-90">+</button>
                </form>
                <x-input-error :messages="$errors->get('newHarvestNote')" class="mt-1" />
            </div>
        </div>

        {{-- CTA --}}
        <div class="text-center">
            <a href="{{ route('recovery.remember') }}" wire:navigate
                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-success dark:bg-success-dark text-white text-sm font-bold shadow-lg hover:scale-105 transition-all duration-200">
                🙏 خلاص، رجعت للمفترق واخترت طريق النجاة
            </a>
        </div>
    </div>
</div>
