<div
    x-data="{ open: @entangle('open') }"
    x-show="open"
    x-cloak
    @keydown.escape.window="open && $wire.close()"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/40" wire:click="close"></div>

    <div x-show="open" x-transition class="relative w-full max-w-lg rounded-2xl bg-surface-light dark:bg-surface-dark shadow-xl p-6 max-h-[90vh] overflow-y-auto">
        <h2 class="text-lg font-semibold text-ink dark:text-ink-dark mb-1">إغلاق الهدف</h2>
        <p class="text-sm text-ink-soft dark:text-ink-dark-soft mb-4">راجع تجربتك مع الهدف عشان تتحسّن في اللي جاي بإذن الله.</p>

        <form wire:submit="save" class="space-y-5">
            {{-- Shortcomings + strengths in one section --}}
            <div class="rounded-xl border border-ink-soft/15 dark:border-ink-dark-soft/15 p-4 space-y-4">
                {{-- Shortcomings --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-danger">التقصيرات</span>
                        <button type="button" wire:click="addShortcoming" class="text-xs text-primary dark:text-primary-dark hover:underline">+ إضافة</button>
                    </div>
                    <div class="space-y-2">
                        @foreach ($shortcomings as $i => $item)
                            <div wire:key="short-{{ $i }}" class="flex items-center gap-2">
                                <input type="text" wire:model="shortcomings.{{ $i }}" class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="مثال: كنت بأجّل كتير" />
                                <button type="button" wire:click="removeShortcoming({{ $i }})" class="text-danger text-sm shrink-0">✕</button>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Strengths --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-success">المميزات</span>
                        <button type="button" wire:click="addStrength" class="text-xs text-primary dark:text-primary-dark hover:underline">+ إضافة</button>
                    </div>
                    <div class="space-y-2">
                        @foreach ($strengths as $i => $item)
                            <div wire:key="strength-{{ $i }}" class="flex items-center gap-2">
                                <input type="text" wire:model="strengths.{{ $i }}" class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="مثال: التزمت بالمواعيد" />
                                <button type="button" wire:click="removeStrength({{ $i }})" class="text-danger text-sm shrink-0">✕</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Improvement percent --}}
            <div x-data="{ p: @entangle('improvement') }">
                <div class="flex justify-between">
                    <x-input-label value="نسبة التحسن في هذا الهدف" />
                    <span class="text-sm font-semibold text-primary dark:text-primary-dark" x-text="p + '%'"></span>
                </div>
                <input type="range" min="0" max="100" step="5" x-model.number="p" class="mt-2 block w-full accent-primary" dir="ltr" />
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">قيّم أداءك في هذا الهدف من ٠ إلى ١٠٠. المقارنة بالهدف السابق تُحسب تلقائيًا في صفحة الإحصائيات.</p>
                <x-input-error :messages="$errors->get('improvement')" class="mt-1" />
            </div>

            {{-- Optional note --}}
            <div>
                <x-input-label for="goal_note" value="ملاحظة عامة (اختياري)" />
                <textarea id="goal_note" wire:model="note" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-success text-white text-sm font-medium hover:opacity-90 transition">إغلاق الهدف</button>
            </div>
        </form>
    </div>
</div>
