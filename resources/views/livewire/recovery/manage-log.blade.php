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
            {{ $form->log ? 'تعديل السجل' : 'سجل جديد' }}
        </h2>

        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="rl_date" value="التاريخ" />
                    <x-text-input id="rl_date" wire:model="form.date" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.date')" class="mt-1" />
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center gap-2 text-sm text-ink dark:text-ink-dark">
                        <input type="checkbox" wire:model.live="form.is_setback" class="rounded border-gray-300 text-danger focus:ring-danger" />
                        سجّل كانتكاسة
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="rl_sleep_location" value="نمت فين (اختياري)" />
                    <select id="rl_sleep_location" wire:model="form.sleep_location" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        <option value="">—</option>
                        <option value="home">في البيت</option>
                        <option value="outside">برا</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="rl_sleep_spot" value="نمت على إيه (اختياري)" />
                    <select id="rl_sleep_spot" wire:model="form.sleep_spot" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        <option value="">—</option>
                        <option value="bed">على السرير</option>
                        <option value="elsewhere">نومة تانية</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="rl_urge" value="شدّة الرغبة (١–١٠)" />
                    <x-text-input id="rl_urge" wire:model="form.urge_level" type="number" min="1" max="10" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.urge_level')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="rl_mood" value="المزاج (١–١٠)" />
                    <x-text-input id="rl_mood" wire:model="form.mood" type="number" min="1" max="10" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.mood')" class="mt-1" />
                </div>
            </div>

            <div>
                <x-input-label value="أصعب فترة في اليوم (اختياري)" />
                <div class="grid grid-cols-2 gap-4 mt-1">
                    <div>
                        <input type="time" wire:model="form.hardest_from" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" dir="ltr" placeholder="من" />
                        <x-input-error :messages="$errors->get('form.hardest_from')" class="mt-1" />
                    </div>
                    <div>
                        <input type="time" wire:model="form.hardest_to" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" dir="ltr" placeholder="إلى" />
                        <x-input-error :messages="$errors->get('form.hardest_to')" class="mt-1" />
                    </div>
                </div>
            </div>

            <div>
                <x-input-label for="rl_trigger" value="المُحفّز (اختياري)" />
                <x-text-input id="rl_trigger" wire:model="form.trigger_note" type="text" class="mt-1 block w-full" placeholder="إيه اللي أثار الرغبة؟" />
            </div>

            @if ($form->is_setback)
                <div>
                    <x-input-label value="ازاي كنت اقدر اتجنب السقوط ده؟ (اختياري)" class="mb-2" />
                    <div class="space-y-2">
                        @foreach ($form->avoidance_reasons as $i => $reason)
                            <div class="flex items-center gap-2">
                                <span class="text-ink-soft dark:text-ink-dark-soft">•</span>
                                <input type="text" wire:model="form.avoidance_reasons.{{ $i }}" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="سبب..." />
                                <button type="button" wire:click="removeAvoidanceReason({{ $i }})" class="text-ink-soft dark:text-ink-dark-soft hover:text-danger px-1">×</button>
                            </div>
                        @endforeach
                        <button type="button" wire:click="addAvoidanceReason" class="text-xs text-primary dark:text-primary-dark hover:underline">+ إضافة سبب</button>
                    </div>
                    <x-input-error :messages="$errors->get('form.avoidance_reasons.*')" class="mt-1" />
                </div>
            @else
                <div>
                    <x-input-label value="عملت ايه عشان مقعتش ضحية التخيلات والأفكار دي؟ (اختياري)" class="mb-2" />
                    <div class="space-y-2">
                        @foreach ($form->protection_actions as $i => $action)
                            <div class="flex items-center gap-2">
                                <span class="text-ink-soft dark:text-ink-dark-soft">•</span>
                                <input type="text" wire:model="form.protection_actions.{{ $i }}" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="خطوة..." />
                                <button type="button" wire:click="removeProtectionAction({{ $i }})" class="text-ink-soft dark:text-ink-dark-soft hover:text-danger px-1">×</button>
                            </div>
                        @endforeach
                        <button type="button" wire:click="addProtectionAction" class="text-xs text-primary dark:text-primary-dark hover:underline">+ إضافة خطوة</button>
                    </div>
                    <x-input-error :messages="$errors->get('form.protection_actions.*')" class="mt-1" />
                </div>
            @endif

            <div>
                <x-input-label value="ليلة اليوم (اختياري)" class="mb-2" />
                <div class="space-y-2 text-sm">
                    <label class="inline-flex items-center gap-2 text-ink dark:text-ink-dark cursor-pointer">
                        <input type="checkbox" wire:model="form.stayed_up_late" class="rounded border-gray-300 text-primary focus:ring-primary" />
                        سهرت 🌙
                    </label>
                    <br>
                    <label class="inline-flex items-center gap-2 text-ink dark:text-ink-dark cursor-pointer">
                        <input type="checkbox" wire:model="form.had_dinner" class="rounded border-gray-300 text-primary focus:ring-primary" />
                        اتغذيت 🍽️
                    </label>
                    <br>
                    <label class="inline-flex items-center gap-2 text-ink dark:text-ink-dark cursor-pointer">
                        <input type="checkbox" wire:model="form.prepared_for_sleep" class="rounded border-gray-300 text-primary focus:ring-primary" />
                        استعديت للنوم 🛏️
                    </label>
                </div>
            </div>

            <div>
                <x-input-label for="rl_note" value="كلمتين لنفسي (اختياري)" />
                <textarea id="rl_note" wire:model="form.note" rows="3" placeholder="اكتب كلمتين لنفسك هنا…" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
            </div>
        </form>
    </div>
</div>
