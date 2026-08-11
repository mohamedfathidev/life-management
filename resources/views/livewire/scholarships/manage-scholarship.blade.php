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
            {{ $form->scholarship ? 'تعديل المنحة' : 'منحة جديدة' }}
        </h2>

        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="sc_name" value="اسم المنحة" />
                    <x-text-input id="sc_name" wire:model="form.name" type="text" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.name')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="sc_inst" value="الجهة (اختياري)" />
                    <x-text-input id="sc_inst" wire:model="form.institution" type="text" class="mt-1 block w-full" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="sc_from" value="بداية التقديم" />
                    <x-text-input id="sc_from" wire:model="form.apply_from" type="date" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="sc_to" value="آخر موعد" />
                    <x-text-input id="sc_to" wire:model="form.apply_to" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.apply_to')" class="mt-1" />
                </div>
            </div>

            <div>
                <x-input-label for="sc_req" value="الشروط / المطلوب (اختياري)" />
                <textarea id="sc_req" wire:model="form.requirements" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
            </div>

            {{-- Stage + related dates --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="sc_stage" value="المرحلة" />
                    <select id="sc_stage" wire:model.live="form.stage" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        @foreach ($stages as $stage)
                            <option value="{{ $stage->value }}">{{ $stage->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="sc_submitted" value="تاريخ تقديم الأوراق" />
                    <x-text-input id="sc_submitted" wire:model="form.submitted_on" type="date" class="mt-1 block w-full" />
                </div>
            </div>

            @if (in_array($form->stage, ['accepted', 'rejected']))
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="sc_decided" value="تاريخ القرار" />
                        <x-text-input id="sc_decided" wire:model="form.decided_on" type="date" class="mt-1 block w-full" />
                    </div>
                </div>
            @endif

            @if ($form->stage === 'rejected')
                <div>
                    <x-input-label for="sc_reason" value="سبب الرفض (من الإيميل)" />
                    <textarea id="sc_reason" wire:model="form.rejection_reason" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
                </div>
            @endif

            <div>
                <x-input-label for="sc_notes" value="ملاحظات (اختياري)" />
                <textarea id="sc_notes" wire:model="form.notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
            </div>
        </form>
    </div>
</div>
