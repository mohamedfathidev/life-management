<div
    x-data="{ open: @entangle('open') }"
    x-show="open"
    x-cloak
    @keydown.escape.window="open && $wire.close()"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/40" wire:click="close"></div>

    <div x-show="open" x-transition class="relative w-full max-w-lg rounded-2xl bg-surface-light dark:bg-surface-dark shadow-xl p-6 max-h-[90vh] overflow-y-auto">
        <h2 class="text-lg font-semibold text-ink dark:text-ink-dark mb-1">تقفيل اليوم</h2>
        <p class="text-sm text-ink-soft dark:text-ink-dark-soft mb-4">قيّم يومك، وقرر مصير التاسكات غير المكتملة.</p>

        <form wire:submit="save" class="space-y-5">
            {{-- Rating 1-10 --}}
            <div>
                <x-input-label value="تقييم اليوم (١–١٠)" />
                <div class="flex flex-wrap gap-1.5 mt-2" dir="ltr">
                    @for ($i = 1; $i <= 10; $i++)
                        <button type="button" wire:click="$set('rating', {{ $i }})"
                            @class([
                                'w-9 h-9 rounded-lg text-sm font-medium transition',
                                'bg-primary text-white dark:bg-primary-dark' => $rating === $i,
                                'bg-bg-light dark:bg-bg-dark text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark' => $rating !== $i,
                            ])>
                            {{ $i }}
                        </button>
                    @endfor
                </div>
                <x-input-error :messages="$errors->get('rating')" class="mt-1" />
            </div>

            {{-- Reflection --}}
            <div>
                <x-input-label for="reflection" value="انعكاس اليوم (اختياري)" />
                <textarea id="reflection" wire:model="reflection" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="إيه اللي مشى كويس؟ وإيه اللي محتاج تحسّنه بكرة؟"></textarea>
                <x-input-error :messages="$errors->get('reflection')" class="mt-1" />
            </div>

            {{-- Incomplete tasks handling --}}
            @if ($incompleteTasks->isNotEmpty())
                <div>
                    <x-input-label value="التاسكات غير المكتملة" />
                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1 mb-2">اختر لكل تاسك: ترحيله لبكرة (بنفس النسبة) أو تركه في قائمة المؤجّلات.</p>
                    <div class="space-y-2">
                        @foreach ($incompleteTasks as $task)
                            <div wire:key="close-task-{{ $task->id }}" class="flex items-center justify-between gap-3 rounded-lg bg-bg-light dark:bg-bg-dark p-3">
                                <span class="text-sm text-ink dark:text-ink-dark min-w-0 truncate">{{ $task->kind->emoji() }} {{ $task->title }} <span class="text-ink-soft dark:text-ink-dark-soft">({{ $task->progress }}%)</span></span>
                                <div class="flex items-center gap-1 shrink-0">
                                    <button type="button" wire:click="$set('decisions.{{ $task->id }}', 'carry')"
                                        @class([
                                            'px-2.5 py-1 rounded-md text-xs transition',
                                            'bg-primary text-white dark:bg-primary-dark' => ($decisions[$task->id] ?? 'carry') === 'carry',
                                            'bg-surface-light dark:bg-surface-dark text-ink-soft dark:text-ink-dark-soft' => ($decisions[$task->id] ?? 'carry') !== 'carry',
                                        ])>ترحيل لبكرة</button>
                                    <button type="button" wire:click="$set('decisions.{{ $task->id }}', 'pool')"
                                        @class([
                                            'px-2.5 py-1 rounded-md text-xs transition',
                                            'bg-warning text-white' => ($decisions[$task->id] ?? '') === 'pool',
                                            'bg-surface-light dark:bg-surface-dark text-ink-soft dark:text-ink-dark-soft' => ($decisions[$task->id] ?? 'carry') !== 'pool',
                                        ])>مؤجّلات</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-success text-white text-sm font-medium hover:opacity-90 transition">تأكيد التقفيل</button>
            </div>
        </form>
    </div>
</div>
