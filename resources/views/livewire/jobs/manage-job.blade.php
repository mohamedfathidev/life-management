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
            {{ $form->job ? 'تعديل الوظيفة' : 'وظيفة جديدة' }}
        </h2>

        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="jb_pos" value="المسمى الوظيفي" />
                    <x-text-input id="jb_pos" wire:model="form.position" type="text" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.position')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="jb_comp" value="الشركة" />
                    <x-text-input id="jb_comp" wire:model="form.company" type="text" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.company')" class="mt-1" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="jb_via" value="قدّمت عن طريق" />
                    <x-text-input id="jb_via" wire:model="form.applied_via" type="text" class="mt-1 block w-full" placeholder="LinkedIn / Wuzzuf / معرفة…" />
                </div>
                <div>
                    <x-input-label for="jb_on" value="تاريخ التقديم" />
                    <x-text-input id="jb_on" wire:model="form.applied_on" type="date" class="mt-1 block w-full" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="jb_deadline" value="آخر موعد تقديم (اختياري)" />
                    <x-text-input id="jb_deadline" wire:model="form.deadline" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.deadline')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="jb_interview" value="يوم الإنترفيو (اختياري)" />
                    <x-text-input id="jb_interview" wire:model="form.interview_at" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.interview_at')" class="mt-1" />
                </div>
            </div>

            <div>
                <x-input-label for="jb_url" value="رابط الإعلان (اختياري)" />
                <x-text-input id="jb_url" wire:model="form.url" type="url" class="mt-1 block w-full" placeholder="https://…" dir="ltr" />
                <x-input-error :messages="$errors->get('form.url')" class="mt-1" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="jb_stage" value="المرحلة" />
                    <select id="jb_stage" wire:model.live="form.stage" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        @foreach ($stages as $stage)
                            <option value="{{ $stage->value }}">{{ $stage->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="jb_goal" value="مربوط بهدف (اختياري)" />
                    <select id="jb_goal" wire:model="form.goal_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        <option value="">— بدون —</option>
                        @foreach ($goals as $goal)
                            <option value="{{ $goal->id }}">{{ $goal->parent_id ? '— ' : '' }}{{ $goal->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if ($form->stage === 'rejected')
                <div>
                    <x-input-label for="jb_reason" value="سبب الرفض (من الإيميل)" />
                    <textarea id="jb_reason" wire:model="form.rejection_reason" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
                </div>
            @endif

            <div>
                <x-input-label for="jb_desc" value="وصف الوظيفة (اختياري)" />
                <textarea id="jb_desc" wire:model="form.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
            </div>
        </form>
    </div>
</div>
