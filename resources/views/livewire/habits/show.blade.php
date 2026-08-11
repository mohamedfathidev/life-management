<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <a href="{{ route('habits.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← كل العادات</a>

        {{-- Header --}}
        <div class="mt-3 flex items-start justify-between gap-4 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 border-e-4" style="border-inline-end-color: {{ $habit->color }}">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">{{ $habit->title }}</h1>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-secondary/25 text-ink dark:text-ink-dark">{{ $habit->type->label() }}</span>
                </div>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-2">
                    🚩 من {{ $habit->start_date->translatedFormat('j M Y') }}
                    @if ($habit->isIntermittent() && $habit->end_date)
                        إلى {{ $habit->end_date->translatedFormat('j M Y') }} ({{ $periodDays }} يوم)
                    @else
                        (مستمرة)
                    @endif
                </p>
                @if ($habit->goal)
                    <a href="{{ route('goals.show', $habit->goal) }}" wire:navigate class="text-xs text-primary dark:text-primary-dark hover:underline">🎯 {{ $habit->goal->title }}</a>
                @endif
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" wire:click="editHabit" class="px-3 py-1.5 rounded-lg border border-primary/40 text-primary dark:text-primary-dark text-sm hover:bg-primary/10 transition">تعديل</button>
                <button type="button" wire:click="delete" wire:confirm="حذف هذه العادة وكل سجلاتها؟" class="px-3 py-1.5 rounded-lg border border-danger/40 text-danger text-sm hover:bg-danger/10 transition">حذف</button>
            </div>
        </div>

        {{-- Stat cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6">
            <div class="rounded-2xl p-5 text-center shadow-sm bg-gradient-to-br from-primary/15 to-primary/5 dark:from-primary-dark/20 dark:to-transparent">
                <p @class([
                    'text-3xl font-bold',
                    'text-success' => $adherence >= 70,
                    'text-warning' => $adherence >= 40 && $adherence < 70,
                    'text-danger' => $adherence < 40,
                ])>{{ $adherence }}%</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">نسبة الالتزام</p>
            </div>
            <div class="rounded-2xl p-5 text-center shadow-sm bg-surface-light dark:bg-surface-dark">
                <p class="text-3xl font-bold text-success">{{ $doneCount }}</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">يوم ملتزم</p>
            </div>
            <div class="rounded-2xl p-5 text-center shadow-sm bg-surface-light dark:bg-surface-dark">
                <p class="text-3xl font-bold text-danger">{{ $missedCount }}</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">يوم فوّت</p>
            </div>
            <div class="rounded-2xl p-5 text-center shadow-sm bg-surface-light dark:bg-surface-dark">
                <p class="text-3xl font-bold text-ink dark:text-ink-dark">{{ $currentStreak }}</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">سلسلة حالية</p>
            </div>
        </div>

        <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-3 text-center">
            من أصل {{ $applicableDays }} يوم مطلوب حتى الآن · أطول سلسلة: {{ $bestStreak }} يوم
        </p>

        {{-- Day grid --}}
        <div class="mt-6 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            <h3 class="font-semibold text-ink dark:text-ink-dark mb-4">التقويم</h3>
            <div class="flex flex-wrap gap-1.5">
                @foreach ($cells as $cell)
                    @if ($cell['isFuture'])
                        <div class="w-8 h-8 rounded-md flex items-center justify-center text-[11px] bg-bg-light/50 dark:bg-bg-dark/50 text-ink-soft/30 dark:text-ink-dark-soft/30"
                             title="{{ $cell['date'] }}">{{ $cell['day'] }}</div>
                    @else
                        <button type="button" wire:click="toggle('{{ $cell['date'] }}')"
                            wire:key="cell-{{ $cell['date'] }}"
                            title="{{ $cell['date'] }}"
                            @class([
                                'w-8 h-8 rounded-md flex items-center justify-center text-[11px] transition',
                                'bg-success text-white' => $cell['done'],
                                'bg-bg-light dark:bg-bg-dark text-ink-soft dark:text-ink-dark-soft hover:bg-ink-soft/10' => ! $cell['done'],
                                'ring-2 ring-primary' => $cell['isToday'],
                            ])>{{ $cell['day'] }}</button>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <livewire:habits.manage-habit />
</div>
