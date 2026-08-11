<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <a href="{{ route('challenges.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← كل التحديات</a>

        {{-- Header --}}
        <div class="mt-3 flex items-start justify-between gap-4 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 border-e-4" style="border-inline-end-color: {{ $challenge->color }}">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">{{ $challenge->title }}</h1>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $challenge->status->color() }}/15 text-{{ $challenge->status->color() }}">{{ $challenge->status->label() }}</span>
                </div>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-2">
                    🚩 {{ $challenge->start_date->translatedFormat('j M Y') }} → {{ $endDate->translatedFormat('j M Y') }} · {{ $challenge->duration_days }} يوم
                </p>
                @if ($challenge->description)<p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1 whitespace-pre-line">{{ $challenge->description }}</p>@endif
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" wire:click="editChallenge" class="px-3 py-1.5 rounded-lg border border-primary/40 text-primary dark:text-primary-dark text-sm hover:bg-primary/10 transition">تعديل</button>
                <button type="button" wire:click="delete" wire:confirm="حذف التحدي؟" class="px-3 py-1.5 rounded-lg border border-danger/40 text-danger text-sm hover:bg-danger/10 transition">حذف</button>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6">
            <div class="rounded-2xl p-5 text-center shadow-sm bg-gradient-to-br from-success/15 to-success/5 dark:from-success/20 dark:to-transparent">
                <p class="text-3xl font-bold text-success">{{ $challenge->progressPercent() }}%</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">التقدّم</p>
            </div>
            <div class="rounded-2xl p-5 text-center shadow-sm bg-surface-light dark:bg-surface-dark">
                <p class="text-3xl font-bold text-ink dark:text-ink-dark">{{ $challenge->doneCount() }}</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">يوم مُنجز</p>
            </div>
            <div class="rounded-2xl p-5 text-center shadow-sm bg-surface-light dark:bg-surface-dark">
                <p class="text-3xl font-bold text-primary dark:text-primary-dark">{{ $challenge->currentStreak() }}</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">سلسلة حالية</p>
            </div>
            <div class="rounded-2xl p-5 text-center shadow-sm bg-surface-light dark:bg-surface-dark">
                <p class="text-3xl font-bold text-warning">{{ $challenge->daysRemaining() }}</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">يوم باقٍ</p>
            </div>
        </div>

        {{-- Status actions --}}
        <div class="flex flex-wrap items-center gap-2 mt-6">
            @if ($challenge->status->value === 'active')
                <button type="button" wire:click="setStatus('completed')" class="px-4 py-2 rounded-lg bg-success text-white text-sm hover:opacity-90 transition">🏆 أكملته</button>
                <button type="button" wire:click="setStatus('abandoned')" class="px-4 py-2 rounded-lg bg-danger/15 text-danger text-sm hover:bg-danger/25 transition">تركته</button>
            @else
                <button type="button" wire:click="setStatus('active')" class="px-4 py-2 rounded-lg border border-ink-soft/20 text-ink dark:text-ink-dark text-sm hover:bg-ink-soft/5 transition">إعادة تفعيل</button>
            @endif
        </div>

        {{-- Day grid --}}
        <div class="mt-6 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            <h3 class="font-semibold text-ink dark:text-ink-dark mb-4">أيام التحدي</h3>
            <div class="flex flex-wrap gap-1.5">
                @foreach ($cells as $cell)
                    @if ($cell['isFuture'])
                        <div class="w-9 h-9 rounded-md flex items-center justify-center text-[11px] bg-bg-light/50 dark:bg-bg-dark/50 text-ink-soft/30 dark:text-ink-dark-soft/30" title="اليوم {{ $cell['n'] }}">{{ $cell['n'] }}</div>
                    @else
                        <button type="button" wire:click="toggle('{{ $cell['date'] }}')" wire:key="cd-{{ $cell['date'] }}"
                            title="اليوم {{ $cell['n'] }} — {{ $cell['date'] }}"
                            @class([
                                'w-9 h-9 rounded-md flex items-center justify-center text-[11px] transition',
                                'bg-success text-white' => $cell['done'],
                                'bg-bg-light dark:bg-bg-dark text-ink-soft dark:text-ink-dark-soft hover:bg-ink-soft/10' => ! $cell['done'],
                                'ring-2 ring-primary' => $cell['isToday'],
                            ])>{{ $cell['n'] }}</button>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <livewire:challenges.manage-challenge />
</div>
