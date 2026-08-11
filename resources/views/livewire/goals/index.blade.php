<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Page header --}}
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">الأهداف</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">
                    كل شيء في حياتك منظّم تحت الأهداف.
                </p>
            </div>
            <button
                type="button"
                wire:click="$dispatch('create-goal')"
                class="inline-flex items-center gap-2 rounded-lg bg-primary dark:bg-primary-dark px-4 py-2 text-white text-sm font-medium shadow-sm hover:opacity-90 transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                هدف جديد
            </button>
        </div>

        {{-- Status filter --}}
        <div class="flex flex-wrap items-center gap-2 mb-6">
            <button
                type="button"
                wire:click="$set('statusFilter', '')"
                @class([
                    'px-3 py-1.5 text-sm rounded-full border transition',
                    'bg-primary text-white border-primary dark:bg-primary-dark dark:border-primary-dark' => $statusFilter === '',
                    'border-transparent bg-surface-light dark:bg-surface-dark text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark' => $statusFilter !== '',
                ])
            >
                الكل
            </button>
            @foreach ($statuses as $status)
                <button
                    type="button"
                    wire:click="$set('statusFilter', '{{ $status->value }}')"
                    @class([
                        'px-3 py-1.5 text-sm rounded-full border transition',
                        'bg-primary text-white border-primary dark:bg-primary-dark dark:border-primary-dark' => $statusFilter === $status->value,
                        'border-transparent bg-surface-light dark:bg-surface-dark text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark' => $statusFilter !== $status->value,
                    ])
                >
                    {{ $status->label() }}
                </button>
            @endforeach
        </div>

        {{-- Goals grid --}}
        @if ($goals->isEmpty())
            <div class="text-center py-20 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-ink-soft dark:text-ink-dark-soft">لا توجد أهداف بعد. ابدأ بإضافة هدفك الأول.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($goals as $goal)
                    <div
                        wire:key="goal-{{ $goal->id }}"
                        class="group relative rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md transition p-5 border-t-4"
                        style="border-top-color: {{ $goal->color }}"
                    >
                        <a href="{{ route('goals.show', $goal) }}" wire:navigate class="block">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="font-semibold text-ink dark:text-ink-dark line-clamp-2">{{ $goal->title }}</h3>
                                <span class="shrink-0 text-xs px-2 py-0.5 rounded-full bg-{{ $goal->status->color() }}/15 text-{{ $goal->status->color() }}">
                                    {{ $goal->status->label() }}
                                </span>
                            </div>
                            <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">{{ $goal->category->label() }}</p>
                            @if ($goal->description)
                                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-3 line-clamp-2">{{ $goal->description }}</p>
                            @endif
                            <div class="flex flex-wrap items-center gap-2 mt-3">
                                @if ($goal->target_date)
                                    @php($remaining = $goal->remainingDays())
                                    <span @class([
                                        'text-xs px-2 py-0.5 rounded-full',
                                        'bg-danger/15 text-danger' => $goal->isOverdue(),
                                        'bg-primary/10 text-primary dark:text-primary-dark' => ! $goal->isOverdue(),
                                    ])>
                                        @if ($remaining > 0)
                                            باقٍ {{ $remaining }} يوم
                                        @elseif ($remaining === 0)
                                            ينتهي اليوم
                                        @else
                                            متأخر {{ abs($remaining) }} يوم
                                        @endif
                                    </span>
                                @endif
                                @if ($goal->children_count > 0)
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-secondary/25 text-ink dark:text-ink-dark">
                                        🎯 {{ $goal->children_count }} هدف فرعي
                                    </span>
                                @endif
                            </div>
                        </a>

                        <div class="mt-4 flex items-center gap-3 opacity-0 group-hover:opacity-100 transition">
                            <button type="button" wire:click="$dispatch('edit-goal', { goal: {{ $goal->id }} })" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                            <button type="button" wire:click="delete({{ $goal->id }})" wire:confirm="حذف هذا الهدف؟ لا يمكن التراجع." class="text-xs text-danger hover:underline">حذف</button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $goals->links() }}
            </div>
        @endif
    </div>

    {{-- Create / edit modal lives here so the page owns its lifecycle --}}
    <livewire:goals.manage-goal />
</div>
