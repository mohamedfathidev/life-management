<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <a href="{{ route('planner') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← المخطط الأسبوعي</a>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mt-1">المؤجّلات</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">تاسكات غير مكتملة ومش متعيّنة ليوم. رجّعها لأي يوم لما تكون جاهز.</p>
            </div>
        </div>

        {{-- Shared target date for reassignment --}}
        <div class="flex flex-wrap items-end gap-3 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-4 mb-5">
            <div>
                <label for="assign_date" class="text-xs text-ink-soft dark:text-ink-dark-soft">نقل إلى يوم</label>
                <input id="assign_date" type="date" wire:model="assignDate" class="mt-1 block rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" dir="ltr" />
            </div>
            <p class="text-xs text-ink-soft dark:text-ink-dark-soft pb-2">اختر التاريخ ثم اضغط «نقل» بجانب أي تاسك.</p>
            @error('assignDate') <span class="text-xs text-danger w-full">{{ $message }}</span> @enderror
        </div>

        <div class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            @forelse ($tasks as $task)
                <div wire:key="pool-task-{{ $task->id }}" class="flex items-center justify-between gap-3 py-3 border-b border-ink-soft/10 dark:border-ink-dark-soft/10 last:border-0">
                    <div class="min-w-0">
                        <p class="text-sm text-ink dark:text-ink-dark flex items-center gap-2">
                            <span>{{ $task->kind->emoji() }}</span>
                            <span class="truncate">{{ $task->title }}</span>
                        </p>
                        <div class="flex items-center gap-2 mt-1 text-xs text-ink-soft dark:text-ink-dark-soft flex-wrap">
                            <span class="px-2 py-0.5 rounded-full bg-{{ $task->status->color() }}/15 text-{{ $task->status->color() }}">{{ $task->status->label() }} · {{ $task->progress }}%</span>
                            @if ($task->goal)
                                <a href="{{ route('goals.show', $task->goal) }}" wire:navigate class="hover:underline">🎯 {{ $task->goal->title }}</a>
                            @endif
                            @if ($task->carry_count > 0)<span>↩ مُرحّل {{ $task->carry_count }} مرة</span>@endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button" wire:click="assign({{ $task->id }})" class="text-xs px-3 py-1.5 rounded-lg bg-primary dark:bg-primary-dark text-white hover:opacity-90 transition">نقل</button>
                        <button type="button" wire:click="$dispatch('edit-task', { task: {{ $task->id }} })" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                        <button type="button" wire:click="$dispatch('delete-task', { task: {{ $task->id }} })" wire:confirm="حذف هذا التاسك؟" class="text-xs text-danger hover:underline">حذف</button>
                    </div>
                </div>
            @empty
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft text-center py-10">لا توجد تاسكات مؤجّلة. 🎉</p>
            @endforelse
        </div>
    </div>

    {{-- Reuse the task modal for editing/deleting --}}
    <livewire:planner.manage-task />
</div>
