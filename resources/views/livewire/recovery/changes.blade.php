<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <a href="{{ route('recovery.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← التعافي</a>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mt-1">🧭 تغييرات جذرية في شخصيتي</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1 max-w-md leading-relaxed">
                    مش نية بس — كل تغيير هنا ليه خطوات فعلية بتتابعها لحد ما تتحقق.
                </p>
            </div>
            <button type="button" wire:click="createChange" class="shrink-0 px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium shadow-sm hover:opacity-90 transition">+ تغيير جديد</button>
        </div>

        @if ($activeChanges->isEmpty() && $completedChanges->isEmpty())
            <div class="text-center py-20 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-4xl mb-3">🧭</p>
                <p class="text-ink-soft dark:text-ink-dark-soft">لسه مفيش تغييرات مسجلة. ابدأ بأول حاجة عايز تصلحها في نفسك.</p>
            </div>
        @else
            @if ($activeChanges->isNotEmpty())
                <div class="space-y-3">
                    @foreach ($activeChanges as $change)
                        @php
                            $circumference = 100.53;
                            $offset = $circumference - ($circumference * $change->progress / 100);
                        @endphp
                        <a wire:key="change-{{ $change->id }}" href="{{ route('recovery.changes.show', $change) }}" wire:navigate
                           class="group flex items-center gap-4 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md border border-transparent hover:border-primary/20 dark:hover:border-primary-dark/20 p-4 transition-all duration-200">

                            <div class="relative w-14 h-14 shrink-0">
                                <svg viewBox="0 0 36 36" class="w-14 h-14 -rotate-90">
                                    <circle cx="18" cy="18" r="16" fill="none" stroke-width="3" class="stroke-ink-soft/15 dark:stroke-ink-dark-soft/15" />
                                    <circle cx="18" cy="18" r="16" fill="none" stroke-width="3" stroke-linecap="round"
                                            class="stroke-primary dark:stroke-primary-dark transition-all duration-500"
                                            stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $offset }}" />
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center text-lg">
                                    {{ $change->icon ?: '🔥' }}
                                </div>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-bold text-ink dark:text-ink-dark group-hover:text-primary dark:group-hover:text-primary-dark transition truncate">{{ $change->title }}</h3>
                                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-{{ $change->status->color() }}/15 text-{{ $change->status->color() }} shrink-0">{{ $change->status->label() }}</span>
                                </div>
                                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">
                                    {{ $change->total_steps > 0 ? "{$change->done_steps} من {$change->total_steps} خطوات" : 'لسه من غير خطوات' }}
                                    <span class="mx-1">·</span>
                                    {{ $change->daysSinceStart() }} يوم من البداية
                                    @if ($change->target_date)
                                        <span class="mx-1">·</span>
                                        @php($left = $change->daysRemaining())
                                        {{ $left > 0 ? "باقي {$left} يوم" : 'الموعد المستهدف فات' }}
                                    @endif
                                </p>
                            </div>

                            <span class="text-sm font-extrabold text-primary dark:text-primary-dark shrink-0">{{ $change->progress }}%</span>
                        </a>
                    @endforeach
                </div>
            @endif

            @if ($completedChanges->isNotEmpty())
                <div class="pt-2">
                    <h2 class="text-sm font-bold text-ink-soft dark:text-ink-dark-soft mb-3 flex items-center gap-1.5">🏆 تغييرات اتحققت</h2>
                    <div class="flex flex-wrap gap-3">
                        @foreach ($completedChanges as $change)
                            <a wire:key="done-{{ $change->id }}" href="{{ route('recovery.changes.show', $change) }}" wire:navigate
                               class="flex items-center gap-2 pe-3 ps-2 py-2 rounded-full bg-success/10 dark:bg-success-dark/10 border border-success/20 dark:border-success-dark/20 hover:opacity-80 transition">
                                <span class="text-lg">{{ $change->icon ?: '🔥' }}</span>
                                <span class="text-xs font-medium text-ink dark:text-ink-dark">{{ $change->title }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </div>

    <livewire:recovery.manage-change />
</div>
