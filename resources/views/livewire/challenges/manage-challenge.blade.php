<div
    x-data="{ open: @entangle('open') }"
    x-show="open"
    x-cloak
    @keydown.escape.window="open && $wire.close()"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/40" wire:click="close"></div>

    <div x-show="open" x-transition class="relative w-full max-w-lg rounded-2xl bg-surface-light dark:bg-surface-dark shadow-xl p-6 max-h-[90vh] overflow-y-auto">
        <h2 class="text-lg font-semibold text-ink dark:text-ink-dark mb-4">{{ $form->challenge ? 'تعديل التحدي' : 'تحدٍّ جديد' }}</h2>

        <form wire:submit="save" class="space-y-4">
            <div>
                <x-input-label for="cl_title" value="العنوان" />
                <x-text-input id="cl_title" wire:model="form.title" type="text" class="mt-1 block w-full" placeholder="مثال: ٣٠ يوم بدون سكر" />
                <x-input-error :messages="$errors->get('form.title')" class="mt-1" />
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <x-input-label for="cl_start" value="تاريخ البداية" />
                    <x-text-input id="cl_start" wire:model="form.start_date" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.start_date')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="cl_dur" value="المدة (أيام)" />
                    <x-text-input id="cl_dur" wire:model="form.duration_days" type="number" min="1" max="365" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.duration_days')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="cl_color" value="اللون" />
                    <input id="cl_color" wire:model="form.color" type="color" class="mt-1 block w-full h-10 rounded-md border border-gray-300 dark:border-gray-600 bg-transparent cursor-pointer" />
                </div>
            </div>

            @if ($form->challenge)
                <div>
                    <x-input-label for="cl_status" value="الحالة" />
                    <select id="cl_status" wire:model="form.status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <x-input-label for="cl_desc" value="الوصف (اختياري)" />
                <textarea id="cl_desc" wire:model="form.description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
            </div>
        </form>
    </div>
</div>
