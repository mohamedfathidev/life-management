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
            {{ $form->track ? 'تعديل المسار' : 'مسار مذاكرة جديد' }}
        </h2>

        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="st_title" value="العنوان" />
                    <x-text-input id="st_title" wire:model="form.title" type="text" class="mt-1 block w-full" placeholder="مثال: أساسيات هندسة البرمجيات" />
                    <x-input-error :messages="$errors->get('form.title')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="st_field" value="المجال" />
                    <x-text-input id="st_field" wire:model="form.field" type="text" class="mt-1 block w-full" placeholder="سوفتوير / مانجمنت / تدريس…" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="st_start" value="أبدأ" />
                    <x-text-input id="st_start" wire:model="form.start_date" type="date" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="st_end" value="أخلص" />
                    <x-text-input id="st_end" wire:model="form.end_date" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.end_date')" class="mt-1" />
                </div>
            </div>

            <div>
                <x-input-label for="st_plan" value="الخطة (بذاكر إيه)" />
                <textarea id="st_plan" wire:model="form.plan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
            </div>

            <div>
                <x-input-label for="st_res" value="المصادر" />
                <textarea id="st_res" wire:model="form.resources" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="كورسات، كتب، روابط…"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="st_target" value="التارجت" />
                    <x-text-input id="st_target" wire:model="form.target" type="text" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="st_cert" value="شهادة (لو فيه)" />
                    <x-text-input id="st_cert" wire:model="form.certificate" type="text" class="mt-1 block w-full" />
                </div>
            </div>

            <label class="inline-flex items-center gap-2 text-sm text-ink dark:text-ink-dark">
                <input type="checkbox" wire:model="form.is_completed" class="rounded border-gray-300 text-success focus:ring-success" />
                تم إكماله
            </label>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
            </div>
        </form>
    </div>
</div>
