<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">العادات</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">علّم عادة النهاردة، وادخل على أي عادة تشوف تفاصيل التزامك.</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <x-add-to-today kind="habit" label="تاسك عادة النهاردة" />
                <button type="button" wire:click="$dispatch('create-habit')" class="inline-flex items-center gap-2 rounded-lg bg-primary dark:bg-primary-dark px-4 py-2 text-white text-sm font-medium shadow-sm hover:opacity-90 transition">
                    + عادة جديدة
                </button>
            </div>
        </div>

        @if ($habits->isEmpty())
            <div class="text-center py-20 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-ink-soft dark:text-ink-dark-soft">ابدأ بإضافة أول عادة (رياضة، ماء، قراءة…).</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($habits as $habit)
                    @php($done = $habit->isDoneOn($today))
                    @php($adherence = $habit->adherencePercent())
                    <div wire:key="habit-{{ $habit->id }}" class="flex items-center gap-4 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-4 border-e-4" style="border-inline-end-color: {{ $habit->color }}">
                        {{-- Today toggle --}}
                        <button type="button" wire:click="toggle({{ $habit->id }}, '{{ $today }}')"
                            title="علّم النهاردة"
                            @class([
                                'w-11 h-11 rounded-full flex items-center justify-center text-lg shrink-0 transition',
                                'bg-success text-white' => $done,
                                'bg-bg-light dark:bg-bg-dark text-ink-soft/50 dark:text-ink-dark-soft/50 hover:bg-ink-soft/10' => ! $done,
                            ])>
                            {{ $done ? '✓' : '○' }}
                        </button>

                        {{-- Info → detail --}}
                        <a href="{{ route('habits.show', $habit) }}" wire:navigate class="flex-1 min-w-0">
                            <p class="font-semibold text-ink dark:text-ink-dark flex items-center gap-2">
                                {{ $habit->title }}
                                <span class="text-xs px-2 py-0.5 rounded-full bg-secondary/25 text-ink dark:text-ink-dark">{{ $habit->type->label() }}</span>
                            </p>
                            <div class="flex items-center gap-3 mt-1 text-xs text-ink-soft dark:text-ink-dark-soft">
                                <span>🔥 {{ $habit->currentStreak() }} يوم</span>
                                <span>· فوّت {{ $habit->missedCount() }}</span>
                            </div>
                        </a>

                        {{-- Adherence --}}
                        <a href="{{ route('habits.show', $habit) }}" wire:navigate class="text-center shrink-0">
                            <p @class([
                                'text-xl font-bold',
                                'text-success' => $adherence >= 70,
                                'text-warning' => $adherence >= 40 && $adherence < 70,
                                'text-danger' => $adherence < 40,
                            ])>{{ $adherence }}%</p>
                            <p class="text-[10px] text-ink-soft dark:text-ink-dark-soft">التزام</p>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <livewire:habits.manage-habit />
</div>
