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
            {{ $form->activity ? 'تعديل النشاط' : 'نشاط / مشاركة جديدة' }}
        </h2>

        <form wire:submit="save" class="space-y-4">
            <div>
                <x-input-label for="ac_title" value="اسم النشاط / المسابقة" />
                <x-text-input id="ac_title" wire:model="form.title" type="text" class="mt-1 block w-full" placeholder="مثال: هاكاثون التحول الرقمي" />
                <x-input-error :messages="$errors->get('form.title')" class="mt-1" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="ac_type" value="النوع" />
                    <select id="ac_type" wire:model="form.type" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        @foreach ($types as $type)
                            <option value="{{ $type->value }}">{{ $type->emoji() }} {{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="ac_stage" value="المرحلة" />
                    <select id="ac_stage" wire:model.live="form.stage" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        @foreach ($stages as $stage)
                            <option value="{{ $stage->value }}">{{ $stage->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="ac_org" value="الجهة المنظمة (اختياري)" />
                    <x-text-input id="ac_org" wire:model="form.organizer" type="text" class="mt-1 block w-full" placeholder="Google / جامعة…" />
                </div>
                <div>
                    <x-input-label for="ac_loc" value="المكان (اختياري)" />
                    <x-text-input id="ac_loc" wire:model="form.location" type="text" class="mt-1 block w-full" placeholder="أونلاين / القاهرة…" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="ac_start" value="من تاريخ (اختياري)" />
                    <x-text-input id="ac_start" wire:model="form.start_date" type="date" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="ac_end" value="إلى تاريخ (اختياري)" />
                    <x-text-input id="ac_end" wire:model="form.end_date" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.end_date')" class="mt-1" />
                </div>
            </div>

            <div>
                <x-input-label for="ac_url" value="الرابط (اختياري)" />
                <x-text-input id="ac_url" wire:model="form.url" type="url" class="mt-1 block w-full" placeholder="https://…" dir="ltr" />
                <x-input-error :messages="$errors->get('form.url')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="ac_goal" value="مربوط بهدف (اختياري)" />
                <select id="ac_goal" wire:model="form.goal_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                    <option value="">— بدون —</option>
                    @foreach ($goals as $goal)
                        <option value="{{ $goal->id }}">{{ $goal->parent_id ? '— ' : '' }}{{ $goal->title }}</option>
                    @endforeach
                </select>
            </div>

            @if (in_array($form->stage, ['accepted', 'done'], true))
                <div>
                    <x-input-label for="ac_result" value="النتيجة / الإنجاز (اختياري)" />
                    <x-text-input id="ac_result" wire:model="form.result" type="text" class="mt-1 block w-full" placeholder="المركز الأول / شهادة حضور…" />
                    <x-input-error :messages="$errors->get('form.result')" class="mt-1" />
                </div>
            @endif

            <div>
                <x-input-label for="ac_notes" value="ملاحظات (اختياري)" />
                <textarea id="ac_notes" wire:model="form.notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
            </div>
        </form>
    </div>
</div>
