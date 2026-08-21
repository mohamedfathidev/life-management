<div class="py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Header: a softly glowing banner, distinct from the plain list-style pages --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary/15 via-secondary/10 to-transparent dark:from-primary-dark/20 dark:via-secondary-dark/10 p-6 sm:p-8">
            <div class="absolute -top-10 -end-10 w-40 h-40 rounded-full bg-secondary/25 dark:bg-secondary-dark/20 blur-2xl"></div>
            <div class="absolute -bottom-14 -start-14 w-48 h-48 rounded-full bg-primary/20 dark:bg-primary-dark/20 blur-2xl"></div>

            <div class="relative flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <a href="{{ route('recovery.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← التعافي</a>
                    <h1 class="text-3xl font-extrabold text-ink dark:text-ink-dark mt-2">✨ أحلام التعافي وفوائدها</h1>
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-2 max-w-md leading-relaxed">
                        مش بس بتهرب من حاجة — إنت ماشي لحاجة. اكتب حلمك، وليه يستاهل كل التعب.
                    </p>
                </div>
                <button type="button" wire:click="createDream" class="shrink-0 px-5 py-2.5 rounded-xl bg-primary dark:bg-primary-dark text-white text-sm font-semibold shadow-md hover:opacity-90 hover:shadow-lg transition">
                    + حلم جديد
                </button>
            </div>
        </div>

        {{-- Active dreams: the vision board --}}
        @if ($activeDreams->isEmpty() && $achievedDreams->isEmpty())
            <div class="text-center py-20 rounded-3xl border-2 border-dashed border-primary/25 dark:border-primary-dark/25">
                <p class="text-5xl mb-3">🌅</p>
                <p class="text-ink-soft dark:text-ink-dark-soft">لسه مفيش أحلام مكتوبة. ابدأ بحلم واحد، وحدد ليه هو مهم ليك.</p>
            </div>
        @else
            @if ($activeDreams->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($activeDreams as $dream)
                        @php
                            $palette = ['primary', 'secondary', 'success'][$loop->index % 3];
                        @endphp
                        <div wire:key="dream-{{ $dream->id }}"
                             class="group relative overflow-hidden rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-lg border border-transparent hover:border-{{ $palette }}/30 dark:hover:border-{{ $palette }}-dark/30 p-5 transition-all duration-300 hover:-translate-y-0.5">
                            <div class="absolute -top-8 -end-8 w-24 h-24 rounded-full bg-{{ $palette }}/10 dark:bg-{{ $palette }}-dark/10 group-hover:scale-125 transition-transform duration-500"></div>

                            <div class="relative flex items-start justify-between gap-2">
                                <div class="w-12 h-12 rounded-2xl bg-{{ $palette }}/15 dark:bg-{{ $palette }}-dark/20 flex items-center justify-center text-2xl shadow-sm">
                                    {{ $dream->icon ?: '🌅' }}
                                </div>
                                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition">
                                    <button type="button" wire:click="$dispatch('edit-dream', { dream: {{ $dream->id }} })" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                                    <button type="button" wire:click="$dispatch('delete-dream', { dream: {{ $dream->id }} })" wire:confirm="حذف هذا الحلم؟" class="text-xs text-danger hover:underline">حذف</button>
                                </div>
                            </div>

                            <h3 class="relative font-bold text-base text-ink dark:text-ink-dark mt-3 leading-snug">
                                {{ $dream->title }}
                            </h3>

                            @if (! empty($dream->benefits))
                                <ul class="relative mt-3 space-y-1.5">
                                    @foreach ($dream->benefits as $benefit)
                                        <li class="flex items-start gap-1.5 text-xs text-ink-soft dark:text-ink-dark-soft">
                                            <span class="text-{{ $palette }} dark:text-{{ $palette }}-dark mt-0.5">✦</span>
                                            <span class="leading-relaxed">{{ $benefit }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            @if ($dream->recovery)
                                <span class="relative inline-block mt-3 text-[11px] px-2 py-0.5 rounded-full bg-{{ $palette }}/10 text-{{ $palette }} dark:text-{{ $palette }}-dark font-medium">
                                    في {{ $dream->recovery->title }}
                                </span>
                            @endif

                            <button type="button" wire:click="toggleAchieved({{ $dream->id }})"
                                    class="relative w-full mt-4 pt-3 border-t border-gray-100 dark:border-gray-800/60 flex items-center justify-center gap-1.5 text-xs font-semibold text-success dark:text-success-dark hover:opacity-80 transition">
                                🏁 تحقق الحلم
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Achieved dreams: a hall of fame strip --}}
            @if ($achievedDreams->isNotEmpty())
                <div class="pt-2">
                    <h2 class="text-sm font-bold text-ink-soft dark:text-ink-dark-soft mb-3 flex items-center gap-1.5">
                        🏆 أحلام اتحققت
                    </h2>
                    <div class="flex flex-wrap gap-3">
                        @foreach ($achievedDreams as $dream)
                            <div wire:key="achieved-{{ $dream->id }}" class="group relative flex items-center gap-2 pe-3 ps-2 py-2 rounded-full bg-success/10 dark:bg-success-dark/10 border border-success/20 dark:border-success-dark/20">
                                <span class="text-lg">{{ $dream->icon ?: '🌅' }}</span>
                                <span class="text-xs font-medium text-ink dark:text-ink-dark line-through decoration-success/60">{{ $dream->title }}</span>
                                <button type="button" wire:click="toggleAchieved({{ $dream->id }})" title="رجّعه نشط" class="text-[10px] text-ink-soft dark:text-ink-dark-soft opacity-0 group-hover:opacity-100 hover:text-danger transition">✕</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </div>

    <livewire:recovery.manage-dream />
</div>
