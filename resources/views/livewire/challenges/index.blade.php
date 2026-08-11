<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">التحديات</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">تحديات بمدة محددة (زي «٣٠ يوم بدون سكر») — علّم كل يوم.</p>
            </div>
            <button type="button" wire:click="$dispatch('create-challenge')" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium shadow-sm hover:opacity-90 transition">+ تحدٍّ</button>
        </div>

        @if ($challenges->isEmpty())
            <div class="text-center py-20 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-ink-soft dark:text-ink-dark-soft">ابدأ أول تحدٍّ ليك.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($challenges as $ch)
                    @php($done = $ch->isDoneOn($today))
                    @php($progress = $ch->progressPercent())
                    <div wire:key="ch-{{ $ch->id }}" class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-5 border-e-4" style="border-inline-end-color: {{ $ch->color }}">
                        <div class="flex items-center gap-4">
                            @if ($ch->status->value === 'active')
                                <button type="button" wire:click="toggleToday({{ $ch->id }})" title="علّم النهاردة"
                                    @class([
                                        'w-11 h-11 rounded-full flex items-center justify-center text-lg shrink-0 transition',
                                        'bg-success text-white' => $done,
                                        'bg-bg-light dark:bg-bg-dark text-ink-soft/50 dark:text-ink-dark-soft/50 hover:bg-ink-soft/10' => ! $done,
                                    ])>{{ $done ? '✓' : '○' }}</button>
                            @else
                                <div class="w-11 h-11 rounded-full flex items-center justify-center text-lg shrink-0 bg-{{ $ch->status->color() }}/15 text-{{ $ch->status->color() }}">
                                    {{ $ch->status->value === 'completed' ? '🏆' : '—' }}
                                </div>
                            @endif

                            <a href="{{ route('challenges.show', $ch) }}" wire:navigate class="flex-1 min-w-0">
                                <p class="font-semibold text-ink dark:text-ink-dark flex items-center gap-2 flex-wrap">
                                    {{ $ch->title }}
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $ch->status->color() }}/15 text-{{ $ch->status->color() }}">{{ $ch->status->label() }}</span>
                                </p>
                                <div class="flex items-center gap-3 mt-1 text-xs text-ink-soft dark:text-ink-dark-soft">
                                    <span>🔥 {{ $ch->currentStreak() }} يوم</span>
                                    <span>· {{ $ch->doneCount() }}/{{ $ch->duration_days }}</span>
                                    @if ($ch->status->value === 'active')<span>· باقٍ {{ $ch->daysRemaining() }} يوم</span>@endif
                                </div>
                                <div class="mt-2 h-1.5 rounded-full bg-ink-soft/15 dark:bg-ink-dark-soft/15 overflow-hidden">
                                    <div class="h-full rounded-full bg-success" style="width: {{ $progress }}%"></div>
                                </div>
                            </a>

                            <span class="text-sm font-bold text-success shrink-0">{{ $progress }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <livewire:challenges.manage-challenge />
</div>
