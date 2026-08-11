<div class="py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Breadcrumb: back to list, or up to parent goal when this is a sub-goal --}}
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('goals.index') }}" wire:navigate class="text-primary dark:text-primary-dark hover:underline">← كل الأهداف</a>
            @if ($parent)
                <span class="text-ink-soft dark:text-ink-dark-soft">/</span>
                <a href="{{ route('goals.show', $parent) }}" wire:navigate class="text-primary dark:text-primary-dark hover:underline">{{ $parent->title }}</a>
            @endif
        </div>

        <div class="mt-3 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 border-t-4" style="border-top-color: {{ $goal->color }}">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">{{ $goal->title }}</h1>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $goal->status->color() }}/15 text-{{ $goal->status->color() }}">
                            {{ $goal->status->label() }}
                        </span>
                        @if ($goal->isSubGoal())
                            <span class="text-xs px-2 py-0.5 rounded-full bg-secondary/25 text-ink dark:text-ink-dark">هدف فرعي</span>
                        @endif
                    </div>
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">{{ $goal->category->label() }}</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    @if ($goal->canClose())
                        <button type="button" wire:click="closeGoal" class="px-3 py-1.5 rounded-lg bg-success text-white text-sm hover:opacity-90 transition">
                            إغلاق الهدف
                        </button>
                    @endif
                    <button type="button" wire:click="editGoal" class="px-3 py-1.5 rounded-lg border border-primary/40 text-primary dark:text-primary-dark text-sm hover:bg-primary/10 transition">
                        تعديل
                    </button>
                </div>
            </div>

            {{-- Dates + remaining-days countdown --}}
            @if ($goal->start_date || $goal->target_date)
                @php($remaining = $goal->remainingDays())
                <div class="flex flex-wrap items-center gap-4 mt-4 text-sm">
                    @if ($goal->start_date)
                        <span class="text-ink-soft dark:text-ink-dark-soft">🚩 البداية: {{ $goal->start_date->translatedFormat('j M Y') }}</span>
                    @endif
                    @if ($goal->target_date)
                        <span class="text-ink-soft dark:text-ink-dark-soft">🏁 النهاية: {{ $goal->target_date->translatedFormat('j M Y') }}</span>
                        <span @class([
                            'font-medium px-2.5 py-1 rounded-full text-xs',
                            'bg-danger/15 text-danger' => $goal->isOverdue(),
                            'bg-success/15 text-success' => ! $goal->isOverdue(),
                        ])>
                            @if ($remaining > 0)
                                باقٍ {{ $remaining }} يوم لإتمام الهدف (≈ {{ $goal->remainingWeeks() }} أسبوع)
                            @elseif ($remaining === 0)
                                ينتهي اليوم
                            @else
                                متأخر بـ {{ abs($remaining) }} يوم
                            @endif
                        </span>
                    @endif
                </div>

                {{-- Time progress bar --}}
                @if ($goal->timeProgressPercent() !== null)
                    <div class="mt-3">
                        <div class="flex justify-between text-xs text-ink-soft dark:text-ink-dark-soft mb-1">
                            <span>الوقت المنقضي</span>
                            <span>{{ $goal->timeProgressPercent() }}%</span>
                        </div>
                        <div class="h-2 rounded-full bg-ink-soft/15 dark:bg-ink-dark-soft/15 overflow-hidden">
                            <div class="h-full rounded-full bg-primary dark:bg-primary-dark transition-all" style="width: {{ $goal->timeProgressPercent() }}%"></div>
                        </div>
                    </div>
                @endif
            @endif

            {{-- Overall completion (bottom-up: sub-goals + linked tasks) --}}
            @php($completion = $goal->completionPercent())
            <div class="mt-4">
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium text-ink dark:text-ink-dark">نسبة الإنجاز الكلية</span>
                    <span class="font-semibold text-success">{{ $completion }}%</span>
                </div>
                <div class="h-3 rounded-full bg-ink-soft/15 dark:bg-ink-dark-soft/15 overflow-hidden">
                    <div class="h-full rounded-full bg-success transition-all" style="width: {{ $completion }}%"></div>
                </div>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">
                    محسوبة من إنجاز الأهداف الفرعية والتاسكات المربوطة بالهدف.
                </p>
            </div>
        </div>

        {{-- Closing review summary (once the goal is closed) --}}
        @if ($goal->review)
            <div class="mt-6 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-ink dark:text-ink-dark">مراجعة الإغلاق</h3>
                    <span class="text-sm font-semibold text-primary dark:text-primary-dark">التحسن: {{ $goal->review->improvement_percent }}%</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-danger mb-1">التقصيرات</p>
                        <ul class="list-disc ps-5 space-y-0.5 text-sm text-ink-soft dark:text-ink-dark-soft">
                            @forelse ($goal->review->shortcomings ?? [] as $point)
                                <li>{{ $point }}</li>
                            @empty
                                <li class="list-none text-ink-soft dark:text-ink-dark-soft">—</li>
                            @endforelse
                        </ul>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-success mb-1">المميزات</p>
                        <ul class="list-disc ps-5 space-y-0.5 text-sm text-ink-soft dark:text-ink-dark-soft">
                            @forelse ($goal->review->strengths ?? [] as $point)
                                <li>{{ $point }}</li>
                            @empty
                                <li class="list-none text-ink-soft dark:text-ink-dark-soft">—</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
                @if ($goal->review->note)
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-3 whitespace-pre-line">{{ $goal->review->note }}</p>
                @endif
            </div>
        @endif

        {{-- Sub-goals panel --}}
        <div class="mt-6 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-ink dark:text-ink-dark">الأهداف الفرعية</h3>
                <button type="button" wire:click="addSubGoal" class="text-sm px-3 py-1.5 rounded-lg bg-primary dark:bg-primary-dark text-white hover:opacity-90 transition">
                    + هدف فرعي
                </button>
            </div>

            @forelse ($children as $child)
                <a href="{{ route('goals.show', $child) }}" wire:navigate wire:key="child-{{ $child->id }}"
                   class="flex items-center justify-between gap-3 py-3 border-b border-ink-soft/10 dark:border-ink-dark-soft/10 last:border-0 hover:opacity-80">
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background: {{ $child->color }}"></span>
                        <div>
                            <p class="text-sm text-ink dark:text-ink-dark">{{ $child->title }}</p>
                            <div class="flex items-center gap-2 text-xs text-ink-soft dark:text-ink-dark-soft mt-0.5">
                                <span>{{ $child->status->label() }}</span>
                                @if ($child->children_count > 0)<span>• {{ $child->children_count }} فرعي</span>@endif
                            </div>
                        </div>
                    </div>
                    @if ($child->target_date)
                        @php($cr = $child->remainingDays())
                        <span @class([
                            'text-xs px-2 py-0.5 rounded-full shrink-0',
                            'bg-danger/15 text-danger' => $child->isOverdue(),
                            'bg-primary/10 text-primary dark:text-primary-dark' => ! $child->isOverdue(),
                        ])>
                            {{ $cr > 0 ? "باقٍ {$cr} يوم" : ($cr === 0 ? 'اليوم' : 'متأخر') }}
                        </span>
                    @endif
                </a>
            @empty
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft text-center py-6">لا توجد أهداف فرعية. قسّم هدفك إلى خطوات أصغر للتركيز.</p>
            @endforelse
        </div>

        {{-- Tabs --}}
        <div class="mt-6 border-b border-ink-soft/15 dark:border-ink-dark-soft/15">
            <nav class="flex flex-wrap gap-1 -mb-px">
                @foreach ($tabs as $key => $label)
                    <button
                        type="button"
                        wire:click="$set('tab', '{{ $key }}')"
                        @class([
                            'px-4 py-2.5 text-sm border-b-2 transition',
                            'border-primary text-primary dark:text-primary-dark dark:border-primary-dark font-medium' => $tab === $key,
                            'border-transparent text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark' => $tab !== $key,
                        ])
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- Tab content --}}
        <div class="mt-6">
            @if ($tab === 'overview')
                <div class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                    <h3 class="font-semibold text-ink dark:text-ink-dark mb-2">الوصف</h3>
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft whitespace-pre-line">
                        {{ $goal->description ?: 'لا يوجد وصف لهذا الهدف بعد.' }}
                    </p>
                </div>

            @elseif ($tab === 'logs')
                <div class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-ink dark:text-ink-dark">السجلات اليومية</h3>
                        <button type="button" wire:click="$dispatch('create-log', { goalId: {{ $goal->id }} })" class="text-sm px-3 py-1.5 rounded-lg bg-primary dark:bg-primary-dark text-white hover:opacity-90 transition">
                            + سجل اليوم
                        </button>
                    </div>

                    @forelse ($goal->dailyLogs()->latest('date')->get() as $log)
                        <div wire:key="log-{{ $log->id }}" class="flex items-start justify-between gap-4 py-3 border-b border-ink-soft/10 dark:border-ink-dark-soft/10 last:border-0">
                            <div>
                                <p class="text-sm text-ink dark:text-ink-dark">{{ $log->date->translatedFormat('l، j M Y') }}</p>
                                <div class="flex items-center gap-3 mt-1 text-xs text-ink-soft dark:text-ink-dark-soft">
                                    @if ($log->mood)<span>😊 المزاج: {{ $log->mood }}/10</span>@endif
                                    @if ($log->difficulty)<span>⚡ الصعوبة: {{ $log->difficulty }}/10</span>@endif
                                </div>
                                @if ($log->note)<p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">{{ $log->note }}</p>@endif
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" wire:click="$dispatch('edit-log', { log: {{ $log->id }} })" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                                <button type="button" wire:click="$dispatch('delete-log', { log: {{ $log->id }} })" wire:confirm="حذف هذا السجل؟" class="text-xs text-danger hover:underline">حذف</button>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-ink-soft dark:text-ink-dark-soft text-center py-8">لا توجد سجلات لهذا الهدف بعد.</p>
                    @endforelse
                </div>

            @else
                {{-- Stubbed tabs for later phases --}}
                <div class="rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30 p-10 text-center">
                    <p class="text-ink-soft dark:text-ink-dark-soft">
                        قسم «{{ $tabs[$tab] }}» سيتم بناؤه في مرحلة لاحقة.
                    </p>
                </div>
            @endif
        </div>
    </div>

    {{-- Goal edit modal + close-goal modal + log create/edit modal --}}
    <livewire:goals.manage-goal />
    <livewire:goals.close-goal />
    <livewire:daily-logs.manage-log @log-saved="$refresh" />
</div>
