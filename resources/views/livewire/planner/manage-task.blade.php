<div
    x-data="{ open: @entangle('open') }"
    x-show="open"
    x-cloak
    @keydown.escape.window="open && $wire.close()"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/40" wire:click="close"></div>

    <div x-show="open" x-transition class="relative w-full max-w-lg rounded-2xl bg-surface-light dark:bg-surface-dark shadow-xl p-6 max-h-[90vh] overflow-y-auto">
        <h2 class="text-lg font-semibold text-ink dark:text-ink-dark mb-4">
            {{ $form->task ? 'تعديل التاسك' : 'تاسك جديد' }}
        </h2>

        <form wire:submit="save" class="space-y-4">
            {{-- Title --}}
            <div>
                <x-input-label for="task_title" value="عنوان التاسك" />
                <x-text-input id="task_title" wire:model="form.title" type="text" class="mt-1 block w-full" placeholder="مثال: مذاكرة فصل ٣، مشوار البنك…" />
                <x-input-error :messages="$errors->get('form.title')" class="mt-1" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Goal link (optional) --}}
                <div>
                    <x-input-label for="task_goal" value="مربوط بهدف (اختياري)" />
                    <select id="task_goal" wire:model="form.goal_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        <option value="">— بدون هدف —</option>
                        @foreach ($goals as $goal)
                            <option value="{{ $goal->id }}">{{ $goal->parent ? $goal->parent->title.' › ' : '' }}{{ $goal->title }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('form.goal_id')" class="mt-1" />
                </div>

                {{-- Kind --}}
                <div>
                    <x-input-label for="task_kind" value="النوع" />
                    <select id="task_kind" wire:model="form.kind" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        @foreach ($kinds as $kind)
                            <option value="{{ $kind->value }}">{{ $kind->emoji() }} {{ $kind->label() }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('form.kind')" class="mt-1" />
                </div>
            </div>

            {{-- Progress --}}
            <div x-data="{ p: @entangle('form.progress') }">
                <div class="flex justify-between">
                    <x-input-label value="نسبة الإنجاز" />
                    <span class="text-sm text-ink-soft dark:text-ink-dark-soft" x-text="p + '%'"></span>
                </div>
                <input type="range" min="0" max="100" step="5" x-model.number="p" class="mt-2 block w-full accent-primary" dir="ltr" />
                <x-input-error :messages="$errors->get('form.progress')" class="mt-1" />
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
            </div>
        </form>
    </div>
</div>
