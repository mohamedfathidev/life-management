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
            {{ $form->recovery ? 'تعديل التعافي' : 'تعافٍ جديد' }}
        </h2>

        <form wire:submit="save" class="space-y-4">
            <div>
                <x-input-label for="rec_title" value="العنوان" />
                <x-text-input id="rec_title" wire:model="form.title" type="text" class="mt-1 block w-full" placeholder="مثال: تعافٍ من التدخين" />
                <x-input-error :messages="$errors->get('form.title')" class="mt-1" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="rec_start" value="تاريخ بداية الفترة" />
                    <x-text-input id="rec_start" wire:model="form.start_date" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.start_date')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="rec_end" value="تاريخ نهاية الفترة (اختياري)" />
                    <x-text-input id="rec_end" wire:model="form.end_date" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.end_date')" class="mt-1" />
                </div>
            </div>

            <div>
                <x-input-label for="rec_status" value="الحالة" />
                <select id="rec_status" wire:model="form.status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="rec_goal" value="مربوط بهدف (اختياري)" />
                <select id="rec_goal" wire:model="form.goal_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                    <option value="">— بدون هدف —</option>
                    @foreach ($goals as $goal)
                        <option value="{{ $goal->id }}">{{ $goal->parent_id ? '— ' : '' }}{{ $goal->title }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="rec_desc" value="وصف (اختياري)" />
                <textarea id="rec_desc" wire:model="form.description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
            </div>
        </form>
    </div>
</div>
