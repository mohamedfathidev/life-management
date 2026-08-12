<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Breadcrumb + day navigation --}}
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('planner') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← المخطط الأسبوعي</a>
            <div class="flex items-center gap-2">
                <a href="{{ route('planner.day', $prevDate) }}" wire:navigate class="px-2.5 py-1 rounded-lg bg-surface-light dark:bg-surface-dark shadow-sm text-sm hover:opacity-90">اليوم السابق ›</a>
                <a href="{{ route('planner.day', $nextDate) }}" wire:navigate class="px-2.5 py-1 rounded-lg bg-surface-light dark:bg-surface-dark shadow-sm text-sm hover:opacity-90">‹ اليوم التالي</a>
            </div>
        </div>

        {{-- Day header card --}}
        <div class="mt-3 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6"
             @if ($day->isStarted() && ! $day->isClosed()) wire:poll.60s @endif>
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">{{ $day->date->translatedFormat('l، j F Y') }}</h1>
                    <div class="flex items-center gap-2 mt-2 text-sm">
                        @if ($day->isClosed())
                            <span class="px-2 py-0.5 rounded-full bg-success/15 text-success">مُقفل</span>
                            @if ($day->rating)<span class="text-ink-soft dark:text-ink-dark-soft">التقييم: {{ $day->rating }}/10</span>@endif
                        @elseif ($day->isStarted())
                            <span class="px-2 py-0.5 rounded-full bg-primary/10 text-primary dark:text-primary-dark">جارٍ</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full bg-ink-soft/10 text-ink-soft dark:text-ink-dark-soft">لم يبدأ</span>
                        @endif
                    </div>
                </div>

                {{-- Day actions --}}
                @if (! $day->isClosed() && $day->isStarted())
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="toggleBreak"
                            @class([
                                'px-4 py-2 rounded-lg text-sm font-medium transition',
                                'bg-warning/20 text-warning' => $ongoingBreak,
                                'bg-surface-light dark:bg-surface-dark border border-ink-soft/20 text-ink dark:text-ink-dark hover:bg-ink-soft/5' => ! $ongoingBreak,
                            ])>
                            {{ $ongoingBreak ? 'إنهاء البريك' : 'بدء بريك' }}
                        </button>
                        <button type="button" wire:click="requestCloseDay" class="px-4 py-2 rounded-lg bg-success text-white text-sm font-medium hover:opacity-90 transition">
                            تقفيل اليوم
                        </button>
                    </div>
                @endif
            </div>

            {{-- Start-time control (you decide when the day started) --}}
            @if (! $day->isClosed())
                <div class="mt-4 flex flex-wrap items-end gap-3 rounded-lg bg-bg-light dark:bg-bg-dark p-3">
                    <div>
                        <label for="start_time" class="text-xs text-ink-soft dark:text-ink-dark-soft">وقت بداية اليوم</label>
                        <input id="start_time" type="time" wire:model="startTimeInput"
                               class="mt-1 block rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" dir="ltr" />
                    </div>
                    <button type="button" wire:click="setStart" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">
                        {{ $day->isStarted() ? 'تعديل البداية' : 'تسجيل البداية' }}
                    </button>
                    <button type="button" wire:click="startNow" class="px-3 py-2 rounded-lg border border-ink-soft/20 text-ink dark:text-ink-dark text-sm hover:bg-ink-soft/5 transition">
                        الآن
                    </button>
                    @error('startTimeInput') <span class="text-xs text-danger w-full">{{ $message }}</span> @enderror
                </div>
            @endif

            {{-- Time metrics --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-5">
                <div class="rounded-lg bg-bg-light dark:bg-bg-dark p-3">
                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft">البداية</p>
                    <p class="text-sm font-semibold text-ink dark:text-ink-dark mt-1">{{ $day->started_at?->translatedFormat('g:i A') ?? '—' }}</p>
                </div>
                <div class="rounded-lg bg-bg-light dark:bg-bg-dark p-3">
                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft">النهاية</p>
                    <p class="text-sm font-semibold text-ink dark:text-ink-dark mt-1">{{ $day->ended_at?->translatedFormat('g:i A') ?? '—' }}</p>
                </div>
                <div class="rounded-lg bg-bg-light dark:bg-bg-dark p-3">
                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft">البريكات</p>
                    <p class="text-sm font-semibold text-ink dark:text-ink-dark mt-1">{{ $day->breakMinutes() }} د</p>
                </div>
                <div class="rounded-lg bg-primary/10 dark:bg-primary-dark/15 p-3">
                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft">ساعات العمل الفعلية</p>
                    <p class="text-sm font-semibold text-primary dark:text-primary-dark mt-1">{{ $workedLabel }}</p>
                </div>
            </div>

            @if ($ongoingBreak)
                <p class="text-xs text-warning mt-3">☕ في بريك منذ {{ $ongoingBreak->started_at->translatedFormat('g:i A') }}</p>
            @endif

            {{-- Breaks log: each break from → to --}}
            @if ($day->breaks->isNotEmpty())
                <div class="mt-4">
                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft mb-2">البريكات ({{ $day->breaks->count() }})</p>
                    <div class="space-y-1.5">
                        @foreach ($day->breaks as $break)
                            <div wire:key="break-{{ $break->id }}" class="flex items-center justify-between gap-3 rounded-lg bg-bg-light dark:bg-bg-dark px-3 py-2 text-sm">
                                <span class="text-ink dark:text-ink-dark" dir="ltr">
                                    ☕ {{ $break->started_at->translatedFormat('g:i A') }}
                                    →
                                    @if ($break->ended_at)
                                        {{ $break->ended_at->translatedFormat('g:i A') }}
                                        <span class="text-ink-soft dark:text-ink-dark-soft">({{ $break->durationMinutes() }} د)</span>
                                    @else
                                        <span class="text-warning">جارٍ الآن…</span>
                                    @endif
                                </span>
                                @unless ($day->isClosed())
                                    <button type="button" wire:click="deleteBreak({{ $break->id }})" wire:confirm="حذف هذا البريك؟" class="text-xs text-danger hover:underline shrink-0">حذف</button>
                                @endunless
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Day completion --}}
            <div class="mt-5">
                <div class="flex justify-between text-xs text-ink-soft dark:text-ink-dark-soft mb-1">
                    <span>إنجاز اليوم</span>
                    <span>{{ $completion }}%</span>
                </div>
                <div class="h-2 rounded-full bg-ink-soft/15 dark:bg-ink-dark-soft/15 overflow-hidden">
                    <div class="h-full rounded-full bg-success transition-all" style="width: {{ $completion }}%"></div>
                </div>
            </div>

            @if ($day->isClosed() && $day->reflection)
                <div class="mt-4 rounded-lg bg-bg-light dark:bg-bg-dark p-3">
                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft mb-1">انعكاس اليوم</p>
                    <p class="text-sm text-ink dark:text-ink-dark whitespace-pre-line">{{ $day->reflection }}</p>
                </div>
            @endif
        </div>

        {{-- Tasks --}}
        <div class="mt-6 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            <div class="flex items-center justify-between mb-1">
                <h3 class="font-semibold text-ink dark:text-ink-dark">تاسكات اليوم</h3>
                <button type="button" wire:click="addTask" class="text-sm px-3 py-1.5 rounded-lg bg-primary dark:bg-primary-dark text-white hover:opacity-90 transition">
                    + تاسك
                </button>
            </div>
            @if ($planStartLabel || $plannedLabel)
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mb-4">
                    @if ($planStartLabel && $planEndLabel)<span dir="ltr" class="inline-block">🕒 {{ $planStartLabel }} → {{ $planEndLabel }}</span>@endif
                    @if ($plannedLabel)<span class="mx-1">·</span> إجمالي متوقع: {{ $plannedLabel }}@endif
                </p>
            @endif

            @forelse ($tasks as $task)
                <div wire:key="task-{{ $task->id }}" class="py-3 border-b border-ink-soft/10 dark:border-ink-dark-soft/10 last:border-0"
                     x-data="{ p: {{ $task->progress }} }">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm text-ink dark:text-ink-dark flex items-center gap-2">
                                <span>{{ $task->kind->emoji() }}</span>
                                <span>{{ $task->title }}</span>
                            </p>
                            <div class="flex items-center gap-2 mt-1 text-xs text-ink-soft dark:text-ink-dark-soft flex-wrap">
                                <span class="px-2 py-0.5 rounded-full bg-{{ $task->status->color() }}/15 text-{{ $task->status->color() }}">{{ $task->status->label() }}</span>
                                @if ($task->start_time)
                                    <span class="px-2 py-0.5 rounded-full bg-primary/10 text-primary dark:text-primary-dark" dir="ltr">🕒 {{ $task->startLabel() }}@if ($task->endLabel()) – {{ $task->endLabel() }}@endif</span>
                                    @if ($task->durationLabel())<span>({{ $task->durationLabel() }})</span>@endif
                                @endif
                                @if ($task->goal)
                                    <a href="{{ route('goals.show', $task->goal) }}" wire:navigate class="hover:underline">{{ $task->kind->emoji() }} {{ $task->goal->title }}</a>
                                @endif
                                @if ($task->carry_count > 0)<span>↩ مُرحّل {{ $task->carry_count }} مرة</span>@endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" wire:click="$dispatch('edit-task', { task: {{ $task->id }} })" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                            <button type="button" wire:click="$dispatch('delete-task', { task: {{ $task->id }} })" wire:confirm="حذف هذا التاسك؟" class="text-xs text-danger hover:underline">حذف</button>
                        </div>
                    </div>

                    {{-- Progress slider --}}
                    <div class="flex items-center gap-3 mt-2" dir="ltr">
                        <input type="range" min="0" max="100" step="5"
                               value="{{ $task->progress }}"
                               x-model.number="p"
                               x-on:change="$wire.setTaskProgress({{ $task->id }}, p)"
                               class="flex-1 accent-primary" />
                        <span class="text-xs text-ink-soft dark:text-ink-dark-soft w-10 text-left" x-text="p + '%'"></span>
                    </div>
                </div>
            @empty
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft text-center py-8">لا توجد تاسكات لهذا اليوم. أضف أول تاسك.</p>
            @endforelse
        </div>
    </div>

    {{-- Modals (manage-task modal lives globally in the app layout) --}}
    <livewire:planner.close-day />
</div>
