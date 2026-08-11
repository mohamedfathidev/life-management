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
            {{ $form->log ? 'تعديل السجل' : 'سجل اليوم' }}
        </h2>

        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                {{-- Date --}}
                <div>
                    <x-input-label for="log_date" value="التاريخ" />
                    <x-text-input id="log_date" wire:model="form.date" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.date')" class="mt-1" />
                </div>

                {{-- Module type --}}
                <div>
                    <x-input-label for="module_type" value="الوحدة" />
                    <select id="module_type" wire:model="form.module_type" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        @foreach ($modules as $module)
                            <option value="{{ $module->value }}">{{ $module->label() }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('form.module_type')" class="mt-1" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Mood --}}
                <div>
                    <x-input-label for="mood" value="المزاج (1–10)" />
                    <x-text-input id="mood" wire:model="form.mood" type="number" min="1" max="10" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.mood')" class="mt-1" />
                </div>

                {{-- Difficulty --}}
                <div>
                    <x-input-label for="difficulty" value="الصعوبة (1–10)" />
                    <x-text-input id="difficulty" wire:model="form.difficulty" type="number" min="1" max="10" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.difficulty')" class="mt-1" />
                </div>
            </div>

            {{-- Note --}}
            <div>
                <x-input-label for="log_note" value="ملاحظة اليوم (اختياري)" />
                <textarea id="log_note" wire:model="form.note" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
                <x-input-error :messages="$errors->get('form.note')" class="mt-1" />
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
            </div>
        </form>
    </div>
</div>
