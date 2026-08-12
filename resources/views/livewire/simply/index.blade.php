@php($circ = 2 * 3.14159 * 52)
<div class="min-h-screen bg-gradient-to-b from-success/10 via-transparent to-transparent dark:from-success/5">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Header --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-success/25 to-primary/15 dark:from-success/20 dark:to-primary/10 p-8 mb-8">
            {{-- decorative leaves --}}
            <svg class="absolute -top-4 -start-4 w-28 h-28 text-success/20 rotate-12" fill="currentColor" viewBox="0 0 24 24"><path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/></svg>
            <svg class="absolute bottom-2 end-2 w-20 h-20 text-primary/20 -rotate-12" fill="currentColor" viewBox="0 0 24 24"><path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/></svg>

            <div class="relative flex items-center justify-between gap-4">
                <div>
                    <p class="text-2xl font-bold text-ink dark:text-ink-dark">{{ $greeting }} 🌿</p>
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">{{ $dateLabel }}</p>
                    <p class="text-sm text-success dark:text-success-dark mt-3 max-w-xs leading-relaxed">{{ $line }}</p>
                </div>

                {{-- Progress ring --}}
                <div class="relative shrink-0">
                    <svg viewBox="0 0 120 120" class="w-24 h-24 -rotate-90">
                        <circle cx="60" cy="60" r="52" fill="none" stroke="currentColor" class="text-success/15" stroke-width="9" />
                        <circle cx="60" cy="60" r="52" fill="none" stroke="currentColor" class="text-success transition-all duration-500" stroke-width="9" stroke-linecap="round"
                            stroke-dasharray="{{ $circ }}" stroke-dashoffset="{{ $circ * (1 - $percent / 100) }}" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-xl font-bold text-ink dark:text-ink-dark">{{ $percent }}%</span>
                        <span class="text-[10px] text-ink-soft dark:text-ink-dark-soft">{{ $doneCount }}/{{ $totalCount }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Timeline --}}
        <section class="mb-8">
            <h2 class="text-sm font-semibold text-ink-soft dark:text-ink-dark-soft mb-4 flex items-center gap-2">⏱️ جدولك النهاردة</h2>
            @if ($timeline->isEmpty())
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft bg-surface-light dark:bg-surface-dark rounded-2xl p-5 text-center">مفيش مواعيد محددة بوقت النهاردة — يومك مفتوح 🍃</p>
            @else
                <div class="relative ps-6">
                    {{-- stem --}}
                    <div class="absolute top-2 bottom-2 start-[7px] w-0.5 bg-success/25"></div>
                    <div class="space-y-3">
                        @foreach ($timeline as $item)
                            <div wire:key="tl-{{ $loop->index }}" class="relative">
                                <div class="absolute -start-6 top-4 w-3.5 h-3.5 rounded-full border-2 border-success {{ $item['done'] ? 'bg-success' : 'bg-bg-light dark:bg-bg-dark' }}"></div>
                                <div class="flex items-center gap-3 rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-4">
                                    @if ($item['toggle'])
                                        <button type="button" wire:click="{{ $item['toggle'] }}({{ $item['id'] }})"
                                            class="shrink-0 w-6 h-6 rounded-full border-2 flex items-center justify-center transition {{ $item['done'] ? 'bg-success border-success text-white' : 'border-success/40 text-transparent hover:border-success' }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    @else
                                        <span class="shrink-0 text-lg">{{ $item['emoji'] }}</span>
                                    @endif
                                    <a href="{{ $item['url'] }}" wire:navigate class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-ink dark:text-ink-dark truncate {{ $item['done'] ? 'line-through opacity-60' : '' }}">{{ $item['title'] }}</p>
                                    </a>
                                    @if ($item['time'])<span class="shrink-0 text-xs font-medium text-success" dir="ltr">{{ $item['time'] }}</span>@endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </section>

        {{-- All-day checklist --}}
        <section class="space-y-6">
            @if ($untimedTasks->isNotEmpty())
                <div>
                    <h2 class="text-sm font-semibold text-ink-soft dark:text-ink-dark-soft mb-3">🗂️ تاسكات بدون وقت</h2>
                    <div class="space-y-2">
                        @foreach ($untimedTasks as $t)
                            <x-simply-check :done="$t->isDone()" wire:key="ut-{{ $t->id }}" toggle="toggleTask({{ $t->id }})" :title="$t->title" :emoji="$t->kind->emoji()" :url="route('tasks.show', $t)" />
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($habits->isNotEmpty())
                <div>
                    <h2 class="text-sm font-semibold text-ink-soft dark:text-ink-dark-soft mb-3">🔁 عاداتك</h2>
                    <div class="space-y-2">
                        @foreach ($habits as $h)
                            <x-simply-check :done="$h->logs->isNotEmpty()" wire:key="hb-{{ $h->id }}" toggle="toggleHabit({{ $h->id }})" :title="$h->title" emoji="🌱" :url="route('habits.show', $h)" />
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($challenges->isNotEmpty())
                <div>
                    <h2 class="text-sm font-semibold text-ink-soft dark:text-ink-dark-soft mb-3">🔥 تحدياتك</h2>
                    <div class="space-y-2">
                        @foreach ($challenges as $c)
                            <x-simply-check :done="$c->logs->isNotEmpty()" wire:key="ch-{{ $c->id }}" toggle="toggleChallenge({{ $c->id }})" :title="$c->title" emoji="🔥" :url="route('challenges.show', $c)" />
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Spiritual row (view + links) --}}
            <div>
                <h2 class="text-sm font-semibold text-ink-soft dark:text-ink-dark-soft mb-3">🤍 روحانياتك</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <a href="{{ route('religion.prayers') }}" wire:navigate class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-4 flex items-center gap-3">
                        <span class="text-xl">🕌</span>
                        <div><p class="text-sm font-medium text-ink dark:text-ink-dark">الصلوات</p><p class="text-xs {{ $prayerDone >= 5 ? 'text-success' : 'text-ink-soft dark:text-ink-dark-soft' }}">{{ $prayerDone }}/5</p></div>
                    </a>
                    <a href="{{ route('religion.quran') }}" wire:navigate class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-4 flex items-center gap-3">
                        <span class="text-xl">📖</span>
                        <div><p class="text-sm font-medium text-ink dark:text-ink-dark">ورد القرآن</p><p class="text-xs {{ $quranDone ? 'text-success' : 'text-ink-soft dark:text-ink-dark-soft' }}">{{ $quranDone ? 'تم ✓' : 'لسه' }}</p></div>
                    </a>
                    @if ($hasTopics)
                        <a href="{{ route('recovery.nutrition') }}" wire:navigate class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-4 flex items-center gap-3">
                            <span class="text-xl">🧠</span>
                            <div><p class="text-sm font-medium text-ink dark:text-ink-dark">التغذية الذهنية</p><p class="text-xs {{ $nutritionDone ? 'text-success' : 'text-ink-soft dark:text-ink-dark-soft' }}">{{ $nutritionDone ? 'تم ✓' : 'لسه' }}</p></div>
                        </a>
                    @endif
                </div>
            </div>
        </section>
    </div>
</div>
