<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <a href="{{ route('recovery.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← كل حالات التعافي</a>

        {{-- Header --}}
        <div class="mt-3 flex items-start justify-between gap-4 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">{{ $recovery->title }}</h1>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $recovery->status->color() }}/15 text-{{ $recovery->status->color() }}">{{ $recovery->status->label() }}</span>
                </div>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-2">
                    🗓️ الفترة: من {{ $recovery->start_date->translatedFormat('j M Y') }}
                    @if ($recovery->end_date) إلى {{ $recovery->end_date->translatedFormat('j M Y') }} @else (مفتوحة) @endif
                </p>
                @if ($recovery->description)
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1 whitespace-pre-line">{{ $recovery->description }}</p>
                @endif
            </div>
            <button type="button" wire:click="editRecovery" class="shrink-0 px-3 py-1.5 rounded-lg border border-primary/40 text-primary dark:text-primary-dark text-sm hover:bg-primary/10 transition">تعديل</button>
        </div>

        {{-- Streak counter --}}
        <div class="mt-6 rounded-2xl bg-gradient-to-br from-success/15 to-success/5 dark:from-success/20 dark:to-transparent shadow-sm p-8 text-center">
            <p class="text-6xl font-bold text-success">{{ $streakDays }}</p>
            <p class="text-ink-soft dark:text-ink-dark-soft mt-2">يوم نظيف متواصل — منذ {{ $streakSince->translatedFormat('j M Y') }}</p>

            <div class="flex items-center justify-center gap-8 mt-6 text-sm">
                <div>
                    <p class="text-xl font-bold text-ink dark:text-ink-dark">{{ $bestStreak }}</p>
                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft">أطول فترة</p>
                </div>
                <div>
                    <p class="text-xl font-bold text-ink dark:text-ink-dark">{{ $setbackCount }}</p>
                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft">عدد الانتكاسات</p>
                </div>
            </div>

            <div class="flex items-center justify-center gap-3 mt-6">
                <button type="button" wire:click="addLog(false)" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">سجّل اليوم</button>
                <button type="button" wire:click="addLog(true)" class="px-4 py-2 rounded-lg bg-danger/15 text-danger text-sm font-medium hover:bg-danger/25 transition">سجّل انتكاسة</button>
            </div>
        </div>

        {{-- Logs --}}
        <div class="mt-6 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            <h3 class="font-semibold text-ink dark:text-ink-dark mb-4">السجل</h3>
            @forelse ($logs as $log)
                <div wire:key="reclog-{{ $log->id }}" class="flex items-start justify-between gap-4 py-3 border-b border-ink-soft/10 dark:border-ink-dark-soft/10 last:border-0">
                    <div class="min-w-0">
                        <p class="text-sm text-ink dark:text-ink-dark flex items-center gap-2">
                            {{ $log->date->translatedFormat('l، j M Y') }}
                            @if ($log->is_setback)<span class="text-xs px-2 py-0.5 rounded-full bg-danger/15 text-danger">انتكاسة</span>@endif
                        </p>
                        <div class="flex items-center gap-3 mt-1 text-xs text-ink-soft dark:text-ink-dark-soft flex-wrap">
                            @if ($log->urge_level)<span>🌊 الرغبة: {{ $log->urge_level }}/10</span>@endif
                            @if ($log->mood)<span>😊 المزاج: {{ $log->mood }}/10</span>@endif
                            @if ($log->hardest_from && $log->hardest_to)<span dir="ltr">⏰ {{ substr($log->hardest_from, 0, 5) }}–{{ substr($log->hardest_to, 0, 5) }}</span>@endif
                        </div>
                        @if ($log->trigger_note)<p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">المُحفّز: {{ $log->trigger_note }}</p>@endif
                        @if ($log->note)<p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">{{ $log->note }}</p>@endif
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button" wire:click="$dispatch('edit-recovery-log', { log: {{ $log->id }} })" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                        <button type="button" wire:click="$dispatch('delete-recovery-log', { log: {{ $log->id }} })" wire:confirm="حذف هذا السجل؟" class="text-xs text-danger hover:underline">حذف</button>
                    </div>
                </div>
            @empty
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft text-center py-8">لا توجد سجلات بعد.</p>
            @endforelse
        </div>
    </div>

    <livewire:recovery.manage-recovery />
    <livewire:recovery.manage-log />
</div>
