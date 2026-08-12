<div
    x-data="{ open: @entangle('open') }"
    x-show="open"
    x-cloak
    @keydown.escape.window="open && $wire.close()"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/40" wire:click="close"></div>

    <div x-show="open" x-transition class="relative w-full max-w-lg rounded-2xl bg-surface-light dark:bg-surface-dark shadow-xl p-6 max-h-[90vh] overflow-y-auto">
        <h2 class="text-lg font-semibold text-ink dark:text-ink-dark mb-4">{{ $form->dream ? 'تعديل الحلم' : 'حلم جديد' }}</h2>

        <form wire:submit="save" class="space-y-4">
            <div>
                <x-input-label for="dr_title" value="الحلم" />
                <x-text-input id="dr_title" wire:model="form.title" type="text" class="mt-1 block w-full" placeholder="مثال: أسافر أوروبا" />
                <x-input-error :messages="$errors->get('form.title')" class="mt-1" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="dr_from" value="أنا واقف فين" />
                    <x-text-input id="dr_from" wire:model="form.from_point" type="text" class="mt-1 block w-full" placeholder="نقطة البداية" />
                </div>
                <div>
                    <x-input-label for="dr_to" value="عايز أوصل فين" />
                    <x-text-input id="dr_to" wire:model="form.to_point" type="text" class="mt-1 block w-full" placeholder="الوجهة" />
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <x-input-label for="dr_dv" value="المدة" />
                    <x-text-input id="dr_dv" wire:model="form.duration_value" type="number" min="1" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="dr_du" value="الوحدة" />
                    <select id="dr_du" wire:model="form.duration_unit" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        @foreach ($units as $unit)
                            <option value="{{ $unit->value }}">{{ $unit->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="dr_status" value="الحالة" />
                    <select id="dr_status" wire:model="form.status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="dr_target" value="تاريخ مستهدف (اختياري)" />
                    <x-text-input id="dr_target" wire:model="form.target_date" type="date" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="dr_color" value="اللون" />
                    <input id="dr_color" wire:model="form.color" type="color" class="mt-1 block w-full h-10 rounded-md border border-gray-300 dark:border-gray-600 bg-transparent cursor-pointer" />
                </div>
            </div>

            <div>
                <x-input-label for="dr_why" value="ليه بحلم بيه؟ (اختياري)" />
                <textarea id="dr_why" wire:model="form.why" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
            </div>

            <div>
                <x-input-label for="dr_desc" value="وصف (اختياري)" />
                <textarea id="dr_desc" wire:model="form.description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
            </div>
        </form>
    </div>
</div>
