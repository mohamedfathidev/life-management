<div class="py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">كل التاسكات</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">كل حاجة اتجمّعت من كل الأقسام في مكان واحد.</p>
            </div>
            <x-add-to-today kind="other" label="تاسك جديد النهاردة" class="!px-4 !py-2" />
        </div>

        {{-- Scope tabs --}}
        <div class="flex gap-2 text-sm">
            @foreach (['today' => 'النهاردة', 'pool' => 'المؤجلات', 'all' => 'الكل'] as $key => $label)
                <button wire:click="$set('scope', '{{ $key }}')"
                    class="px-4 py-2 rounded-lg font-medium transition {{ $scope === $key ? 'bg-primary dark:bg-primary-dark text-white' : 'bg-surface-light dark:bg-surface-dark text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark' }}">
                    {{ $label }}
                    <span class="text-xs opacity-80">({{ $counts[$key] }})</span>
                </button>
            @endforeach
        </div>

        {{-- Filters --}}
        <div class="flex flex-wrap items-end gap-3 bg-surface-light dark:bg-surface-dark rounded-xl p-4">
            <div class="flex-1 min-w-[140px]">
                <label class="block text-xs text-ink-soft dark:text-ink-dark-soft mb-1">النوع</label>
                <select wire:model.live="kind" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                    <option value="">كل الأنواع</option>
                    @foreach ($kinds as $k)
                        <option value="{{ $k->value }}">{{ $k->emoji() }} {{ $k->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[140px]">
                <label class="block text-xs text-ink-soft dark:text-ink-dark-soft mb-1">الحالة</label>
                <select wire:model.live="status" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                    <option value="">كل الحالات</option>
                    @foreach ($statuses as $s)
                        <option value="{{ $s->value }}">{{ $s->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs text-ink-soft dark:text-ink-dark-soft mb-1">الهدف</label>
                <select wire:model.live="goalId" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                    <option value="">كل الأهداف</option>
                    @foreach ($goals as $g)
                        <option value="{{ $g->id }}">{{ $g->parent ? $g->parent->title.' › ' : '' }}{{ $g->title }}</option>
                    @endforeach
                </select>
            </div>
            <button wire:click="resetFilters" class="px-3 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إعادة ضبط</button>
        </div>

        {{-- Today's cross-module agenda (only in the "today" tab) --}}
        @if ($scope === 'today' && count($agenda))
            <div class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-lg">📋</span>
                    <h2 class="text-sm font-semibold text-ink dark:text-ink-dark">أجندة النهاردة من الأقسام</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    @foreach ($agenda as $group)
                        <div>
                            <p class="text-xs font-medium text-ink-soft dark:text-ink-dark-soft mb-2">{{ $group['title'] }}</p>
                            <div class="space-y-1">
                                @foreach ($group['items'] as $item)
                                    <a href="{{ $item['url'] }}" wire:navigate class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        @if ($item['done'] === true)
                                            <span class="shrink-0 w-5 h-5 rounded-full bg-success text-white flex items-center justify-center text-[11px]">✓</span>
                                        @elseif ($item['done'] === false)
                                            <span class="shrink-0 w-5 h-5 rounded-full border-2 border-warning/60 flex items-center justify-center"></span>
                                        @else
                                            <span class="shrink-0 text-base leading-none">{{ $item['emoji'] }}</span>
                                        @endif
                                        <span class="text-sm text-ink dark:text-ink-dark truncate {{ $item['done'] === true ? 'line-through opacity-60' : '' }}">
                                            @if ($item['done'] !== null){{ $item['emoji'] }} @endif{{ $item['label'] }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <p class="text-[11px] text-ink-soft dark:text-ink-dark-soft mt-3">دي حاجات النهاردة من أقسامها — علّمها من مكانها. تحت التاسكات الفعلية بس.</p>
            </div>
        @endif

        {{-- Task list --}}
        <div class="space-y-2">
            @forelse ($tasks as $task)
                @php($sc = $task->status->color())
                <div wire:key="task-{{ $task->id }}"
                    class="flex items-center gap-3 bg-surface-light dark:bg-surface-dark rounded-xl px-4 py-3 shadow-sm {{ $task->is_important ? 'ring-2 ring-warning/50 bg-warning/5' : '' }}">
                    <button type="button" wire:click="toggleImportant({{ $task->id }})" title="أهم تاسك" class="shrink-0 text-lg {{ $task->is_important ? 'text-warning' : 'text-ink-soft/40 dark:text-ink-dark-soft/40 hover:text-warning' }}">{{ $task->is_important ? '⭐' : '☆' }}</button>

                    {{-- Done toggle --}}
                    <button wire:click="toggleDone({{ $task->id }})" title="تعليم كمكتملة"
                        class="shrink-0 w-6 h-6 rounded-full border-2 flex items-center justify-center transition
                            {{ $task->isDone() ? 'bg-success border-success text-white' : 'border-gray-300 dark:border-gray-600 text-transparent hover:border-success' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </button>

                    {{-- Kind badge --}}
                    <span class="shrink-0 text-lg" title="{{ $task->kind->label() }}">{{ $task->kind->emoji() }}</span>

                    {{-- Body --}}
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('tasks.show', $task) }}" wire:navigate class="block text-sm font-medium text-ink dark:text-ink-dark truncate hover:text-primary dark:hover:text-primary-dark transition {{ $task->isDone() ? 'line-through opacity-60' : '' }}">{{ $task->title }}</a>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-xs text-ink-soft dark:text-ink-dark-soft">
                            @if ($task->start_time)
                                <span class="text-primary dark:text-primary-dark" dir="ltr">🕒 {{ $task->startLabel() }}@if ($task->endLabel()) – {{ $task->endLabel() }}@endif</span>
                                @if ($task->durationLabel())<span>({{ $task->durationLabel() }})</span>@endif
                            @endif
                            @if ($task->goal)
                                <span>🎯 {{ $task->goal->parent ? $task->goal->parent->title.' › ' : '' }}{{ $task->goal->title }}</span>
                            @endif
                            @if ($task->day)
                                <span>📅 {{ $task->day->date->translatedFormat('l j M') }}</span>
                            @else
                                <span>🗂️ المؤجلات</span>
                            @endif
                            @if ($task->carry_count > 0)
                                <span class="text-warning">↻ اترحّلت {{ $task->carry_count }} مرة</span>
                            @endif
                        </div>
                    </div>

                    {{-- Progress slider (set how much you finished) --}}
                    <div class="hidden sm:flex items-center gap-2 w-36 shrink-0" dir="ltr" x-data="{ p: {{ $task->progress }} }">
                        <input type="range" min="0" max="100" step="5" value="{{ $task->progress }}"
                               x-model.number="p" x-on:change="$wire.setProgress({{ $task->id }}, p)"
                               class="flex-1 accent-primary" title="حدّد قد إيه خلصت" />
                        <span class="text-xs font-medium text-{{ $sc }} w-9 text-left" x-text="p + '%'"></span>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-1 shrink-0">
                        <button wire:click="$dispatch('edit-task', { task: {{ $task->id }} })" title="تعديل"
                            class="p-1.5 rounded-lg text-ink-soft dark:text-ink-dark-soft hover:text-primary dark:hover:text-primary-dark hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button wire:click="$dispatch('delete-task', { task: {{ $task->id }} })" wire:confirm="متأكد تمسح التاسك دي؟" title="حذف"
                            class="p-1.5 rounded-lg text-ink-soft dark:text-ink-dark-soft hover:text-danger hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-16 bg-surface-light dark:bg-surface-dark rounded-xl">
                    <p class="text-4xl mb-3">🗂️</p>
                    <p class="text-ink-soft dark:text-ink-dark-soft">مفيش تاسكات بالفلاتر دي.</p>
                    <div class="mt-4 flex justify-center">
                        <x-add-to-today kind="other" label="أضف أول تاسك النهاردة" class="!px-4 !py-2" />
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
