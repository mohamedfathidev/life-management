<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <a href="{{ route('market-study.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← مذاكرة السوق</a>

        {{-- Header --}}
        <div class="mt-3 flex items-start justify-between gap-4 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">{{ $track->title }}</h1>
                    @if ($track->is_completed)<span class="text-xs px-2 py-0.5 rounded-full bg-success/15 text-success">مكتمل</span>@endif
                </div>
                @if ($track->field)<p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">{{ $track->field }}</p>@endif
                @if ($track->start_date || $track->end_date)
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-2">
                        🗓️
                        @if ($track->start_date) من {{ $track->start_date->translatedFormat('j M Y') }} @endif
                        @if ($track->end_date) إلى {{ $track->end_date->translatedFormat('j M Y') }} @endif
                    </p>
                @endif
                @if ($track->certificate)<p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">🏅 {{ $track->certificate }}</p>@endif
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" wire:click="toggleCompleted" class="px-3 py-1.5 rounded-lg border text-sm transition {{ $track->is_completed ? 'border-ink-soft/30 text-ink-soft dark:text-ink-dark-soft' : 'border-success/40 text-success hover:bg-success/10' }}">
                    {{ $track->is_completed ? 'إلغاء الإكمال' : '✓ تم' }}
                </button>
                <button type="button" wire:click="editTrack" class="px-3 py-1.5 rounded-lg border border-primary/40 text-primary dark:text-primary-dark text-sm hover:bg-primary/10 transition">تعديل</button>
                <button type="button" wire:click="delete" wire:confirm="حذف هذا المسار؟" class="px-3 py-1.5 rounded-lg border border-danger/40 text-danger text-sm hover:bg-danger/10 transition">حذف</button>
            </div>
        </div>

        {{-- Plan / resources / target --}}
        <div class="mt-6 space-y-4">
            @if ($track->target)
                <div class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                    <h3 class="font-semibold text-ink dark:text-ink-dark mb-2">🎯 التارجت</h3>
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft whitespace-pre-line">{{ $track->target }}</p>
                </div>
            @endif
            @if ($track->plan)
                <div class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                    <h3 class="font-semibold text-ink dark:text-ink-dark mb-2">الخطة — بذاكر إيه</h3>
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft whitespace-pre-line">{{ $track->plan }}</p>
                </div>
            @endif
            @if ($track->resources)
                <div class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                    <h3 class="font-semibold text-ink dark:text-ink-dark mb-2">المصادر</h3>
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft whitespace-pre-line">{{ $track->resources }}</p>
                </div>
            @endif
            @if (! $track->plan && ! $track->resources && ! $track->target)
                <div class="text-center py-10 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                    <p class="text-ink-soft dark:text-ink-dark-soft">لسه مفيش تفاصيل — اضغط «تعديل» وحط خطتك ومصادرك وتارجتك.</p>
                </div>
            @endif

            {{-- Study tasks (kind = مذاكرة) --}}
            <div class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-ink dark:text-ink-dark">📚 تاسكات المذاكرة</h3>
                    <button type="button" wire:click="addStudyTask" class="text-sm px-3 py-1.5 rounded-lg bg-primary dark:bg-primary-dark text-white hover:opacity-90 transition">+ تاسك</button>
                </div>
                @forelse ($tasks as $task)
                    <div wire:key="strack-task-{{ $task->id }}" class="flex items-center justify-between gap-3 py-2.5 border-b border-ink-soft/10 dark:border-ink-dark-soft/10 last:border-0">
                        <div class="min-w-0">
                            <p class="text-sm text-ink dark:text-ink-dark">{{ $task->title }}</p>
                            <div class="flex items-center gap-2 mt-0.5 text-xs text-ink-soft dark:text-ink-dark-soft flex-wrap">
                                <span class="px-2 py-0.5 rounded-full bg-{{ $task->status->color() }}/15 text-{{ $task->status->color() }}">{{ $task->status->label() }} · {{ $task->progress }}%</span>
                                @if ($task->day)
                                    <a href="{{ route('planner.day', $task->day->date->toDateString()) }}" wire:navigate class="hover:underline">🗓️ {{ $task->day->date->translatedFormat('j M') }}</a>
                                @else
                                    <span>في المؤجّلات</span>
                                @endif
                            </div>
                        </div>
                        <button type="button" wire:click="$dispatch('edit-task', { task: {{ $task->id }} })" class="text-xs text-primary dark:text-primary-dark hover:underline shrink-0">تعديل</button>
                    </div>
                @empty
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft text-center py-6">مفيش تاسكات مذاكرة لسه. أضف أول تاسك.</p>
                @endforelse
            </div>
        </div>
    </div>

    <livewire:market-study.manage-track />
</div>
