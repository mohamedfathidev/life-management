<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <a href="{{ route('religion') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← الدين</a>
        <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mt-1 mb-1">الصلوات</h1>
        <p class="text-sm text-ink-soft dark:text-ink-dark-soft mb-6">{{ now()->translatedFormat('l، j F Y') }}</p>

        {{-- Today's 5 prayers --}}
        <div class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 space-y-3">
            @foreach ($prayers as $prayer)
                @php($current = $today->{$prayer})
                <div wire:key="prayer-{{ $prayer }}" class="flex items-center justify-between gap-3 flex-wrap">
                    <span class="font-medium text-ink dark:text-ink-dark">{{ $labels[$prayer] }}</span>
                    <div class="flex items-center gap-1.5 flex-wrap justify-end">
                        @foreach ($states as $state)
                            <button type="button" wire:click="setPrayer('{{ $prayer }}', '{{ $state->value }}')"
                                @class([
                                    'px-2.5 py-1.5 rounded-lg text-xs transition',
                                    'bg-primary text-white dark:bg-primary-dark' => $current === $state && $state->value === 'jamaah',
                                    'bg-success text-white' => $current === $state && $state->value === 'ontime',
                                    'bg-warning text-white' => $current === $state && $state->value === 'prayed',
                                    'bg-ink-soft/20 text-ink dark:text-ink-dark' => $current === $state && $state->value === 'none',
                                    'bg-bg-light dark:bg-bg-dark text-ink-soft dark:text-ink-dark-soft hover:bg-ink-soft/10' => $current !== $state,
                                ])>
                                {{ $state->label() }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="pt-3 border-t border-ink-soft/10 dark:border-ink-dark-soft/10 text-center">
                <span class="text-sm text-ink-soft dark:text-ink-dark-soft">اليوم: {{ $today->doneCount() }}/5 · {{ $today->onTimeCount() }} في وقتها · {{ $today->jamaahCount() }} جماعة</span>
            </div>
        </div>

        {{-- Month stats --}}
        <div class="grid grid-cols-3 gap-4 mt-6">
            <div class="rounded-2xl bg-gradient-to-br from-success/15 to-success/5 dark:from-success/20 dark:to-transparent shadow-sm p-5 text-center">
                <p class="text-2xl font-bold text-success">{{ $completionPercent }}%</p>
                <p class="text-[11px] text-ink-soft dark:text-ink-dark-soft mt-1">التزام الشهر</p>
            </div>
            <div class="rounded-2xl bg-gradient-to-br from-warning/15 to-warning/5 dark:from-warning/20 dark:to-transparent shadow-sm p-5 text-center">
                <p class="text-2xl font-bold text-warning">{{ $onTimePercent }}%</p>
                <p class="text-[11px] text-ink-soft dark:text-ink-dark-soft mt-1">في وقتها</p>
            </div>
            <div class="rounded-2xl bg-gradient-to-br from-primary/15 to-primary/5 dark:from-primary-dark/20 dark:to-transparent shadow-sm p-5 text-center">
                <p class="text-2xl font-bold text-primary dark:text-primary-dark">{{ $jamaahPercent }}%</p>
                <p class="text-[11px] text-ink-soft dark:text-ink-dark-soft mt-1">جماعة</p>
            </div>
        </div>
    </div>
</div>
