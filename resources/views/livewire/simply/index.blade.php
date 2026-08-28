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

            {{-- Motivational line — prominent --}}
            <div class="relative mt-6 pt-5 border-t border-success/20">
                <p class="text-lg sm:text-2xl font-bold text-ink dark:text-ink-dark leading-relaxed">🌿 {{ $line }}</p>
            </div>
        </div>

        {{-- Appointments — calendar-based schedule items, independent of tasks --}}
        <section class="mb-8">
            <h2 class="text-sm font-semibold text-ink-soft dark:text-ink-dark-soft mb-4 flex items-center gap-2">📅 المواعيد</h2>
            @if ($appointments->isEmpty())
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft bg-surface-light dark:bg-surface-dark rounded-2xl p-5 text-center">مفيش مواعيد النهاردة — يومك مفتوح 🍃</p>
            @else
                <div class="relative ps-6">
                    {{-- stem --}}
                    <div class="absolute top-2 bottom-2 start-[7px] w-0.5 bg-success/25"></div>
                    <div class="space-y-3">
                        @foreach ($appointments as $item)
                            <div wire:key="ap-{{ $loop->index }}" class="relative">
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
                                        @if (! empty($item['kind']))<span class="inline-block mt-0.5 text-[10px] px-2 py-0.5 rounded-full bg-primary/10 text-primary dark:text-primary-dark">{{ $item['kind'] }}</span>@endif
                                    </a>
                                    @if ($item['time'])<span class="shrink-0 text-xs font-medium text-success" dir="ltr">{{ $item['time'] }}</span>@endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </section>

        {{-- Timed tasks --}}
        <section class="mb-8">
            <h2 class="text-sm font-semibold text-ink-soft dark:text-ink-dark-soft mb-4 flex items-center gap-2">⏰ تاسكات بوقت</h2>
            @if ($timedTasks->isEmpty())
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft bg-surface-light dark:bg-surface-dark rounded-2xl p-5 text-center">مفيش تاسكات بوقت محدد النهاردة.</p>
            @else
                <div class="relative ps-6">
                    {{-- stem --}}
                    <div class="absolute top-2 bottom-2 start-[7px] w-0.5 bg-success/25"></div>
                    <div class="space-y-3">
                        @foreach ($timedTasks as $item)
                            <div wire:key="tt-{{ $loop->index }}" class="relative">
                                <div class="absolute -start-6 top-4 w-3.5 h-3.5 rounded-full border-2 border-success {{ $item['done'] ? 'bg-success' : 'bg-bg-light dark:bg-bg-dark' }}"></div>
                                <div class="flex items-center gap-3 rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-4 {{ ($item['important'] ?? false) ? 'ring-2 ring-warning/50 bg-warning/5' : '' }}">
                                    @if ($item['important'] ?? false)<span class="shrink-0 text-warning">⭐</span>@endif
                                    <button type="button" wire:click="{{ $item['toggle'] }}({{ $item['id'] }})"
                                        class="shrink-0 w-6 h-6 rounded-full border-2 flex items-center justify-center transition {{ $item['done'] ? 'bg-success border-success text-white' : 'border-success/40 text-transparent hover:border-success' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                    <a href="{{ $item['url'] }}" wire:navigate class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-ink dark:text-ink-dark truncate {{ $item['done'] ? 'line-through opacity-60' : '' }}">{{ $item['title'] }}</p>
                                        @if (! empty($item['kind']))<span class="inline-block mt-0.5 text-[10px] px-2 py-0.5 rounded-full bg-primary/10 text-primary dark:text-primary-dark">{{ $item['kind'] }}</span>@endif
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
                            <x-simply-check :done="$t->isDone()" :important="$t->is_important" wire:key="ut-{{ $t->id }}" toggle="toggleTask({{ $t->id }})" :title="$t->title" :emoji="$t->kind->emoji()" :hint="$t->kind->label()" :url="route('tasks.show', $t)" />
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

            @if ($comfortExperiences->isNotEmpty())
                <div>
                    <h2 class="text-sm font-semibold text-ink-soft dark:text-ink-dark-soft mb-3">🚀 خارج الزون</h2>
                    <div class="space-y-2">
                        @foreach ($comfortExperiences as $ce)
                            <x-simply-check :done="$ce->status->value === 'done'" wire:key="ce-{{ $ce->id }}" toggle="toggleComfortExperience({{ $ce->id }})" :title="$ce->title" :emoji="$ce->kind->emoji()" :hint="$ce->kind->label()" :url="route('comfort-zone')" />
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Recovery band --}}
            <div>
                <h2 class="text-sm font-semibold text-ink-soft dark:text-ink-dark-soft mb-3">🌱 التعافي</h2>
                <div class="space-y-2">
                    {{-- Nightly check: prepared for tonight or stayed up --}}
                    <div class="flex items-center justify-between gap-3 rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-4">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-ink dark:text-ink-dark">🌙 استعديت لليلة النهاردة ولا سهرت؟</p>
                            @if ($nightStatus === 'missed')<p class="text-[11px] text-danger">سهرت ومااستعديتش لليلة</p>@elseif ($nightStatus === 'done')<p class="text-[11px] text-success">أحسنت — نمت بخير واستعديت 🤍</p>@endif
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" wire:click="setNight('done')"
                                class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $nightStatus === 'done' ? 'bg-success text-white' : 'bg-success/15 text-success hover:bg-success/25' }}">✓ استعديت لليلة</button>
                            <button type="button" wire:click="setNight('missed')"
                                class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $nightStatus === 'missed' ? 'bg-danger text-white' : 'bg-danger/15 text-danger hover:bg-danger/25' }}">😴 سهرت</button>
                        </div>
                    </div>
                    @if ($hasTopics && ! $nutritionDone)
                        <a href="{{ route('recovery.nutrition') }}" wire:navigate class="flex items-center gap-3 rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-4">
                            <span class="text-xl">📚</span>
                            <p class="text-sm font-medium text-ink dark:text-ink-dark">عندك تغذية ذهنية تتقرأ النهاردة</p>
                        </a>
                    @endif

                    {{-- Daily blessing reminder — health & wellness, easy to take for granted --}}
                    <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-4">
                        <p class="text-sm font-medium text-ink dark:text-ink-dark mb-1.5">🤍 نعمة النهاردة: الصحة والعافية</p>
                        <p class="text-xs text-ink-soft dark:text-ink-dark-soft leading-relaxed">
                            الصحة والعافية نعمة عظيمة كتير مننساها. لو فيه حاجة بتشتكي منها، افتكر اللي مبتلين بأمراض خبيثة وعمليات وألم كل يوم — اللهم عافينا. يومك العادي النهاردة نعمة كبيرة أوي والله.
                        </p>
                        <p class="text-xs text-ink dark:text-ink-dark leading-relaxed mt-3 italic border-t border-ink-soft/10 dark:border-ink-dark-soft/10 pt-3">
                            «مَن أصبحَ معافًى في بدنِه، آمنًا في سِربِه، عندَه قوتُ يومِه، فكأنَّما حِيزَت له الدُّنيا بحذافيرِها»
                            <span class="block not-italic text-[11px] text-ink-soft dark:text-ink-dark-soft mt-1">— حديث شريف</span>
                        </p>
                        <a href="{{ route('recovery.blessings') }}" wire:navigate class="inline-block text-xs text-primary dark:text-primary-dark hover:underline mt-3">🌍 شوف باقي النعم اللي بغفل عنها ←</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
