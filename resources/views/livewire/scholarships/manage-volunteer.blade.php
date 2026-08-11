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
            {{ $form->activity ? 'تعديل النشاط' : 'نشاط / تقديم تطوّع' }}
        </h2>

        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="vol_title" value="النشاط" />
                    <x-text-input id="vol_title" wire:model="form.title" type="text" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.title')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="vol_org" value="الجهة (اختياري)" />
                    <x-text-input id="vol_org" wire:model="form.organization" type="text" class="mt-1 block w-full" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="vol_via" value="قدّمت من خلال" />
                    <x-text-input id="vol_via" wire:model="form.applied_via" type="text" class="mt-1 block w-full" placeholder="الموقع / معرفة / إعلان…" />
                </div>
                <div>
                    <x-input-label for="vol_stage" value="المرحلة" />
                    <select id="vol_stage" wire:model.live="form.stage" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        @foreach (\App\Enums\ScholarshipStage::cases() as $stage)
                            <option value="{{ $stage->value }}">{{ $stage->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <x-input-label for="vol_start" value="من" />
                    <x-text-input id="vol_start" wire:model="form.start_date" type="date" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="vol_end" value="إلى" />
                    <x-text-input id="vol_end" wire:model="form.end_date" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.end_date')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="vol_hours" value="ساعات" />
                    <x-text-input id="vol_hours" wire:model="form.hours" type="number" min="0" class="mt-1 block w-full" />
                </div>
            </div>

            @if ($form->stage === 'rejected')
                <div>
                    <x-input-label for="vol_reason" value="سبب الرفض" />
                    <textarea id="vol_reason" wire:model="form.rejection_reason" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
                </div>
            @endif

            <div>
                <x-input-label for="vol_desc" value="وصف (اختياري)" />
                <textarea id="vol_desc" wire:model="form.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
            </div>
        </form>
    </div>
</div>
