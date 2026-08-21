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
            {{ $form->dream ? 'تعديل الحلم' : '✨ حلم جديد' }}
        </h2>

        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-[auto_1fr] gap-3 items-end">
                <div>
                    <x-input-label for="rd_icon" value="أيقونة" />
                    <x-text-input id="rd_icon" wire:model="form.icon" type="text" class="mt-1 block w-16 text-center text-lg" maxlength="10" />
                </div>
                <div>
                    <x-input-label for="rd_title" value="الحلم" />
                    <x-text-input id="rd_title" wire:model="form.title" type="text" class="mt-1 block w-full" placeholder="مثال: أبقى قدوة لأولادي" />
                </div>
            </div>
            <x-input-error :messages="$errors->get('form.icon')" class="-mt-2" />
            <x-input-error :messages="$errors->get('form.title')" class="-mt-2" />

            <div>
                <x-input-label for="rd_recovery" value="فترة التعافي (اختياري)" />
                <select id="rd_recovery" wire:model="form.recovery_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                    <option value="">بدون ربط بفترة</option>
                    @foreach ($recoveries as $recovery)
                        <option value="{{ $recovery->id }}">{{ $recovery->title }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('form.recovery_id')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="rd_benefits" value="فوايده — سطر لكل فايدة" />
                <textarea id="rd_benefits" wire:model="form.benefitsInput" rows="4" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="ثقة أكبر بنفسي&#10;وقت أكتر لأهلي&#10;راحة بال حقيقية"></textarea>
                <x-input-error :messages="$errors->get('form.benefitsInput')" class="mt-1" />
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
            </div>
        </form>
    </div>
</div>
